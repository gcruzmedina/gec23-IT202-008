<?php
require(__DIR__ . "/../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}

$allowed_columns = [
    "title",
    "episodes",
    "score",
    "status",
    "year"
];

$sort = ["asc", "desc"];

$results = [];

// API Request
if (count($_GET) > 0) {

    $anime = se($_GET, "anime", "", false);
    $limit = se($_GET, "limit", 10, false);
    $column = se($_GET, "column", "", false);
    $order = se($_GET, "order", "", false);

    // Validate limit
    if (!is_numeric($limit) || $limit < 1 || $limit > 25) {
        $limit = 10;
    }

    // Validate column
    if (empty($column) || !in_array($column, $allowed_columns)) {
        $column = "score";
    }

    // Validate order
    if (empty($order) || !in_array($order, $sort)) {
        $order = "desc";
    }

    // Build API URL
    $url = "https://api.jikan.moe/v4/anime?q="
        . urlencode($anime)
        . "&limit=$limit"
        . "&order_by=$column"
        . "&sort=$order";

    error_log("Anime URL: " . $url);

    try {
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data["data"])) {
            $results = $data["data"];
        }
    } catch (Exception $e) {
        error_log("Anime API Error: " . var_export($e, true));
        flash("Error fetching anime data", "danger");
    }
}

// Dropdown Options
$cols = array_map(function ($col) {
    return [$col => ucfirst($col)];
}, $allowed_columns);

array_unshift($cols, ["" => "Select Column"]);

$orderOptions = array_map(function ($o) {
    return [$o => strtoupper($o)];
}, $sort);

array_unshift($orderOptions, ["" => "Select Order"]);

// Form Fields
$form = [
    [
        "type" => "text",
        "id" => "anime",
        "name" => "anime",
        "label" => "Search Anime",
        "value" => se($_GET, "anime", "", false),
    ],
    [
        "type" => "select",
        "id" => "column",
        "name" => "column",
        "label" => "Sort By",
        "options" => $cols,
        "value" => se($_GET, "column", "", false),
    ],
    [
        "type" => "select",
        "id" => "order",
        "name" => "order",
        "label" => "Order",
        "options" => $orderOptions,
        "value" => se($_GET, "order", "", false),
    ],
    [
        "type" => "number",
        "id" => "limit",
        "name" => "limit",
        "label" => "Limit",
        "value" => se($_GET, "limit", "10", false),
        "rules" => ["min" => 1, "max" => 25]
    ]
];
?>

<div class="container-fluid">
    <h1>Anime Search</h1>

    <div>
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
    </div>

    <hr>

    <?php if (count($results) == 0) : ?>
        <p>No results to show</p>

    <?php else : ?>

        <div class="row">

            <?php foreach ($results as $anime): ?>

                <div class="col-md-3 mb-4">

                    <div class="card h-100 p-2">

                        <img
                            src="<?php echo $anime["images"]["jpg"]["image_url"] ?? ""; ?>"
                            class="card-img-top"
                            alt="anime image"
                        >

                        <div class="card-body">

                            <h5 class="card-title">
                                <?php echo $anime["title"] ?? "No Title"; ?>
                            </h5>

                            <p>
                                Episodes:
                                <?php echo $anime["episodes"] ?? "N/A"; ?>
                            </p>

                            <p>
                                Score:
                                <?php echo $anime["score"] ?? "N/A"; ?>
                            </p>

                            <p>
                                Status:
                                <?php echo $anime["status"] ?? "N/A"; ?>
                            </p>

                            <p>
                                Year:
                                <?php echo $anime["year"] ?? "N/A"; ?>
                            </p>

                            <a
                                href="<?php echo get_url('view.php?id=' . $anime['mal_id']); ?>" class="btn btn-primary"
                            >
                                View
                            </a>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>
</div>

<?php require(__DIR__ . "/../../partials/footer.php"); ?>