<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getDB()
{
    global $db;
    global $dbhost, $dbport, $dbdatabase, $dbuser, $dbpass;

    if (!isset($db)) {
        try {
            require_once(__DIR__ . "/config.php");

            // ✅ include port (IMPORTANT)
            $connection_string = "mysql:host=$dbhost;port=$dbport;dbname=$dbdatabase;charset=utf8mb4";

            $db = new PDO($connection_string, $dbuser, $dbpass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // ✅ show real errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

        } catch (PDOException $e) {
            // ✅ show real error (this is what you need)
            error_log("DB ERROR: " . $e->getMessage());
            die("DB CONNECTION FAILED: " . $e->getMessage());
        }
    }

    return $db;
}