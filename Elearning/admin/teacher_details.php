<?php
include_once 'Database.php';

$db = new Database();
$db->connect();

$teachers = $db->getAllTeachers();


if (isset($_GET['delete_teacher'])) {
    $db->deleteTeacher($_GET['delete_teacher']);
    header("Location: teacher_details.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Details</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container">

<div class="btn-container">
    <a href="student_details.php" class="btn">View Students</a>
    <a href="teacher_details.php" class="btn">View Teachers</a>
    <a href="contact_details.php" class="btn">View Contact Messages</a>
    <a href="teacher_aproval.php" class="btn">Pending Aplication</a>
    <a href="\elearning/logout.php" class="btn">Logout</a>
    <a href="courses.php" class="btn">Courses</a>
    <a href="feedback.php" class="btn">View Feedback</a>
</div>

</div>

    <h1>Teachers</h1>

    <div class="container">

        <table>
            <tr>
                <th>Teacher ID</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?php echo $teacher['teacher_id']; ?></td>
                    <td><?php echo $teacher['email']; ?></td>
                    <td><?php echo $teacher['phone']; ?></td>
                    <td><?php echo $teacher['gender']; ?></td>
                    <td>
                        <a href="?delete_teacher=<?php echo $teacher['teacher_id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>

</body>
</html>
