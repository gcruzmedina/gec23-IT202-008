<?php
require(__DIR__ . "/../../partials/nav.php");

$results = [];

// Handle API request
if (isset($_GET["anime"]) && !empty($_GET["anime"])) {
    $anime = urlencode($_GET["anime"]);
    $limit = se($_GET, "limit", 10, false);

    if (!is_numeric($limit) || $limit < 1 || $limit > 25) {
        $limit = 10;
    }

    $url = "https://api.jikan.moe/v4/anime?q=$anime&limit=$limit";

    try {
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data["data"])) {
            $results = $data["data"];
        }
    } catch (Exception $e) {
        error_log("Anime API error: " . var_export($e, true));
        flash("Error fetching anime data", "danger");
    }
}

// Form fields
$form = [
    [
        "type" => "text",
        "id" => "anime",
        "name" => "anime",
        "label" => "Search Anime",
        "value"=> se($_GET, "anime", "", false),
    ],
    [
        "type"=>"number",
        "id"=>"limit",
        "name"=>"limit",
        "label"=>"Limit",
        "value"=>se($_GET, "limit", "10", false),
        "rules"=>["min"=>1, "max"=>25]
    ]
];
?>

<div class="container-fluid">
    <h1>Anime Search</h1>

    <form>
        <div class="row">
            <?php foreach ($form as $field): ?>
                <div class="col">
                    <?php render_input($field); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
        <a href="?" class="btn btn-secondary">Reset</a>
    </form>

    <hr>

    <?php if (count($results) == 0) : ?>
        <p>No results to show</p>
    <?php else : ?>
        <div class="row">
            <?php foreach ($results as $anime): ?>
                <div class="col-md-3">
                    <div class="card p-2 m-2 h-100">
                        <img 
                            src="<?php echo $anime["images"]["jpg"]["image_url"] ?? ""; ?>" 
                            class="card-img-top"
                            alt="anime image"
                        >
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $anime["title"] ?? "No Title"; ?>
                            </h5>
                            <p>Episodes: <?php echo $anime["episodes"] ?? "N/A"; ?></p>
                            <p>Score: <?php echo $anime["score"] ?? "N/A"; ?></p>
                            <p>Status: <?php echo $anime["status"] ?? "N/A"; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>