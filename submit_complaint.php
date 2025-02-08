<?php
require_once "config/Database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $appliance = $_POST["appliance"];
    $description = $_POST["description"];

    $database = new Database();
    $conn = $database->getConnection();

    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = $user["id"];
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)");
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":phone", $phone);
            $stmt->execute();
            $user_id = $conn->lastInsertId();
        }

        // Insert complaint with new fields
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, name, phone_number, address, appliance, description) 
                                VALUES (:user_id, :name, :phone, :address, :appliance, :description)");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":appliance", $appliance);
        $stmt->bindParam(":description", $description);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Complaint submitted successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
