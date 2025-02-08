<?php
require_once 'config/database.php';

if (isset($_GET['complaint_id'])) {
    $complaint_id = $_GET['complaint_id'];

    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("SELECT * FROM complaints WHERE id = :complaint_id");
    $stmt->bindParam(":complaint_id", $complaint_id);
    $stmt->execute();
    $complaint = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($complaint) {
        echo json_encode([
            "success" => true,
            "id" => $complaint['id'],
            "name" => $complaint['name'],
            "address" => $complaint['address'],
            "phone_number" => $complaint['phone_number'],
            "appliance" => $complaint['appliance'],
            "description" => $complaint['description'],
            "status" => $complaint['status']
        ]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>
