<?php
session_start();
include 'DBConnection.php';
$db = new DBConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data.
    $username = $db->conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Plaintext password, to be verified against the hashed version

    // Prepare statement to fetch user data based on username.
    $stmt = $db->prepare("SELECT user_id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists.
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
        // Verify the password.
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variable and redirect.
            $_SESSION['user_id'] = $user_id;
            header("Location: my_courses.php"); // Redirect to the courses page.
            exit();
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "User not found. Please check your username.";
    }
    $stmt->close();
}
$db->close();
?>
