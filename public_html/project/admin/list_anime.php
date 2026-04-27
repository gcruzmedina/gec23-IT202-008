<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

// Fetch anime data
$query = "SELECT id, title, episodes, score, status, image_url, synopsis, is_api 
FROM `Anime` ORDER BY created DESC LIMIT 25";

$db = getDB();
$stmt = $db->prepare($query);
$results = [];

try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching anime " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}
?>

<div class="container-fluid">
    <h3>List Anime</h3>

    <?php if (count($results) == 0) : ?>
        <p>No results to show</p>
    <?php else : ?>
        <table class="table">
            <?php foreach ($results as $index => $record) : ?>
                
                <?php if ($index == 0) : ?>
                    <thead>
                        <?php foreach ($record as $column => $value) : ?>
                            <th><?php se($column); ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </thead>
                <?php endif; ?>

                <tr>
                    <?php foreach ($record as $column => $value) : ?>
                        <td>
                            <?php if ($column === "image_url") : ?>
                                <img src="<?php se($value); ?>" width="80">
                            <?php elseif ($column === "synopsis") : ?>
                                <?php echo substr($value, 0, 100) . "..."; ?>
                            <?php else : ?>
                                <?php se($value, null, "N/A"); ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>

                    <td>
                        <a href="<?php echo get_url("admin/edit_anime.php");?>?id=<?php se($record, "id"); ?>">Edit</a>
                    </td>
                </tr>

            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>