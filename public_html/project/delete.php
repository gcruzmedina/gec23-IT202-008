<?php
require(__DIR__ . "/../../partials/nav.php");

//  Permission check
if (!has_role("Admin")) {
    flash("You don't have permission to delete records", "danger");
    die(header("Location: " . get_url("landing.php")));
}

//  Get ID
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

//  Invalid ID
if ($id <= 0) {
    flash("Invalid or missing ID", "danger");
    die(header("Location: " . get_url("landing.php")));
}

// Determine where to go back
$redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "landing.php";

// Optional: preserve query string (filters/sort)
$query = isset($_GET["query"]) ? $_GET["query"] : "";
$final_redirect = get_url($redirect . ($query ? "?$query" : ""));

// Load record (optional but good practice)
$db = getDB();
$stmt = $db->prepare("SELECT id FROM anime_data WHERE id = :id LIMIT 1");
$stmt->execute([":id" => $id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

// Record not found
if (!$record) {
    flash("Record not found", "warning");
    die(header("Location: $final_redirect"));
}

//  Delete record
$stmt = $db->prepare("DELETE FROM anime_data WHERE id = :id");
$stmt->execute([":id" => $id]);

// Success
flash("Record deleted successfully", "success");

// Redirect back
die(header("Location: $final_redirect"));