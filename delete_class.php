<?php
session_start();
include 'DBConnection.php';
$db = new DBConnection();

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $course_id = intval($_POST['course_id']);

    // Delete the enrollment record.
    $stmt = $db->prepare("DELETE FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $user_id, $course_id);
    if ($stmt->execute()) {
        echo "Course deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();

    // Check if there is anyone on the waitlist for this course.
    $stmt = $db->prepare("SELECT waitlist_id, user_id FROM waitlist WHERE course_id = ? ORDER BY request_date ASC LIMIT 1");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->bind_result($waitlist_id, $next_user_id);
    if ($stmt->fetch()) {
        $stmt->close();

        // Enroll the first waiting user.
        $stmt = $db->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $next_user_id, $course_id);
        if ($stmt->execute()) {
            // Remove the user from the waitlist.
            $stmt_del = $db->prepare("DELETE FROM waitlist WHERE waitlist_id = ?");
            $stmt_del->bind_param("i", $waitlist_id);
            $stmt_del->execute();
            $stmt_del->close();
            echo " The next student on the waitlist has been enrolled.";
        }
        $stmt->close();
    } else {
        $stmt->close();
    }
}
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <title>Delete Course</title>
</head>
<body>
    <a href="my_courses.php">Back to My Courses</a>
</body>
</html>
