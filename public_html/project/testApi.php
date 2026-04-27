<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["anime"])) {
    $data = ["q" => $_GET["anime"]];
    $endpoint = "https://anime-data-scraper-api.p.rapidapi.com/v1/anime/popular";
    $isRapidAPI = true;
    $rapidAPIHost = "anime-data-scraper-api.p.rapidapi.com";
    $result = get($endpoint, "ANIME_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
}
?>
<div class="container-fluid">
    <h1>Anime Stats</h1>
    <p>Find out info on your favorite animes!</p>
    <form>
        <div>
            <label>Anime Name</label>
            <input name="anime" />
            <input type="submit" value="Fetch Stats" />
        </div>
    </form>
    <div class="row">
        <?php if (!empty($result)) : ?>
            <pre><?php var_export($result); ?></pre>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");
//gec23 4/20