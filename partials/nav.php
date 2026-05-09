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
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">

        <a class="navbar-brand text-uppercase" href="#">
            terkoloko
        </a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto">

                <?php if (is_logged_in()) : ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo get_url('landing.php'); ?>">
                            Landing
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo get_url('profile.php'); ?>">
                            Profile
                        </a>
                    </li>

                    <!-- Anime Dropdown -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown">

                            Anime
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('admin/create_anime.php'); ?>">
                                    Create Anime
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('admin/list_anime.php'); ?>">
                                    List Anime
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- Anime Cards Dropdown -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown">

                            Anime Cards
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('brokers.php'); ?>">
                                    All Cards
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('available_brokers.php'); ?>">
                                    Available Cards
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('unavailable_brokers.php'); ?>">
                                    Unavailable Cards
                                </a>
                            </li>

                        </ul>
                    </li>

                <?php endif; ?>

                <?php if (!is_logged_in()) : ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo get_url('login.php'); ?>">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo get_url('register.php'); ?>">
                            Register
                        </a>
                    </li>

                <?php endif; ?>

                <?php if (has_role("Admin")) : ?>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown">

                            Roles
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('admin/create_role.php'); ?>">
                                    Create Role
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('admin/list_roles.php'); ?>">
                                    List Roles
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    href="<?php echo get_url('admin/assign_roles.php'); ?>">
                                    Assign Roles
                                </a>
                            </li>

                        </ul>
                    </li>

                <?php endif; ?>

                <?php if (is_logged_in()) : ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo get_url('logout.php'); ?>">
                            Logout
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>