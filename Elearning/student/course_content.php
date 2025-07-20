<?php

require 'C:\xampp\htdocs\Elearning\connection.php';

session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: \Elearning/login.php");
    exit;
}
$student_id = $_SESSION['student_id'];

if (!isset($_GET['course_id'])) {
    echo "Error: Missing or invalid course ID.";
}
$course_id = intval($_GET['course_id']);  

class Course {
    private $conn;
    public $course_name;

    public function __construct($conn, $course_id) {
        $this->conn = $conn;
        $this->loadCourse($course_id);
    }

    private function loadCourse($course_id) {
        $query = "SELECT course_name FROM courses WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Debug: Error preparing course query: " . $this->conn->error);
        }
        $stmt->bind_param("i", $course_id);
        if (!$stmt->execute()) {
            die("Debug: Error executing course query: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            die("Debug: Error: No course found for course_id: $course_id");
        }
        $course = $result->fetch_assoc();
        $this->course_name = $course['course_name'];
    }
}

class Videos {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getVideos($course_id) {
        $query = "SELECT video_id, video_url FROM videos WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Debug: Error preparing videos query: " . $this->conn->error);
        }
        $stmt->bind_param("i", $course_id);
        if (!$stmt->execute()) {
            die("Debug: Error executing videos query: " . $stmt->error);
        }
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

class Notes {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getNotes($course_id) {
        $query = "SELECT note_id, pdf_name, pdf_path FROM notes WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Debug: Error preparing notes query: " . $this->conn->error);
        }
        $stmt->bind_param("i", $course_id);
        if (!$stmt->execute()) {
            die("Debug: Error executing notes query: " . $stmt->error);
        }
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

class Assignments {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAssignments($course_id) {
        $query = "SELECT assignment_id, assignment_title, pdf_document, deadline FROM assignments WHERE course_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Debug: Error preparing assignments query: " . $this->conn->error);
        }
        $stmt->bind_param("i", $course_id);
        if (!$stmt->execute()) {
            die("Debug: Error executing assignments query: " . $stmt->error);
        }
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

class Submission {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function hasSubmitted($student_id, $course_id, $assignment_id) {
        $query = "SELECT * FROM submissions WHERE student_id = ? AND course_id = ? AND assignment_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Debug: Error preparing submission query: " . $this->conn->error);
        }
        $stmt->bind_param("sii", $student_id, $course_id, $assignment_id);
        if (!$stmt->execute()) {
            die("Debug: Error executing submission query: " . $stmt->error);
        }
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}

$course = new Course($conn, $course_id);
$videos = new Videos($conn);
$notes = new Notes($conn);
$assignments = new Assignments($conn);
$submission = new Submission($conn);

$videoList = $videos->getVideos($course_id);
$noteList = $notes->getNotes($course_id);
$assignmentList = $assignments->getAssignments($course_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course->course_name); ?> - Content</title>
    <link rel="stylesheet" href="course_content.css">
</head>
<body>
    <nav>
        <div class="logo">E-Learning</div>
        <ul class="nav-links">
            <li><a href="new_courses.php">New Courses</a></li>
            <li><a href="#">Courses</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="\elearning/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Course Content: <?php echo htmlspecialchars($course->course_name); ?></h2>

        <div class="videos-section">
            <h3>Videos</h3>
            <?php if (!empty($videoList)) : ?>
                <?php foreach ($videoList as $video) : ?>
                    <p><a href="<?php echo htmlspecialchars($video['video_url']); ?>" target="_blank">Watch Video</a></p>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No videos available for this course.</p>
            <?php endif; ?>
        </div>

        <div class="notes-section">
            <h3>Notes</h3>
            <?php if (!empty($noteList)) : ?>
                <table>
                    <thead>
                        <th>Notes</th>
                        <th>View</th>
                    </thead>
                    <tbody>
                        <?php foreach ($noteList as $note) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($note['pdf_name']); ?></td>
                                <td>
                                    <a href="\Elearning/teacher/<?php echo htmlspecialchars($note['pdf_path']); ?>" target="_blank">View PDF</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No notes available for this course.</p>
            <?php endif; ?>
        </div>

        <div class="assignments-section">
            <h3>Pending Assignments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Assignment</th>
                        <th>View</th>
                        <th>Deadline</th>
                        <th>Submit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assignmentList)) : ?>
                        <?php foreach ($assignmentList as $assignment) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['assignment_title']); ?></td>
                                <td>
                                    <a href="\Elearning/teacher/<?php echo htmlspecialchars($assignment['pdf_document']); ?>" target="_blank">View PDF</a>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($assignment['deadline'])); ?></td>
                                <td>
                                    <?php if ($submission->hasSubmitted($student_id, $course_id, $assignment['assignment_id'])) : ?>
                                        <p>Submitted</p>
                                    <?php else : ?>
                                        <form action="submit_assignment.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                            <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                            <input type="file" name="submission_file" required>
                                            <button type="submit">Submit</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No assignments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
