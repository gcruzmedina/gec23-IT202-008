<?php
require_once(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    header("Location: " . get_url("landing.php"));
    exit;
}

if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $animeName = trim(se($_POST, "anime", "", false));
    $animeData = [];

    if ($action === "fetch") {
        if ($animeName) {
            $result = fetch_anime($animeName); // your API function
            error_log("Anime API Data: " . var_export($result, true));

            if (!empty($result)) {
                $animeData = [
                    "anime_id" => $result["mal_id"] ?? uniqid(),
                    "title" => $result["title"] ?? "",
                    "type" => $result["type"] ?? "",
                    "status" => $result["status"] ?? "",
                    "episodes" => $result["episodes"] ?? null,
                    "score" => $result["score"] ?? null,
                    "image_url" => $result["images"]["jpg"]["image_url"] ?? "",
                    "synopsis" => $result["synopsis"] ?? "",
                    "api_source" => "jikan",
                    "is_api" => 1
                ];
            } else {
                flash("No anime found", "warning");
            }
        } else {
            flash("You must provide an anime name", "warning");
        }
    } 
    else if ($action === "create") {
        $allowed = ["anime_id","title","type","status","episodes","score","image_url","synopsis"];

        foreach ($allowed as $field) {
            $animeData[$field] = se($_POST, $field, null, false);
        }

        $animeData["is_api"] = 0;
    }

    // INSERT INTO DB
    if (!empty($animeData)) {
        $db = getDB();
        $query = "INSERT INTO `IT202_G26_Anime` ";
        $columns = [];
        $params = [];

        foreach ($animeData as $k => $v) {
            $columns[] = "`$k`";
            $params[":$k"] = $v;
        }

        $query .= "(" . join(",", $columns) . ")";
        $query .= " VALUES (" . join(",", array_keys($params)) . ")";

        try {
            $stmt = $db->prepare($query);
            $stmt->execute($params);

            $id = $db->lastInsertId();
            flash("Inserted anime record $id", "success");
        } catch (PDOException $e) {
            die($e->getMessage()); // shows real error
        }
    }
}
require(__DIR__ . "/../../../partials/nav.php");
?>
<div class="container-fluid">
    <h3>Create or Fetch Anime</h3>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Create</a>
        </li>
    </ul>

    <div id="fetch" class="tab-target">
        <form method="POST">
            <div class="mb-3">
                <label for="anime">Anime Name</label>
                <input type="search" name="anime" id="anime" placeholder="e.g. Naruto" required>
            </div>

            <input type="hidden" name="action" value="fetch">
            <input type="submit" value="Fetch" class="btn btn-primary">
        </form>
    </div>

    <div id="create" style="display:none;" class="tab-target">
        <form method="POST">

            <div class="mb-3">
                <label for="anime_id">Anime ID</label>
                <input type="text" name="anime_id" id="anime_id" placeholder="API ID (or anything unique)" required>
            </div>

            <div class="mb-3">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" placeholder="Anime Title" required>
            </div>

            <div class="mb-3">
                <label for="type">Type</label>
                <input type="text" name="type" id="type" placeholder="TV, Movie, OVA">
            </div>

            <div class="mb-3">
                <label for="status">Status</label>
                <input type="text" name="status" id="status" placeholder="Airing, Completed">
            </div>

            <div class="mb-3">
                <label for="episodes">Episodes</label>
                <input type="number" name="episodes" id="episodes" placeholder="12">
            </div>

            <div class="mb-3">
                <label for="score">Score</label>
                <input type="number" step="0.01" name="score" id="score" placeholder="8.5">
            </div>

            <div class="mb-3">
                <label for="image_url">Image URL</label>
                <input type="text" name="image_url" id="image_url" placeholder="https://...">
            </div>

            <div class="mb-3">
                <label for="synopsis">Synopsis</label>
                <textarea name="synopsis" id="synopsis" placeholder="Description"></textarea>
            </div>

            <input type="hidden" name="action" value="create">
            <input type="submit" value="Create" class="btn btn-primary">
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    let target = document.getElementById(tab);
    if (target) {
        let eles = document.getElementsByClassName("tab-target");
        for (let ele of eles) {
            ele.style.display = (ele.id === tab) ? "block" : "none";
        }
    }
}
</script>