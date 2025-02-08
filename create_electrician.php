<?php
require_once "config/Database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    $database = new Database();
    $conn = $database->getConnection();

    try {
        $stmt = $conn->prepare("INSERT INTO electricians (name, email, phone, password) VALUES (:name, :email, :phone, :password)");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":password", $password);
        $stmt->execute();

        // Redirect back to admin.php with success message
        header("Location: admin.php?success=Electrician account created successfully!");
        exit();
    } catch (PDOException $e) {
        // Redirect back to admin.php with error message
        header("Location: admin.php?error=" . urlencode("Error: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: admin.php?error=" . urlencode("Invalid request"));
    exit();
}
?>
