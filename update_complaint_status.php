<?php
session_start();
require_once 'config/database.php';

// Ensure electrician is logged in
if (!isset($_SESSION['electrician_logged_in']) || $_SESSION['electrician_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'] ?? '';

    if (empty($complaint_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid complaint ID']);
        exit;
    }

    try {
        // Update complaint status to "Completed"
        $stmt = $conn->prepare("UPDATE complaints SET status = 'Completed' WHERE id = :complaint_id");
        $stmt->bindParam(":complaint_id", $complaint_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Complaint marked as completed']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
