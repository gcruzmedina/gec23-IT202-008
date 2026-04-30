<?php
// session setup
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}

if ($domain != "localhost") {
    session_set_cookie_params([
        "lifetime" => 60 * 60,
        "path" => "/project",
        "domain" => $domain,
        "secure" => true,
        "httponly" => true,
        "samesite" => "lax"
    ]);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require(__DIR__ . "/../lib/functions.php");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php get_url('styles.css', true); ?>">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand text-uppercase" href="#">terkoloko</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto">

                <?php if (is_logged_in()) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php get_url('landing.php', true); ?>">Landing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php get_url('profile.php', true); ?>">Profile</a>
                    </li>

                    <!-- ✅ Anime dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            Anime
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php get_url('admin/create_anime.php', true); ?>">
                                    Create Anime
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php get_url('admin/list_anime.php', true); ?>">
                                    List Anime
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (!is_logged_in()) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php get_url('login.php', true); ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php get_url('register.php', true); ?>">Register</a>
                    </li>
                <?php endif; ?>

                <?php if (has_role("Admin")) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            Roles
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php get_url('admin/create_role.php', true); ?>">Create Role</a></li>
                            <li><a class="dropdown-item" href="<?php get_url('admin/list_roles.php', true); ?>">List Roles</a></li>
                            <li><a class="dropdown-item" href="<?php get_url('admin/assign_roles.php', true); ?>">Assign Roles</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (is_logged_in()) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php get_url('logout.php', true); ?>">Logout</a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
</nav>