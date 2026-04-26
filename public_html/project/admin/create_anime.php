<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

// Handle anime fetch/create
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $animeName = se($_POST, "anime", "", false);
    $animeData = [];

    if ($animeName) {
        if ($action === "fetch") {
            $result = fetch_anime($animeName); // YOU CREATE THIS FUNCTION

            error_log("Anime API Data: " . var_export($result, true));

            if ($result) {
                // Map API → DB
                $animeData = [
                    "title" => $result["title"] ?? "",
                    "episodes" => $result["episodes"] ?? null,
                    "score" => $result["score"] ?? null,
                    "status" => $result["status"] ?? "",
                    "image_url" => $result["image"] ?? "",
                    "synopsis" => $result["synopsis"] ?? "",
                    "is_api" => 1
                ];
            }
        } else if ($action === "create") {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ["title", "episodes", "score", "status", "image_url", "synopsis"])) {
                    unset($_POST[$k]);
                }
            }
            $animeData = $_POST;
            $animeData["is_api"] = 0;
        }
    } else {
        flash("You must provide an anime name", "warning");
    }

    // Insert into DB
    if (!empty($animeData)) {
        $db = getDB();
        $query = "INSERT INTO `Anime` ";
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
            flash("Inserted anime " . $db->lastInsertId(), "success");
        } catch (PDOException $e) {
            error_log($e);
            flash("Error inserting anime", "danger");
        }
    }
}
?>