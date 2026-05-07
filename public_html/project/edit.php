<?php
require(__DIR__ . "/../../partials/nav.php");

// Get ID
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Validate ID
if ($id <= 0) {
    flash("Invalid or missing ID", "danger");
    die(header("Location: " . get_url("landing.php")));
}

// Load record
$db = getDB();
$stmt = $db->prepare("SELECT * FROM anime_data WHERE id = :id LIMIT 1");
$stmt->execute([":id" => $id]);
$anime = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle missing record
if (!$anime) {
    flash("Anime not found", "warning");
    die(header("Location: " . get_url("landing.php")));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $episodes = intval($_POST["episodes"]);
    $score = floatval($_POST["score"]);
    $status = trim($_POST["status"]);
    $synopsis = trim($_POST["synopsis"]);

    // Validation
    if (empty($title)) {
        flash("Title is required", "danger");
    } elseif ($episodes < 0) {
        flash("Episodes must be 0 or more", "danger");
    } elseif ($score < 0 || $score > 10) {
        flash("Score must be between 0 and 10", "danger");
    } else {
        $stmt = $db->prepare("
            UPDATE anime_data 
            SET title = :title, episodes = :episodes, score = :score, status = :status, synopsis = :synopsis 
            WHERE id = :id
        ");

        $stmt->execute([
            ":title" => $title,
            ":episodes" => $episodes,
            ":score" => $score,
            ":status" => $status,
            ":synopsis" => $synopsis,
            ":id" => $id
        ]);

        flash("Anime updated successfully", "success");
        die(header("Location: " . get_url("view.php?id=$id")));
    }
}

// Form definition (uses your render_input system)
$form = [
    [
        "type" => "text",
        "name" => "title",
        "label" => "Title",
        "value" => $anime["title"],
        "attributes" => ["required" => true]
    ],
    [
        "type" => "number",
        "name" => "episodes",
        "label" => "Episodes",
        "value" => $anime["episodes"],
        "attributes" => ["min" => 0]
    ],
    [
        "type" => "number",
        "name" => "score",
        "label" => "Score",
        "value" => $anime["score"],
        "attributes" => ["min" => 0, "max" => 10, "step" => "0.1"]
    ],
    [
        "type" => "text",
        "name" => "status",
        "label" => "Status",
        "value" => $anime["status"]
    ],
    [
        "type" => "textarea",
        "name" => "synopsis",
        "label" => "Synopsis",
        "value" => $anime["synopsis"]
    ]
];
?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h2>Edit Anime</h2>

        <form method="POST" onsubmit="return validateForm()">
            <div class="row">
                <?php foreach ($form as $field): ?>
                    <div class="col-12 mb-3">
                        <?php render_input($field); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php render_button(["text" => "Update", "type" => "submit", "class" => "btn btn-success"]); ?>
            <a href="<?php echo get_url("view.php?id=$id"); ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
function validateForm() {
    const title = document.querySelector("[name='title']").value.trim();
    const score = parseFloat(document.querySelector("[name='score']").value);

    if (!title) {
        alert("Title is required");
        return false;
    }

    if (score < 0 || score > 10) {
        alert("Score must be between 0 and 10");
        return false;
    }

    return true;
}
</script>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>