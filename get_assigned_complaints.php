<?php
require_once "config/Database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint_id = $_POST["complaint_id"];
    $electrician_id = $_POST["electrician"];

    $database = new Database();
    $conn = $database->getConnection();

    try {
        $stmt = $conn->prepare("UPDATE complaints SET assigned_to = :electrician WHERE id = :complaint_id");
        $stmt->bindParam(":electrician", $electrician_id);
        $stmt->bindParam(":complaint_id", $complaint_id);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Complaint assigned successfully"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
