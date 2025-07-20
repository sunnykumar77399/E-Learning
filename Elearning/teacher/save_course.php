<?php
session_start();

require 'C:\xampp\htdocs\Elearning\connection.php';

class CourseManager
{
    private $conn;
    private $teacher_id;

    public function __construct($conn)
    {
        $this->conn = $conn; 
        if (!isset($_SESSION['teacher_id'])) {
            header("Location: login.php");
            exit();
        } 
        $this->teacher_id = $_SESSION['teacher_id'];
    }
 
    public function createCourse($course_name, $description)
    { 
        $course_name = $this->conn->real_escape_string(trim($course_name));
        $description = $this->conn->real_escape_string(trim($description));
 
        $sql = "INSERT INTO courses (teacher_id, course_name, description) VALUES ('$this->teacher_id', '$course_name', '$description')";

        if ($this->conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Course created successfully!";
            return true;
        } else {
            $_SESSION['error'] = "Error: Could not create the course. Please try again.";
            return false;
        }
    }
} 
$courseManager = new CourseManager($conn);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $isCourseCreated = $courseManager->createCourse($course_name, $description);
    if ($isCourseCreated) {
        echo "<script>
                alert('Course created successfully!');
                window.location.href = 'dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: Could not create the course. Please try again.');
                window.location.href = 'dashboard.php';
              </script>";
    }
}

$conn->close();
?>
