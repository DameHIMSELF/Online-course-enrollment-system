<?php
session_start();
include 'DBConnection.php';
$db = new DBConnection();

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process the form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $course_id = intval($_POST['course_id']);

    // Get the course details (including max enrollment).
    $stmt = $db->prepare("SELECT max_enrollment FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->bind_result($max_enrollment);
    $stmt->fetch();
    $stmt->close();

    // Count current enrollments.
    $stmt = $db->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->bind_result($current_enrollment);
    $stmt->fetch();
    $stmt->close();

    if ($current_enrollment < $max_enrollment) {
        // There is space, so enroll the student.
        $stmt = $db->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $course_id);
        if ($stmt->execute()) {
            echo "Enrollment successful!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Course is full, add the student to the waitlist.
        $stmt = $db->prepare("INSERT INTO waitlist (user_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $course_id);
        if ($stmt->execute()) {
            echo "Course is full. You have been added to the waitlist.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
$db->close();
?>

<!-- HTML form for class registration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <title>Register for a Class</title>
</head>
<body>
    <h2>Register for a Class</h2>
    <form method="post" action="register_class.php">
        <label for="course_id">Course ID:</label>
        <input type="number" id="course_id" name="course_id" required>
        <button type="submit">Register</button>
    </form>
    <a href="my_courses.php">My Courses</a>
    <a href="logout.php">Logout</a>
</body>
</html>
