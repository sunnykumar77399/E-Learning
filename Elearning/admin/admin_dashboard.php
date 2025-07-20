<?php
include_once 'Database.php';

$db = new Database();
$db->connect();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            margin: 5px;
        }

        .btn-danger {
            background-color: #f44336;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1>Admin Dashboard</h1>

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

</body>
</html>
