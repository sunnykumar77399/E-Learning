<?php

require 'C:\xampp\htdocs\Elearning\connection.php';

session_start();
 
if (!isset($_SESSION['student_id'])) {
    header("Location: Elearning/login.php"); 
    exit;
}

class Student {
    private $conn;
    public $student_id;

    public function __construct($conn, $student_id) {
        $this->conn = $conn;
        $this->student_id = $student_id;
        $this->checkStudentExists();
    }
 
    private function checkStudentExists() {
        $query = "SELECT 1 FROM students WHERE student_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->student_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            die("Error: Invalid student ID.");
        }
    }
}

class AssignmentSubmission {
    private $conn;
    public $student_id;
    public $course_id;
    public $assignment_id;
    public $file;

    const ALLOWED_FILE_TYPES = ['application/pdf'];
    const MAX_FILE_SIZE = 10 * 1024 * 1024;  
    const UPLOAD_DIR = 'uploads/assignments/';

    public function __construct($conn, $student_id, $course_id, $assignment_id, $file) {
        $this->conn = $conn;
        $this->student_id = $student_id;
        $this->course_id = $course_id;
        $this->assignment_id = $assignment_id;
        $this->file = $file;
    }
 
    public function validateFile() {
        if ($this->file['error'] !== UPLOAD_ERR_OK) {
            die("Error: File upload failed with error code " . $this->file['error']);
        }

        $fileType = mime_content_type($this->file['tmp_name']);
        if (!in_array($fileType, self::ALLOWED_FILE_TYPES)) {
            die("Error: Invalid file type. Only PDF files are allowed.");
        }

        if ($this->file['size'] > self::MAX_FILE_SIZE) {
            die("Error: File is too large. Maximum allowed size is 10MB.");
        }
    }
 
    public function uploadFile() {
        if (!file_exists(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0777, true);
        }

        $filename = basename($this->file['name']);
        $uploadPath = self::UPLOAD_DIR . $filename;

        if (!move_uploaded_file($this->file['tmp_name'], $uploadPath)) {
            die("Error: Failed to move the uploaded file.");
        }

        return $uploadPath;
    }
 
    public function insertSubmission($uploadPath) {
        $query = "INSERT INTO submissions (student_id, course_id, assignment_id, file_path, submitted_at) 
                  VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Error: Database query preparation failed: " . $this->conn->error);
        }

        $stmt->bind_param("siis", $this->student_id, $this->course_id, $this->assignment_id, $uploadPath);
        if (!$stmt->execute()) {
            die("Error: Failed to insert submission into database: " . $stmt->error);
        }
    }
 
    public function submitAssignment() {
        $this->validateFile();
        $uploadPath = $this->uploadFile();
        $this->insertSubmission($uploadPath);
        echo "<script>alert('Assignment submitted successfully!');</script>";
    }
}
 
$student_id = $_SESSION['student_id'];
$student = new Student($conn, $student_id);
 
if (!isset($_POST['course_id'], $_POST['assignment_id'], $_FILES['submission_file'])) {
    die("Error: Missing course_id, assignment_id, or file.");
}

$course_id = intval($_POST['course_id']);
$assignment_id = intval($_POST['assignment_id']);
$file = $_FILES['submission_file'];
 
$assignmentSubmission = new AssignmentSubmission($conn, $student_id, $course_id, $assignment_id, $file);
$assignmentSubmission->submitAssignment();
 
header("Location: course_content.php?course_id=" . $course_id);
exit;

?>
