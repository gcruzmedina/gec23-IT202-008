<?php
require(__DIR__ . "/../../partials/nav.php");

if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}

/*
    GET ANIME ID
*/

$anime_id = se($_GET, "id", -1, false);

if ($anime_id <= 0) {

    flash("Invalid anime ID", "warning");

    redirect(get_last_route());
}

/*
    QUERY
*/

$query = "
SELECT
    a.id,
    title,
    rarity,
    power,
    episodes,
    score,
    status,
    image_url,
    username,
    user_id
FROM `AnimeCards` a
LEFT JOIN `UserAnimeCards` ua
    ON a.id = ua.anime_id
LEFT JOIN `Users` u
    ON u.id = ua.user_id
WHERE a.id = :id
AND a.is_active = 1
";

/*
    FETCH ANIME
*/

$anime = selectAll(
    $query,
    [":id" => $anime_id],
    false
);

/*
    SELECTALL RETURNS ARRAY
*/

if ($anime) {
    $anime = $anime[0] ?? null;
}

/*
    NOT FOUND
*/

if (!$anime) {

    flash("Anime card not found", "warning");

    redirect(get_last_route());
}
?>

<div class="container-fluid">

    <h1>
        Anime Card:
        <?php se($anime, "title", "N/A"); ?>
    </h1>

    <a
        href="<?php get_last_route(true); ?>"
        class="btn btn-secondary mb-2"
    >
        Back
    </a>

    <?php if (!$anime): ?>

        <p>Anime card not found</p>

    <?php else: ?>

        <div class="row">

            <div class="col-md-4">

                <div class="card p-2 m-2 h-100">

                    <img
                        src="<?php echo $anime["image_url"]; ?>"
                        class="card-img-top"
                        alt="anime image"
                    >

                    <div class="card-body">

                        <h3 class="card-title">
                            <?php echo $anime["title"]; ?>
                        </h3>

                        <p>
                            Episodes:
                            <?php echo $anime["episodes"]; ?>
                        </p>

                        <p>
                            Score:
                            <?php echo $anime["score"]; ?>
                        </p>

                        <p>
                            Status:
                            <?php echo $anime["status"]; ?>
                        </p>

                        <p>
                            Rarity:
                            <?php echo $anime["rarity"]; ?>
                        </p>

                        <p>
                            Power:
                            <?php echo $anime["power"]; ?>
                        </p>

                        <?php if (!empty($anime["username"])): ?>

                            <p>
                                Owned By:
                                <?php echo $anime["username"]; ?>
                            </p>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php require(__DIR__ . "/../../partials/footer.php"); ?>