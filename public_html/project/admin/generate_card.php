<?php
require(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

$db = getDB();

if (isset($_POST["anime_id"])) {

    $anime_id = intval($_POST["anime_id"]);

    $rarities = ["Common", "Rare", "Epic", "Legendary"];
    $rarity = $rarities[array_rand($rarities)];

    $price = rand(100, 1000);

    $stmt = $db->prepare("
        INSERT INTO IT202_G26_Anime_Cards
        (anime_id, rarity, price)
        VALUES
        (:anime_id, :rarity, :price)
    ");

    $stmt->execute([
        ":anime_id" => $anime_id,
        ":rarity" => $rarity,
        ":price" => $price
    ]);

    flash("Anime card generated!", "success");
}

$stmt = $db->query("
    SELECT anime_id, title
    FROM IT202_G26_Anime
    ORDER BY title ASC
");

$anime = $stmt->fetchAll(PDO::FETCH_ASSOC);

require(__DIR__ . "/../../partials/nav.php");
?>

<div class="container mt-4">

    <h1>Generate Anime Card</h1>

    <form method="POST" action="">

        <select name="anime_id" class="form-control mb-3">

            <?php foreach ($anime as $a): ?>

                <option value="<?php echo $a["anime_id"]; ?>">
                    <?php echo htmlspecialchars($a["title"]); ?>
                </option>

            <?php endforeach; ?>

        </select>

        <button class="btn btn-primary">
            Generate Card
        </button>

    </form>

</div>

<?php //require(__DIR__ . "/../../../partials/footer.php"); ?>