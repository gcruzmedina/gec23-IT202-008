<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$id = se($_GET, "id", -1, false);

// Handle update
if (isset($_POST["title"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["title", "episodes", "score", "status", "image_url", "synopsis"])) {
            unset($_POST[$k]);
        }
    }

    $anime = $_POST;
    error_log("Cleaned POST: " . var_export($anime, true));

    $db = getDB();
    $query = "UPDATE `Anime` SET ";
    $params = [];

    foreach ($anime as $k => $v) {
        if ($params) {
            $query .= ",";
        }
        $query .= "`$k`=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;

    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Anime updated", "success");
    } catch (PDOException $e) {
        error_log($e);
        flash("Error updating anime", "danger");
    }
}

// Fetch record
$anime = [];
if ($id > -1) {
    $db = getDB();
    $query = "SELECT title, episodes, score, status, image_url, synopsis 
              FROM `Anime` WHERE id = :id";

    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $anime = $r;
        }
    } catch (PDOException $e) {
        error_log($e);
        flash("Error fetching anime", "danger");
    }
} else {
    flash("Invalid id", "danger");
    die(header("Location:" . get_url("admin/list_anime.php")));
}
?>

<div class="container-fluid">
    <h3>Edit Anime</h3>

    <form method="POST">

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" required value="<?php se($anime, "title"); ?>">
        </div>

        <div class="mb-3">
            <label>Episodes</label>
            <input type="number" name="episodes" value="<?php se($anime, "episodes"); ?>">
        </div>

        <div class="mb-3">
            <label>Score</label>
            <input type="number" step="0.01" name="score" value="<?php se($anime, "score"); ?>">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <input type="text" name="status" value="<?php se($anime, "status"); ?>">
        </div>

        <div class="mb-3">
            <label>Image URL</label>
            <input type="text" name="image_url" value="<?php se($anime, "image_url"); ?>">
        </div>

        <div class="mb-3">
            <label>Synopsis</label>
            <textarea name="synopsis"><?php se($anime, "synopsis"); ?></textarea>
        </div>

        <input type="submit" value="Update" class="btn btn-primary">
    </form>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>