<?php
session_start();
require_once 'config/database.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Check if electrician ID is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['electrician_id'])) {
    $electrician_id = $_POST['electrician_id'];

    $database = new Database();
    $conn = $database->getConnection();

    // Delete electrician from the database
    $stmt = $conn->prepare("DELETE FROM electricians WHERE id = ?");
    if ($stmt->execute([$electrician_id])) {
        $_SESSION['message'] = "Electrician deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete electrician.";
    }
}

// Redirect back to admin dashboard
header("Location: admin.php");
exit;
?>
