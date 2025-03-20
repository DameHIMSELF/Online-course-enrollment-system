<?php
// Include the database connection class
include 'DBConnection.php';

// Instantiate the database connection
$db = new DBConnection();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $username = $db->conn->real_escape_string($_POST['username']);
    $name     = $db->conn->real_escape_string($_POST['name']);
    $phone    = $db->conn->real_escape_string($_POST['phone']);
    $email    = $db->conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // The password will be hashed

    // Check if the username already exists
    $stmt = $db->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows > 0) {
        echo "Username already exists. Please choose another one.";
        exit();
    }
    $stmt->close();

    // Securely hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $db->prepare("INSERT INTO users (username, name, phone, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $name, $phone, $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "Registration successful! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $db->close();
}
?>
