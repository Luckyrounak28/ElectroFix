<?php
require_once "config/Database.php";

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT id, name, phone_number, appliance, description, status FROM complaints ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();

$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($complaints);
?>
