<?php

require 'C:\xampp\htdocs\Elearning\connection.php';

session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: \Elearning/login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

class Student {
    private $conn;
    public $student_id;
    public $name;
    public $email;
    public $phone;

    public function __construct($conn, $student_id) {
        $this->conn = $conn;
        $this->student_id = $student_id;
        $this->loadStudentDetails();
    }

    private function loadStudentDetails() {
        $query = "SELECT * FROM students WHERE student_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $this->name = $student['student_id'];
        $this->email = $student['email'];
        $this->phone = $student['phone'];
    }
}

class Course {
    private $conn;
    public $id;
    public $course_name;

    public function __construct($conn, $course_id, $course_name) {
        $this->conn = $conn;
        $this->id = $course_id;
        $this->course_name = $course_name;
    }

    public function getResourceCounts() {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM videos WHERE course_id = ?) AS video_count,
                    (SELECT COUNT(*) FROM notes WHERE course_id = ?) AS notes_count,
                    (SELECT COUNT(*) FROM assignments WHERE course_id = ?) AS assignment_count";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $this->id, $this->id, $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

class Enrollment {
    private $conn;
    public $student_id;

    public function __construct($conn, $student_id) {
        $this->conn = $conn;
        $this->student_id = $student_id;
    }

    public function getCourses() {
        $query = "SELECT courses.id, courses.course_name FROM courses
                    INNER JOIN enrollment ON courses.id = enrollment.course_id
                    WHERE enrollment.student_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

$student = new Student($conn, $student_id);
$enrollment = new Enrollment($conn, $student_id);
$courses = $enrollment->getCourses();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="dashboard1.css">
</head>
<body>
    <nav>
        <div class="logo">E-Learning</div>
        <ul class="nav-links">
            <li><a href="new_courses.php">New Courses</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="\elearning/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
       
        <div class="personal-info">
            <h2>Personal Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student->name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student->email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($student->phone); ?></p>
            <a href="edit_details.php" class="btn">Edit Details</a>
        </div>

        
        <div class="courses">
            <?php
            if (count($courses) > 0) {
                foreach ($courses as $course) {
                    $courseObj = new Course($conn, $course['id'], $course['course_name']);
                    $resources = $courseObj->getResourceCounts();
                    echo '<a href="course_content.php?course_id=' . $course['id'] . '" class="course-card">';
                    echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                    echo '<div class="resources">';
                    echo '<p>Videos: ' . $resources['video_count'] . '</p>';
                    echo '<p>Notes: ' . $resources['notes_count'] . '</p>';
                    echo '<p>Assignments: ' . $resources['assignment_count'] . '</p>';
                    echo '</div>';
                    echo '</a>';
                }
            } else {
                echo '<p>You are not enrolled in any courses yet.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
