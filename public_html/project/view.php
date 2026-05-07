<?php
require(__DIR__ . "/../../partials/nav.php");

// Get ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle invalid ID
if ($id <= 0) {
    flash("Invalid or missing ID", "danger");
    die(header("Location: " . get_url("landing.php")));
}

// Load record
$db = getDB();
$stmt = $db->prepare("SELECT * FROM IT202_G26_Anime WHERE id = :id LIMIT 1");
$stmt->execute([":id" => $id]);
$anime = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle missing record
if (!$anime) {
    flash("Anime not found", "warning");
    die(header("Location: " . get_url("landing.php")));
}
?>

<a href="edit.php?id=<?php echo $anime['id']; ?>" class="btn btn-warning">Edit</a>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h2 class="mb-3"><?php echo htmlspecialchars($anime['title']); ?></h2>

        <p><strong>Type:</strong> <?php echo htmlspecialchars($anime['type']); ?></p>
        <p><strong>Episodes:</strong> <?php echo htmlspecialchars($anime['episodes']); ?></p>
        <p><strong>Score:</strong> <?php echo htmlspecialchars($anime['score']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($anime['status']); ?></p>

        <div class="mt-3">
            <a href="<?php echo get_url('landing.php'); ?>" class="btn btn-primary">Back</a>
            <a href="<?php echo get_url('delete.php?id=' . $anime['id']); ?>" class="btn btn-danger">Delete</a>
        </div>
    </div>
</div>