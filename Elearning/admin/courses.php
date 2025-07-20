<?php

include_once 'Database.php';


$db = new Database();
$conn = $db->connect();


$query = "
    SELECT 
        c.course_name,
        COUNT(e.student_id) AS num_enrolled,
        c.teacher_id
    FROM courses c
    LEFT JOIN enrollment e ON c.id = e.course_id  -- Ensure course_id is the correct column in the enrollment table
    GROUP BY c.id
";

$result = $conn->query($query);


if ($result->num_rows > 0) {
    $courses = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $courses = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Courses</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
       
.container {
    padding: 20px;
    text-align: center;
}


.btn-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}


.btn {
    background-color: #4CAF50; 
    color: white;
    padding: 12px 25px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 1rem;
    text-align: center;
    transition: background-color 0.3s ease;
}


.btn:hover {
    background-color: #45a049; 
}


@media (max-width: 768px) {
    .btn-container {
        flex-direction: column;
        align-items: center;
    }
}

        
    </style>
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

    <h1>Current Courses</h1>

    <div class="container">

        <?php if (empty($courses)): ?>
            <p>No courses available.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Course Name</th>
                    <th>Number of Students Enrolled</th>
                    <th>Course Creator (Teacher ID)</th>
                </tr>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo $course['num_enrolled']; ?></td>
                        <td><?php echo htmlspecialchars($course['teacher_id']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </div>

</body>
</html>
