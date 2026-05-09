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

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Helpers JS -->
<script src="<?php echo get_url('helpers.js'); ?>"></script>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">

        <a class="navbar-brand text-uppercase" href="#">
            TERKOLOKO
        </a>

        <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php if (is_logged_in()) : ?>

                    <!-- Landing -->
                    <li class="nav-item">
                        <a class="nav-link"
                            aria-current="page"
                            href="<?php echo get_url('landing.php'); ?>">
                            Landing
                        </a>
                    </li>

                    <!-- Profile -->
                    <li class="nav-item">
                        <a class="nav-link"
                            aria-current="page"
                            href="<?php echo get_url('profile.php'); ?>">
                            Profile
                        </a>
                    </li>

                    <!-- Anime Cards Dropdown -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            Anime Cards
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('brokers.php'); ?>">
                                    All Cards
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('my_brokers.php'); ?>">
                                    My Cards
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('available_brokers.php'); ?>">
                                    Available Cards
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('unavailable_brokers.php'); ?>">
                                    Unavailable Cards
                                </a>
                            </li>

                        </ul>
                    </li>

                <?php endif; ?>

                <?php if (!is_logged_in()) : ?>

                    <!-- Login -->
                    <li class="nav-item">
                        <a class="nav-link"
                            aria-current="page"
                            href="<?php echo get_url('login.php'); ?>">
                            Login
                        </a>
                    </li>

                    <!-- Register -->
                    <li class="nav-item">
                        <a class="nav-link"
                            aria-current="page"
                            href="<?php echo get_url('register.php'); ?>">
                            Register
                        </a>
                    </li>

                <?php endif; ?>

                <!-- Admin Only -->
                <?php if (has_role("Admin")) : ?>

                    <!-- Anime Dropdown -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            Anime
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('admin/create_anime.php'); ?>">
                                    Create Anime
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('admin/list_anime.php'); ?>">
                                    List Anime
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('admin/generate_card.php'); ?>">
                                    Generate Anime Card
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- Roles Dropdown -->
                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            Roles
                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('admin/create_role.php'); ?>">
                                    Create Role
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('admin/list_roles.php'); ?>">
                                    List Roles
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                    aria-current="page"
                                    href="<?php echo get_url('admin/assign_roles.php'); ?>">
                                    Assign Roles
                                </a>
                            </li>

                        </ul>
                    </li>

                <?php endif; ?>

                <?php if (is_logged_in()) : ?>

                    <!-- Logout -->
                    <li class="nav-item">
                        <a class="nav-link"
                            aria-current="page"
                            href="<?php echo get_url('logout.php'); ?>">
                            Logout
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

            <!-- Points Display -->
            <?php if (is_logged_in()) : ?>

                <span class="navbar-text show-points">
                    Points: 0
                </span>

            <?php endif; ?>

        </div>
    </div>
</nav>