<?php
session_start();
include 'DBConnection.php';
$db = new DBConnection();

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT c.course_id, c.course_code, c.course_name, c.semester, e.enrollment_date 
          FROM enrollments e 
          JOIN courses c ON e.course_id = c.course_id 
          WHERE e.user_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <title>My Registered Courses</title>
</head>
<body>
    <h2>My Registered Courses</h2>
    <table border="1">
        <tr>
            <th>Course ID</th>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Semester</th>
            <th>Enrollment Date</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['course_id']; ?></td>
            <td><?php echo $row['course_code']; ?></td>
            <td><?php echo $row['course_name']; ?></td>
            <td><?php echo $row['semester']; ?></td>
            <td><?php echo $row['enrollment_date']; ?></td>
            <td>
                <form method="post" action="delete_class.php" style="display:inline;">
                    <input type="hidden" name="course_id" value="<?php echo $row['course_id']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
    <a href="add_course.php">Add More Courses</a>
    <a href="logout.php">Logout</a>
</body>
</html>

<?php
$stmt->close();
$db->close();
?>
