<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['complaint_id'])) {
    $complaint_id = $_POST['complaint_id'];

    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
    if ($stmt->execute([$complaint_id])) {
        $_SESSION['message'] = "Complaint deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete complaint.";
    }
}

header("Location: admin.php");
exit;
?>
