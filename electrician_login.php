<?php
session_start();
require_once 'config/config.php'; // Ensure database is included

$database = new Database();
$conn = $database->getConnection();

if (isset($_SESSION['electrician_logged_in']) && $_SESSION['electrician_logged_in'] === true) {
    header("Location: electrician_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT id, name, password FROM electricians WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $electrician = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($electrician && password_verify($password, $electrician['password'])) {
            $_SESSION['electrician_logged_in'] = true;
            $_SESSION['electrician_id'] = $electrician['id'];
            $_SESSION['electrician_name'] = $electrician['name'];
            header("Location: electrician_dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrician Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold text-center mb-4">Electrician Login</h2>
        <?php if (!empty($error)) echo "<p class='text-red-600 text-center'>$error</p>"; ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded">Login</button>
        </form>
    </div>
</body>
</html>
