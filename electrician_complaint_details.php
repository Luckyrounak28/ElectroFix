<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['electrician_logged_in']) || $_SESSION['electrician_logged_in'] !== true) {
    header("Location: electrician_login.php");
    exit;
}

if (!isset($_GET['complaint_id'])) {
    die("Invalid request.");
}

$database = new Database();
$conn = $database->getConnection();
$complaint_id = $_GET['complaint_id'];

$stmt = $conn->prepare("SELECT * FROM complaints WHERE id = :complaint_id");
$stmt->bindParam(":complaint_id", $complaint_id);
$stmt->execute();
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    die("Complaint not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-gray-800 text-white p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Complaint Details</h1>
        <a href="electrician_dashboard.php" class="bg-blue-600 px-4 py-2 rounded">Back</a>
    </header>

    <main class="container mx-auto p-6 bg-white shadow-md rounded">
        <h2 class="text-xl font-bold mb-4 text-blue-600">Complaint #<?= $complaint['id'] ?></h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($complaint['name']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($complaint['address']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($complaint['phone_number']) ?></p>
        <p><strong>Appliance:</strong> <?= htmlspecialchars($complaint['appliance']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($complaint['description']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($complaint['status']) ?></p>

        <?php if ($complaint['status'] !== 'Completed'): ?>
            <form method="POST" action="update_complaint_status.php">
                <input type="hidden" name="complaint_id" value="<?= $complaint['id'] ?>">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded mt-4">Mark as Completed</button>
            </form>
        <?php else: ?>
            <span class="text-green-600 font-bold">Completed</span>
        <?php endif; ?>
    </main>
</body>
</html>
