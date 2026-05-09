<?php
require(__DIR__ . "/../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}

$allowed_columns = ["title", "rarity", "power", "episodes", "score", "created"];
$sort = ["asc", "desc"];

$params = [];

/*
    STEP 1:
    Get anime IDs only
*/

$from = " FROM `AnimeCards` a";
$query = "SELECT a.id";
$count = "SELECT count(a.id) as total";
$count_where = "";

// Only show active anime cards not owned by users
$where = " WHERE 1=1 
    AND a.is_active = 1 
    AND NOT EXISTS (
        SELECT id 
        FROM `UserAnimeCards` ua 
        WHERE ua.anime_id = a.id
    )";

/*
    FILTERING
*/
if (count($_GET) > 0) {

    $title = se($_GET, "title", "", false);

    if (!empty($title)) {
        $where .= " AND title LIKE :title";
        $params[":title"] = "%$title%";
    }

    $rarity = se($_GET, "rarity", "", false);

    if (is_numeric($rarity)) {
        $where .= " AND rarity = :rarity";
        $params[":rarity"] = $rarity;
    }

    $column = se($_GET, "column", "", false);

    if (empty($column) || !in_array($column, $allowed_columns)) {
        $column = "created";
    }

    $order = se($_GET, "order", "", false);

    if (empty($order) || !in_array($order, $sort)) {
        $order = "desc";
    }

    $where .= " ORDER BY a.$column $order";
}

/*
    LIMIT
*/

$limit = se($_GET, "limit", 10, false);

if (!empty($limit) && is_numeric($limit)) {

    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }

    $count_where = $where;

    $where .= " LIMIT :limit";

    $params[":limit"] = (int)$limit;
}

/*
    EXECUTE QUERY
*/

$anime_ids = selectAll("$query $from $where", $params);

if ($anime_ids) {
    $anime_ids = array_map(fn($row) => $row["id"], $anime_ids);
}

$results = [];

/*
    STEP 2:
    Fetch full anime data
*/

if ($anime_ids) {

    $in = str_repeat('?,', count($anime_ids) - 1) . '?';

    $query = "SELECT 
        a.id,
        title,
        rarity,
        power,
        episodes,
        score,
        status,
        image_url
    FROM `AnimeCards` a
    WHERE a.id IN ($in)";

    $results = selectAll($query, $anime_ids);
}

unset($params[":limit"]);

/*
    COUNT QUERY
*/

$count_results = selectAll(
    "$count $from $count_where",
    $params,
    true
);

$count_results = $count_results[0] ?? ["total" => 0];

$result_stats = [
    "current" => count($results),
    "total" => $count_results["total"]
];

/*
    FILTER FORM
*/

$cols = array_map(fn($col) => [$col => $col], $allowed_columns);
array_unshift($cols, ["" => "Select Column"]);

$order = array_map(fn($dir) => [$dir => $dir], $sort);
array_unshift($order, ["" => "Select Order"]);

$form = [
    [
        "type" => "text",
        "id" => "title",
        "name" => "title",
        "label" => "Anime Title",
        "value" => se($_GET, "title", "", false),
    ],
    [
        "type" => "number",
        "id" => "rarity",
        "name" => "rarity",
        "label" => "Rarity",
        "value" => se($_GET, "rarity", "", false),
        "rules" => ["min" => 0, "max" => 5]
    ],
    [
        "type" => "select",
        "id" => "column",
        "name" => "column",
        "label" => "Column",
        "options" => $cols,
        "value" => se($_GET, "column", "", false),
    ],
    [
        "type" => "select",
        "id" => "order",
        "name" => "order",
        "label" => "Order",
        "options" => $order,
        "value" => se($_GET, "order", "", false),
    ],
    [
        "type" => "number",
        "id" => "limit",
        "name" => "limit",
        "label" => "Limit",
        "value" => se($_GET, "limit", "10", false),
        "rules" => ["min" => 1, "max" => 100]
    ]
];
?>

<div class="container-fluid">

    <h1>Available Anime Cards</h1>

    <small>
        These anime cards are not owned by any users.
    </small>

    <form>
        <div class="row">

            <?php foreach ($form as $field): ?>

                <div class="col">
                    <?php render_input($field); ?>
                </div>

            <?php endforeach; ?>

        </div>

        <?php render_button(["text" => "Search", "type" => "submit"]); ?>

        <a href="?" class="btn btn-secondary">
            Reset
        </a>
    </form>

    <?php //results_header($result_stats); ?>

    <?php if (count($results) == 0): ?>

        <p>No anime cards found</p>

    <?php else: ?>

        <div class="row">

            <?php foreach ($results as $entry): ?>

                <div class="col-md-3">

                    <div class="card p-2 m-2 h-100">

                        <img
                            src="<?php echo $entry["image_url"]; ?>"
                            class="card-img-top"
                            alt="anime image"
                        >

                        <div class="card-body">

                            <h5 class="card-title">
                                <?php echo $entry["title"]; ?>
                            </h5>

                            <p>Episodes: <?php echo $entry["episodes"]; ?></p>

                            <p>Score: <?php echo $entry["score"]; ?></p>

                            <p>Status: <?php echo $entry["status"]; ?></p>

                            <p>Rarity: <?php echo $entry["rarity"]; ?></p>

                            <p>Power: <?php echo $entry["power"]; ?></p>

                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php require(__DIR__ . "/../../partials/footer.php"); ?>