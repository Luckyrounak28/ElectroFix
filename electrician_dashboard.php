<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['electrician_id']) || !isset($_SESSION['electrician_name'])) {
    die("Error: Electrician not logged in. Please log in again.");
}

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed.");
}

$electrician_id = $_SESSION['electrician_id'];
$electrician_name = $_SESSION['electrician_name'];

$stmt = $conn->prepare("SELECT * FROM complaints WHERE assigned_to = :electrician_id ORDER BY status ASC, created_at DESC");
$stmt->bindParam(":electrician_id", $electrician_id, PDO::PARAM_INT);
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrician Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .responsive-table {
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-gray-800 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Hello, <?= htmlspecialchars($electrician_name) ?></h1>
        <a href="electrician_logout.php" class="bg-red-600 px-4 py-2 rounded text-sm">Logout</a>
    </header>

    <main class="container mx-auto p-4">
        <h2 class="text-lg font-bold mb-4 text-blue-600">Assigned Complaints</h2>

        <div class="bg-white p-4 shadow-md rounded responsive-table">
            <?php if (count($complaints) > 0): ?>
                <table class="w-full bg-white border border-gray-300 rounded">
                    <thead class="bg-gray-200 text-sm">
                        <tr>
                            <th class="py-2 px-2 border">ID</th>
                            <th class="py-2 px-2 border">Name</th>
                            <th class="py-2 px-2 border">Phone</th>
                            <th class="py-2 px-2 border">Address</th>
                            <th class="py-2 px-2 border">Appliance</th>
                            <th class="py-2 px-2 border">Description</th>
                            <th class="py-2 px-2 border">Status</th>
                            <th class="py-2 px-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $complaint): ?>
                            <tr class="border-b text-sm">
                                <td class="py-2 px-2 border"><?= $complaint['id'] ?></td>
                                <td class="py-2 px-2 border"><?= htmlspecialchars($complaint['name']) ?></td>
                                <td class="py-2 px-2 border"><?= htmlspecialchars($complaint['phone_number']) ?></td>
                                <td class="py-2 px-2 border"><?= htmlspecialchars($complaint['address']) ?></td>
                                <td class="py-2 px-2 border"><?= htmlspecialchars($complaint['appliance']) ?></td>
                                <td class="py-2 px-2 border"><?= htmlspecialchars($complaint['description']) ?></td>
                                <td class="py-2 px-2 border status-<?= $complaint['id'] ?>"><?= htmlspecialchars($complaint['status']) ?></td>
                                <td class="py-2 px-2 border text-center">
                                    <?php if ($complaint['status'] !== 'Completed'): ?>
                                        <button class="bg-green-600 text-white px-3 py-1 rounded complete-btn text-sm" data-id="<?= $complaint['id'] ?>">Mark as Completed</button>
                                    <?php else: ?>
                                        <span class="text-green-600 font-bold">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center py-4 text-gray-500">No assigned complaints</p>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.body.addEventListener("click", function(event) {
                if (event.target.classList.contains("complete-btn")) {
                    let button = event.target;
                    let complaintId = button.getAttribute("data-id");

                    fetch("update_complaint_status.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `complaint_id=${complaintId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`.status-${complaintId}`).textContent = "Completed";
                            button.textContent = "Completed";
                            button.classList.remove("bg-green-600");
                            button.classList.add("bg-gray-500");
                            button.disabled = true;
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error("Error updating complaint:", error));
                }
            });
        });
    </script>
</body>
</html>
