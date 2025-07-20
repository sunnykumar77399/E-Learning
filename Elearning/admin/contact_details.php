<?php
include_once 'Database.php';

$db = new Database();
$db->connect();


$messages = $db->getAllMessages();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Details</title>
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

    <h1>Contact Messages</h1>

    <div class="container">

        <?php if (empty($messages)): ?>
            <p>No contact messages found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Student ID</th>
                    <th>Email</th>
                </tr>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo $message['subject']; ?></td>
                        <td><?php echo $message['message']; ?></td>
                        <td><?php echo $message['student_id']; ?></td>
                        <td><?php echo $message['email']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </div>

</body>
</html>
