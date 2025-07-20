<?php
session_start();

class AssignmentCreator {
    private $conn;

    public function __construct($host, $username, $password, $dbname) {
        $this->conn = new mysqli($host, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getCoursesByTeacher($teacherId) {
        $teacherId = $this->conn->real_escape_string($teacherId);
        $query = "SELECT id, course_name FROM courses WHERE teacher_id = '$teacherId'";
        $result = $this->conn->query($query);

        $courses = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
        }
        return $courses;
    }

    public function createAssignment($courseId, $assignmentTitle, $pdfFile, $pdfTmpName) {
        if (!isset($_SESSION['teacher_id'])) {
            die("Unauthorized: Teacher not logged in.");
        }

        $teacherId = $_SESSION['teacher_id'];
        if ($pdfFile['type'] !== 'application/pdf') {
            die("Error: Only PDF files are allowed.");
        }

        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($pdfFile['name']);
        $targetFilePath = $targetDir . $fileName;
        if (!move_uploaded_file($pdfTmpName, $targetFilePath)) {
            die("Error: Failed to upload the PDF file.");
        }

        $deadline = date('Y-m-d H:i:s', strtotime('+1 minutes'));
        $assignmentTitle = $this->conn->real_escape_string($assignmentTitle);
        $courseId = $this->conn->real_escape_string($courseId);
        $teacherId = $this->conn->real_escape_string($teacherId);

        $insertQuery = "INSERT INTO assignments (assignment_title, pdf_document, deadline, course_id, teacher_id) 
                        VALUES ('$assignmentTitle', '$targetFilePath', '$deadline', '$courseId', '$teacherId')";

        if ($this->conn->query($insertQuery) === TRUE) {
            echo "<script>alert('Assignment created successfully.');</script>";
        } else {
            echo "<p class='error'>Error: " . $this->conn->error . "</p>";
        }
    }

    public function removeExpiredAssignments() {
        $currentTime = date('Y-m-d H:i:s');
        $deleteQuery = "DELETE FROM assignments WHERE deadline < '$currentTime'";
        if ($this->conn->query($deleteQuery) === FALSE) {
            die("Error removing expired assignments: " . $this->conn->error);
        }
    }

    public function __destruct() {
        $this->conn->close();
    }
}

$assignmentCreator = new AssignmentCreator("localhost", "root", "", "elearning");

if (!isset($_SESSION['teacher_id'])) {
    die("Unauthorized: Teacher not logged in.");
}

$teacherId = $_SESSION['teacher_id'];
$assignmentCreator->removeExpiredAssignments();
$courses = $assignmentCreator->getCoursesByTeacher($teacherId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = $_POST['course_id'];
    $assignmentTitle = htmlspecialchars($_POST['assignment_title'], ENT_QUOTES);
    $pdfFile = $_FILES['pdf_document'];
    $pdfTmpName = $_FILES['pdf_document']['tmp_name'];
    $assignmentCreator->createAssignment($courseId, $assignmentTitle, $pdfFile, $pdfTmpName);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8e1;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #ff5722;
            margin-top: 30px;
        }
        form {
            background-color: #fff3e0;
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 1.1rem;
            color: #ff7043;
            margin-bottom: 5px;
            display: inline-block;
        }
        select, input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ffcc80;
            border-radius: 5px;
            font-size: 1rem;
            background-color: #fff3e0;
            color: #333;
        }
        button[type="submit"] {
            background-color: #ff5722;
            color: white;
            padding: 12px 25px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #e64a19;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        nav .logo {
            font-size: 1.5rem;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        nav .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        nav .nav-links a {
            text-decoration: none;
            color: #fff;
            font-size: 1rem;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        nav .nav-links a:hover {
            background-color: #ff5722;
            color: #fff;
        }
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }
            nav .nav-links {
                flex-direction: column;
                width: 100%;
                padding: 0;
            }
            nav .nav-links a {
                display: block;
                width: 100%;
                text-align: left;
            }
        }
    </style>
</head>
<body>
<nav>
    <div class="logo">E-Learning</div>
    <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="/elearning/logout.php">Logout</a></li>
    </ul>
</nav>
<h1>Create Assignment</h1>
<form action="" method="POST" enctype="multipart/form-data">
    <label for="course_id">Select Course:</label>
    <select id="course_id" name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <label for="assignment_title">Assignment Title:</label>
    <input type="text" id="assignment_title" name="assignment_title" required>
    <label for="pdf_document">Upload PDF:</label>
    <input type="file" id="pdf_document" name="pdf_document" accept="application/pdf" required>
    <button type="submit">Create Assignment</button>
</form>
</body>
</html>
