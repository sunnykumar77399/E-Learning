<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

require 'C:\xampp\htdocs\Elearning\connection.php';
 
class PDFUpload {
    private $conn;
    private $course_id;
    private $pdfFile;

    const UPLOAD_DIRECTORY = 'notes/';
    
    public function __construct($conn, $course_id, $pdfFile) {
        $this->conn = $conn;
        $this->course_id = intval($course_id);
        $this->pdfFile = $pdfFile;
    }
 
    private function uploadFile() {
        if (!is_dir(self::UPLOAD_DIRECTORY)) {
            mkdir(self::UPLOAD_DIRECTORY, 0777, true);
        }

        $uploadFile = self::UPLOAD_DIRECTORY . basename($this->pdfFile['name']);
        if (move_uploaded_file($this->pdfFile['tmp_name'], $uploadFile)) {
            return $uploadFile;
        }

        return false;
    }
 
    private function insertFileInfo($pdfFileName, $pdfFilePath) {
        $pdfFileName = mysqli_real_escape_string($this->conn, $pdfFileName);
        $pdfFilePath = mysqli_real_escape_string($this->conn, $pdfFilePath);

        $insertPdfQuery = "
            INSERT INTO notes (course_id, pdf_name, pdf_path) 
            VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($insertPdfQuery);
        $stmt->bind_param("iss", $this->course_id, $pdfFileName, $pdfFilePath);

        return $stmt->execute();
    }
 
    public function processUpload() {
        $uploadFile = $this->uploadFile();
        if ($uploadFile) {
            $pdfFileName = basename($this->pdfFile['name']);
            if ($this->insertFileInfo($pdfFileName, $uploadFile)) {
                echo "<script>alert('PDF uploaded successfully!'); window.location.href = 'dashboard.php';</script>";
            } else {
                echo "<script>alert('Failed to upload PDF. Please try again.'); window.location.href = 'dashboard.php';</script>";
            }
        } else {
            echo "<script>alert('There was an error uploading the file.'); window.location.href = 'dashboard.php';</script>";
        }
    }
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['pdfFile'], $_POST['course_id'])) {
        $pdfFile = $_FILES['pdfFile'];
        $course_id = $_POST['course_id'];

        $pdfUpload = new PDFUpload($conn, $course_id, $pdfFile);
        $pdfUpload->processUpload();
    } else {
        echo "<script>alert('No file was selected for upload.'); window.location.href = 'dashboard.php';</script>";
    }
}
?>
