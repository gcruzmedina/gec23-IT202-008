<?php
require(__DIR__ . "/../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}

$allowed_columns = [
    "title",
    "rarity",
    "power",
    "episodes",
    "score",
    "created"
];

$sort = ["asc", "desc"];

$params = [];

/*
    STEP 1:
    GET USER'S ANIME IDS
*/

$query = "
SELECT a.id
FROM `AnimeCards` a
JOIN `UserAnimeCards` ua
    ON ua.anime_id = a.id
WHERE 1=1
";

$query .= " AND user_id = :user_id";

$params[":user_id"] = get_user_id();

/*
    FILTERS
*/

if (count($_GET) > 0) {

    $title = se($_GET, "title", "", false);

    if (!empty($title)) {
        $query .= " AND title LIKE :title";
        $params[":title"] = "%$title%";
    }

    $rarity = se($_GET, "rarity", "", false);

    if (is_numeric($rarity)) {
        $query .= " AND rarity = :rarity";
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

    $query .= " ORDER BY a.$column $order";
}

/*
    LIMIT
*/

$limit = se($_GET, "limit", 10, false);

if (!empty($limit) && is_numeric($limit)) {

    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }

    $query .= " LIMIT :limit";

    $params[":limit"] = $limit;
}

/*
    EXECUTE QUERY
*/

$db = getDB();

$stmt = $db->prepare($query);

foreach ($params as $key => $val) {

    $type = match (true) {
        is_numeric($val) => PDO::PARAM_INT,
        is_bool($val) => PDO::PARAM_BOOL,
        is_null($val) => PDO::PARAM_NULL,
        default => PDO::PARAM_STR,
    };

    $stmt->bindValue($key, $val, $type);
}

$anime_ids = [];

try {

    $stmt->execute();

    $r = $stmt->fetchAll();

    if ($r) {

        $anime_ids = array_map(
            fn($row) => $row["id"],
            $r
        );
    }

} catch (PDOException $e) {

    error_log("Error fetching anime: " . var_export($e, true));

    flash("Unhandled error occurred", "danger");
}

/*
    STEP 2:
    FETCH FULL ANIME DATA
*/

$results = [];

if ($anime_ids) {

    $in = str_repeat('?,', count($anime_ids) - 1) . '?';

    $query = "
    SELECT
        a.id,
        title,
        rarity,
        power,
        episodes,
        score,
        status,
        image_url
    FROM `AnimeCards` a
    WHERE a.id IN ($in)
    ";

    $stmt = $db->prepare($query);

    $stmt->execute($anime_ids);

    $results = $stmt->fetchAll();
}

/*
    FILTER FORM
*/

$cols = array_map(
    fn($col) => [$col => $col],
    $allowed_columns
);

array_unshift($cols, ["" => "Select Column"]);

$order = array_map(
    fn($dir) => [$dir => $dir],
    $sort
);

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
        "rules" => [
            "min" => 0,
            "max" => 5
        ]
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
        "rules" => [
            "min" => 1,
            "max" => 100
        ]
    ]
];
?>

<div class="container-fluid">

    <h1>My Anime Cards</h1>

    <form>

        <div class="row">

            <?php foreach ($form as $field): ?>

                <div class="col">

                    <?php render_input($field); ?>

                </div>

            <?php endforeach; ?>

        </div>

        <?php render_button([
            "text" => "Search",
            "type" => "submit"
        ]); ?>

        <a href="?" class="btn btn-secondary">
            Reset
        </a>

    </form>

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

                            <p>
                                Episodes:
                                <?php echo $entry["episodes"]; ?>
                            </p>

                            <p>
                                Score:
                                <?php echo $entry["score"]; ?>
                            </p>

                            <p>
                                Status:
                                <?php echo $entry["status"]; ?>
                            </p>

                            <p>
                                Rarity:
                                <?php echo $entry["rarity"]; ?>
                            </p>

                            <p>
                                Power:
                                <?php echo $entry["power"]; ?>
                            </p>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php require(__DIR__ . "/../../partials/footer.php"); ?>