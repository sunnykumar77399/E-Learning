<?php
session_start();
require 'C:\xampp\htdocs\Elearning\connection.php';

class VideoManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

   
    public function getCourses()
    {
        $sql = "SELECT id, course_name FROM courses";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    
    public function embedVideo($courseId, $videoUrl)
    {
        if ($courseId && $videoUrl) {
            $sql = "INSERT INTO videos (video_url, course_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $videoUrl, $courseId);

            if ($stmt->execute()) {
               
                echo "<script>alert('Video URL embedded successfully!');</script>";
                exit();

            } else {
                $_SESSION['error'] = "Error embedding video: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Invalid input. Please provide valid course and video URL.";
        }
    }
} 
$videoManager = new VideoManager($conn);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
    $videoUrl = filter_input(INPUT_POST, 'videoUrl', FILTER_SANITIZE_URL);

   
    $videoManager->embedVideo($courseId, $videoUrl);

   
    header("Location: dashboard.php");
    exit();
}


$courses = $videoManager->getCourses();

$conn->close();
?>

