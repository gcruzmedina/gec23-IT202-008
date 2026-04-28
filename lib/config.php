<?php

// Try environment variable first (deployment)
$url = getenv("DB_URL");

// If not found, read .env manually (safe)
if (!$url) {
    $envPath = __DIR__ . "/../.env";

    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), "#") === 0) continue;

            if (strpos($line, "DB_URL=") === 0) {
                $url = trim(substr($line, 7));
                break;
            }
        }
    }
}

// Hard fail if still missing
if (!$url) {
    throw new Exception("Missing DB_URL configuration");
}

// Parse URL
$db_url = parse_url($url);

// Validate
if (
    !$db_url ||
    !isset($db_url["host"], $db_url["user"], $db_url["pass"], $db_url["path"])
) {
    error_log("Bad DB_URL: " . $url);
    error_log("Parsed: " . print_r($db_url, true));
    throw new Exception("Invalid DB_URL format");
}

// Assign values
$dbhost = $db_url["host"];
$dbuser = $db_url["user"];
$dbpass = $db_url["pass"];
$dbdatabase = ltrim($db_url["path"], "/");
$dbport = $db_url["port"] ?? 3306;

?>