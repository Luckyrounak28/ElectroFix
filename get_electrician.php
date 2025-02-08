<?php
require_once "config/Database.php";

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT id, name FROM electricians";
$stmt = $conn->prepare($query);
$stmt->execute();

$electricians = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($electricians);
?>
