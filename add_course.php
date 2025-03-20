<?php
session_start();
include 'DBConnection.php';
$db = new DBConnection();

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: Filter courses by semester (if a semester is selected via GET or POST)
$semester = isset($_GET['semester']) ? $_GET['semester'] : 'Spring';

// Retrieve available courses for the semester.
$stmt = $db->prepare("SELECT course_id, course_code, course_name, max_enrollment, semester FROM courses WHERE semester = ?");
$stmt->bind_param("s", $semester);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <title>Add Courses</title>
</head>
<body>
    <h2>Available Courses for <?php echo htmlspecialchars($semester); ?></h2>
    <table border="1">
        <tr>
            <th>Course ID</th>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Max Enrollment</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['course_id']; ?></td>
            <td><?php echo $row['course_code']; ?></td>
         <td><?php echo $row['course_name']; ?></td>
            <td><?php echo $row['max_enrollment']; ?></td>
            <td>
                <form method="post" action="register_class.php" style="display:inline;">
                    <input type="hidden" name="course_id" value="<?php echo $row['course_id']; ?>">
                    <button type="submit">Register</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
    <br>
    <a href="my_courses.php">Back to My Courses</a>
</body>
</html>

<?php
$stmt->close();
$db->close();
?>
