<?php

// 1. Try Render environment variables FIRST
$url = getenv("DB_URL");

// 2. Fallback to .env (for local development only)
if (!$url) {
    $envPath = __DIR__ . "/../.env";

    if (file_exists($envPath)) {
        $env = parse_ini_file($envPath);

        if ($env && isset($env["DB_URL"])) {
            $url = $env["DB_URL"];
        }
    }
}

// 3. Hard fail if still missing
if (!$url) {
    throw new Exception("Missing DB_URL configuration");
}

// 4. Parse database URL
$db_url = parse_url(str_replace("mysql://", "http://", $url));

// 5. Validate
if (
    !$db_url ||
    !isset($db_url["host"], $db_url["user"], $db_url["pass"], $db_url["path"])
) {
    error_log("Bad DB_URL: " . $url);
    error_log("Parsed: " . print_r($db_url, true));
    throw new Exception("Invalid DB_URL format");
}

// 6. Assign values
$dbhost = $db_url["host"];
$dbuser = $db_url["user"];
$dbpass = $db_url["pass"];
$dbdatabase = ltrim($db_url["path"], "/");
$dbport = $db_url["port"] ?? 3306;

?>