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
        $this->teacher_id = $_SESSION['teacher_id'];
    }
 
    public function getCourses()
    {
        $courseQuery = "SELECT id, course_name FROM courses WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($courseQuery);
        $stmt->bind_param('s', $this->teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
 
    public function deleteCourse($courseId)
    {
        $courseId = (int)$courseId;
 
        $checkCourseQuery = "SELECT id FROM courses WHERE id = ? AND teacher_id = ?";
        $stmt = $this->conn->prepare($checkCourseQuery);
        $stmt->bind_param('is', $courseId, $this->teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) { 
            $deleteAssignmentsQuery = "DELETE FROM assignments WHERE course_id = ?";
            $stmt = $this->conn->prepare($deleteAssignmentsQuery);
            $stmt->bind_param('i', $courseId);
            $stmt->execute();
 
            $deleteVideosQuery = "DELETE FROM videos WHERE course_id = ?";
            $stmt = $this->conn->prepare($deleteVideosQuery);
            $stmt->bind_param('i', $courseId);
            $stmt->execute();
 
            $deleteCourseQuery = "DELETE FROM courses WHERE id = ?";
            $stmt = $this->conn->prepare($deleteCourseQuery);
            $stmt->bind_param('i', $courseId);
            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }
}
$courseManager = new CourseManager($conn);
$courses = $courseManager->getCourses();
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['courseIdToDelete'])) {
    $courseId = $_POST['courseIdToDelete'];
    if ($courseManager->deleteCourse($courseId)) {
        echo "<script>alert('Course deleted successfully!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to delete course. Either the course does not exist or you do not have permission to delete it.');</script>";
    }
}

$conn->close();
?>
