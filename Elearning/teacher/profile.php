<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Elearning/login.php"); 
    exit;
}

require 'C:\xampp\htdocs\Elearning\connection.php';
 
class TeacherDashboard {
    private $conn;
    private $teacher_id;

    public function __construct($conn, $teacher_id) {
        $this->conn = $conn;
        $this->teacher_id = $teacher_id;
    }
 
    public function getTeacherDetails() {
        $teacherQuery = "SELECT * FROM teacher WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($teacherQuery);
        $stmt->bind_param("s", $this->teacher_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
 
    public function getCourses() {
        $courseQuery = "SELECT id, course_name, description FROM courses WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($courseQuery);
        $stmt->bind_param("s", $this->teacher_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
 
    public function getCourseResources($course_id) {
        $resourcesQuery = "SELECT 
                            (SELECT COUNT(*) FROM videos WHERE course_id = ?) AS video_count,
                            (SELECT COUNT(*) FROM notes WHERE course_id = ?) AS notes_count,
                            (SELECT COUNT(*) FROM assignments WHERE course_id = ?) AS assignment_count,
                            (SELECT COUNT(*) FROM enrollment WHERE course_id = ?) AS enrolled_count";
        $stmt = $this->conn->prepare($resourcesQuery);
        $stmt->bind_param("iiii", $course_id, $course_id, $course_id, $course_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
 
$teacherDashboard = new TeacherDashboard($conn, $_SESSION['teacher_id']);
 
$teacher = $teacherDashboard->getTeacherDetails();
$courses = $teacherDashboard->getCourses();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="profile1.css">
</head>
<body>
    <nav>
        <div class="logo">E-Learning</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li> 
            <li><a href="about.php">About</a></li> 
            <li><a href="\elearning/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="personal-info">
            <h2>Personal Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($teacher['teacher_id']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($teacher['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($teacher['phone']); ?></p>
            <a href="edit_details.php" class="btn">Edit Details</a>
        </div>

        <div class="courses">
            <?php
            if (count($courses) > 0) {
                foreach ($courses as $course) {
                    $resources = $teacherDashboard->getCourseResources($course['id']);

                    echo '<div class="course-card">';
                    echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                    echo '<p class="description">' . htmlspecialchars($course['description']) . '</p>';
                    echo '<div class="resources">';
                    echo '<p>Videos: ' . $resources['video_count'] . '</p>';
                    echo '<p>Notes: ' . $resources['notes_count'] . '</p>';
                    echo '<p>Assignments: ' . $resources['assignment_count'] . '</p>';
                    echo '<p>Enrolled Students: ' . $resources['enrolled_count'] . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>You have not created any courses yet.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
