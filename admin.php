<?php
session_start();
require_once 'config/database.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$database = new Database();
$conn = $database->getConnection();

// Fetch complaints
$stmt = $conn->prepare("SELECT * FROM complaints ORDER BY created_at DESC");
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch electricians
$stmt = $conn->prepare("SELECT * FROM electricians ORDER BY name ASC");
$stmt->execute();
$electricians = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroFix - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-gray-800 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <a href="admin_logout.php" class="bg-red-600 px-4 py-2 rounded">Logout</a>
    </header>

    <main class="container mx-auto p-4">
        
        <section class="bg-white p-4 rounded shadow-md overflow-x-auto">
            <h2 class="text-lg font-bold mb-4 text-blue-600">Complaints Management</h2>
            <table class="min-w-full bg-white shadow-md rounded text-sm sm:text-base">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-2 sm:px-4">ID</th>
                        <th class="py-2 px-2 sm:px-4">Name</th>
                        <th class="py-2 px-2 sm:px-4">Phone</th>
                        <th class="py-2 px-2 sm:px-4">Appliance</th>
                        <th class="py-2 px-2 sm:px-4">Status</th>
                        <th class="py-2 px-2 sm:px-4">Assign</th>
                        <th class="py-2 px-2 sm:px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr class="border-b">
                            <td class="py-2 px-2 sm:px-4"><?= $complaint['id'] ?></td>
                            <td class="py-2 px-2 sm:px-4"><?= $complaint['name'] ?></td>
                            <td class="py-2 px-2 sm:px-4"><?= $complaint['phone_number'] ?></td>
                            <td class="py-2 px-2 sm:px-4"><?= $complaint['appliance'] ?></td>
                            <td class="py-2 px-2 sm:px-4" id="status-<?= $complaint['id'] ?>">
                                <?= $complaint['status'] ?>
                            </td>
                            <td class="py-2 px-2 sm:px-4 flex flex-col sm:flex-row gap-2">
                                <select class="p-1 border rounded assign-select" data-id="<?= $complaint['id'] ?>">
                                    <option value="">Select Electrician</option>
                                    <?php foreach ($electricians as $electrician): ?>
                                        <option value="<?= $electrician['id'] ?>" <?= ($complaint['assigned_to'] == $electrician['id']) ? 'selected' : '' ?>>
                                            <?= $electrician['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="assign-btn bg-blue-600 text-white px-3 py-1 rounded" data-id="<?= $complaint['id'] ?>">
                                    Assign
                                </button>
                            </td>
                            <td class="py-2 px-2 sm:px-4">
                                <form method="POST" action="delete_complaint.php">
                                    <input type="hidden" name="complaint_id" value="<?= $complaint['id'] ?>">
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="bg-white p-4 rounded shadow-md mt-6">
            <h2 class="text-lg font-bold mb-4 text-blue-600">Manage Electricians</h2>
            <button id="create-electrician-btn" class="bg-green-600 text-white px-4 py-2 rounded">Create Account</button>
            <div id="create-electrician-section" class="hidden mt-4">
                <form id="create-electrician-form" method="POST" action="create_electrician.php">
                    <input type="text" name="name" placeholder="Name" class="p-2 border rounded w-full mb-2" required>
                    <input type="email" name="email" placeholder="Email" class="p-2 border rounded w-full mb-2" required>
                    <input type="text" name="phone" placeholder="Phone" class="p-2 border rounded w-full mb-2" required>
                    <input type="password" name="password" placeholder="Password" class="p-2 border rounded w-full mb-2" required>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Create Account</button>
                </form>
            </div>
            <h3 class="text-lg font-bold mt-6">Existing Electricians</h3>
            <ul id="electricians-list" class="list-disc pl-5">
                <?php foreach ($electricians as $electrician): ?>
                    <li class="flex justify-between items-center py-1">
                        <?= $electrician['name'] ?>
                        <form method="POST" action="delete_electrician.php">
                            <input type="hidden" name="electrician_id" value="<?= $electrician['id'] ?>">
                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

    <script>
        document.getElementById("create-electrician-btn").addEventListener("click", function() {
            document.getElementById("create-electrician-section").classList.toggle("hidden");
        });
        document.querySelectorAll(".assign-btn").forEach(button => {
            button.addEventListener("click", function() {
                let complaintId = this.getAttribute("data-id");
                let electricianId = document.querySelector(`.assign-select[data-id='${complaintId}']`).value;

                if (!electricianId) {
                    alert("Please select an electrician");
                    return;
                }

                fetch("assigned_complaints.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `complaint_id=${complaintId}&electrician_id=${electricianId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        document.getElementById(`status-${complaintId}`).innerText = "Assigned";
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
            });
        });
    </script>
</body>
</html>
