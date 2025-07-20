<?php
include_once 'Database.php';

$db = new Database();
$db->connect();

$students = $db->getAllStudents();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
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

    <h1>Students</h1>

    <div class="container">

        <table>
            <tr>
                <th>Student ID</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo $student['student_id']; ?></td>
                    <td><?php echo $student['email']; ?></td>
                    <td><?php echo $student['phone']; ?></td>
                    <td><?php echo $student['address']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>

</body>
</html>
