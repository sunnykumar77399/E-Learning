<?php
require 'C:\xampp\htdocs\Elearning\connection.php';
session_start();


if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

class CourseManager {
    private $conn;
    private $student_id;

    public function __construct($conn, $student_id) {
        $this->conn = $conn;
        $this->student_id = $student_id;
    }

   
    public function getCourses() {
        $query = "
            SELECT 
                c.id AS course_id, 
                c.course_name, 
                (SELECT COUNT(*) FROM notes WHERE course_id = c.id) AS notes_count,
                (SELECT COUNT(*) FROM assignments WHERE course_id = c.id) AS assignments_count,
                (SELECT COUNT(*) FROM videos WHERE course_id = c.id) AS videos_count
            FROM courses c
        ";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    
    public function isEnrolled($course_id) {
        $course_id = mysqli_real_escape_string($this->conn, $course_id);
        $student_id = mysqli_real_escape_string($this->conn, $this->student_id);
        $query = "SELECT * FROM enrollment WHERE student_id = '$student_id' AND course_id = '$course_id'";
        $result = $this->conn->query($query);
        return $result->num_rows > 0;
    }
    
    public function enroll($course_id) {
        $course_id = mysqli_real_escape_string($this->conn, $course_id);
        $student_id = mysqli_real_escape_string($this->conn, $this->student_id);
    
        if ($this->isEnrolled($course_id)) {
         
            echo "<script>alert('You are already enrolled in this course!');</script>";
            echo "<script>window.location.href = 'new_courses.php';</script>";
            exit;
        } else {
            $query = "INSERT INTO enrollment (student_id, course_id) VALUES ('$student_id', '$course_id')";
            return $this->conn->query($query);
        }
    }
}

$courseManager = new CourseManager($conn, $student_id);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    if ($courseManager->enroll($course_id)) {
        
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Failed to enroll in the course. Please try again.";
    }
}


$courses = $courseManager->getCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Courses</title>
    <link rel="stylesheet" href="new_courses1.css">
    
</head>
<body>
<div class="logo">E-Learning</div>
         <ul class="nav-links"> 
            <li><a href="dashboard.php">Dashboard</a></li> 
            <li><a href="\elearning/logout.php">Logout</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            
        </ul>
    </nav> 
    <div class="header">
        <h1>Available Courses</h1>
    </div>

    <div class="container">
        <?php if (isset($message)) { echo "<div class='message'>$message</div>"; } ?>

        <div class="courses">
            <?php foreach ($courses as $course): ?>
                <div class="course-card">
                   <center> <h3><?php echo htmlspecialchars($course['course_name']); ?></h3></center>
                    <div class="course-info">
                        <p><strong>Notes:</strong> <?php echo $course['notes_count']; ?></p>
                        <p><strong>Assignments:</strong> <?php echo $course['assignments_count']; ?></p>
                        <p><strong>Videos:</strong> <?php echo $course['videos_count']; ?></p>
                    </div>
                  <center>  <form method="POST">
                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                        <button type="submit" class="enroll-btn">Enroll</button>
                    </form></center>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
