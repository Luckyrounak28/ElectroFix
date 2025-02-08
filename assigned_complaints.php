<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $complaint_id = $_POST['complaint_id'];
    $electrician_id = $_POST['electrician_id'];

    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("UPDATE complaints SET assigned_to = ? WHERE id = ?");
    if ($stmt->execute([$electrician_id, $complaint_id])) {
        echo json_encode(["success" => true, "message" => "Complaint assigned successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to assign complaint."]);
    }
}
?>
