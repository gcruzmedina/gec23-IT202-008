<?php
// SAME structure as your friend
require(__DIR__ . "/../../../partials/nav.php");

// 🔥 REQUIRED: load env/config so API key works
require_once(__DIR__ . "/../../../lib/config.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}
?>

<?php
// handle Anime fetch/create
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $animeData = [];

    if ($action === "fetch") {
        $animeName = trim(se($_POST, "anime", "", false));

        if ($animeName) {
            $result = fetch_anime($animeName);
            error_log("Anime API data: " . var_export($result, true));

            if (!empty($result)) {
                $animeData = [
                    "anime_id" => $result["id"] ?? uniqid(),
                    "title" => $result["title"] ?? "",
                    "type" => $result["type"] ?? "",
                    "status" => $result["status"] ?? "",
                    "episodes" => $result["episodes"] ?? null,
                    "score" => $result["score"] ?? null,
                    "image_url" => $result["images"]["jpg"]["image_url"] ?? "",
                    "synopsis" => $result["synopsis"] ?? "",
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
        foreach ($_POST as $k => $v) {
            if (!in_array($k, [
                "anime_id",
                "title",
                "type",
                "status",
                "episodes",
                "score",
                "image_url",
                "synopsis"
            ])) {
                unset($_POST[$k]);
            }
        }

        $animeData = $_POST;
        $animeData["is_api"] = 0;

        error_log("Manual anime data: " . var_export($animeData, true));
    }

    // insert into DB (same as your friend)
    if (!empty($animeData)) {
        try {
            $r = insert("IT202_G26_Anime", $animeData, ["update_duplicate" => true]);

            if ($r["lastInsertId"]) {
                flash("Inserted anime record " . $r["lastInsertId"], "success");
            } else {
                flash("Updated existing anime record", "success");
            }

        } catch (PDOException $e) {
            error_log("DB error: " . var_export($e, true));
            flash("An error occurred", "danger");
        } catch (Exception $e) {
            error_log("General error: " . var_export($e, true));
            flash("An error occurred: " . $e->getMessage(), "danger");
        }
    }
}
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

    <!-- FETCH -->
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

    <!-- CREATE -->
    <div id="create" style="display:none;" class="tab-target">
        <form method="POST">

            <div class="mb-3">
                <label>Anime ID</label>
                <input type="text" name="anime_id" required>
            </div>

            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" required>
            </div>

            <div class="mb-3">
                <label>Type</label>
                <input type="text" name="type">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <input type="text" name="status">
            </div>

            <div class="mb-3">
                <label>Episodes</label>
                <input type="number" name="episodes">
            </div>

            <div class="mb-3">
                <label>Score</label>
                <input type="number" step="0.01" name="score">
            </div>

            <div class="mb-3">
                <label>Image URL</label>
                <input type="text" name="image_url">
            </div>

            <div class="mb-3">
                <label>Synopsis</label>
                <textarea name="synopsis"></textarea>
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

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>