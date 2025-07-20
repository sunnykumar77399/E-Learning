<?php
session_start();
require 'C:\xampp\htdocs\Elearning\connection.php';

class TeacherDashboard {
    private $conn;
    private $teacher_id;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->teacher_id = $_SESSION['teacher_id'];
    }

    public function embedVideo($course_id, $video_url) {
        $insertQuery = "INSERT INTO videos (course_id, video_url) VALUES (?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param('is', $course_id, $video_url);
        return $stmt->execute();
    }

    public function uploadPdf($course_id, $pdfFileName) {
        $insertPdfQuery = "INSERT INTO assignments (course_id, pdf_document) VALUES (?, ?)";
        $stmt = $this->conn->prepare($insertPdfQuery);
        $stmt->bind_param('is', $course_id, $pdfFileName);
        return $stmt->execute();
    }

    public function getCourses() {
        $courseQuery = "SELECT * FROM courses WHERE teacher_id = '$this->teacher_id'";
        $result = $this->conn->query($courseQuery);
        return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAssignments() {
        $sql = "SELECT a.assignment_title, a.assignment_id, c.course_name, a.deadline, a.pdf_document
                FROM assignments a
                JOIN courses c ON a.course_id = c.id
                WHERE a.teacher_id = ? 
                ORDER BY a.deadline DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $this->teacher_id);
        $stmt->execute();
        $assignmentsResult = $stmt->get_result();
        return $assignmentsResult->fetch_all(MYSQLI_ASSOC);
    }

    public function getNotes() {
        $notesQuery = "SELECT notes.pdf_name, notes.pdf_path, courses.course_name 
                       FROM notes 
                       INNER JOIN courses ON notes.course_id = courses.id 
                       WHERE courses.teacher_id = '$this->teacher_id'";
        $notesResult = $this->conn->query($notesQuery);
        return ($notesResult && $notesResult->num_rows > 0) ? $notesResult->fetch_all(MYSQLI_ASSOC) : [];
    }
 

    public function getVideos() {
        $videos = []; 
        $sql = "SELECT v.course_id, c.course_name, v.video_url 
                FROM videos v
                JOIN courses c ON v.course_id = c.id
                WHERE c.teacher_id = '$this->teacher_id'"; 
        $result = $this->conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($video = $result->fetch_assoc()) {
                $videos[] = $video;
            }
        }
        return $videos;
    }
     
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacherDashboard = new TeacherDashboard($conn);

    if (isset($_POST['course_id'], $_POST['videoUrl'])) {
        $course_id = intval($_POST['course_id']);
        $video_url = filter_var($_POST['videoUrl'], FILTER_SANITIZE_URL);
        if ($teacherDashboard->embedVideo($course_id, $video_url)) {
            $_SESSION['message'] = "Video embedded successfully!";
        } else {
            $_SESSION['error'] = "Failed to embed video.";
        }
    }

    if (isset($_FILES['pdfFile'])) {
        $pdfFile = $_FILES['pdfFile'];
        $uploadDirectory = 'uploads/';
        $uploadFile = $uploadDirectory . basename($pdfFile['name']);
        if (move_uploaded_file($pdfFile['tmp_name'], $uploadFile)) {
            $course_id = intval($_POST['course_id']);
            $pdfFileName = basename($pdfFile['name']);
            if ($teacherDashboard->uploadPdf($course_id, $pdfFileName)) {
                $_SESSION['message'] = "PDF uploaded successfully!";
            } else {
                $_SESSION['error'] = "Failed to upload PDF.";
            }
        } else {
            $_SESSION['error'] = "There was an error uploading the file.";
        }
    }

    // $teacherDashboard->closeConnection();
}

$teacherDashboard = new TeacherDashboard($conn);
$course = $teacherDashboard->getCourses();
$assignments = $teacherDashboard->getAssignments();
$notes = $teacherDashboard->getNotes();
$videos = $teacherDashboard->getVideos();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        td {
            background-color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .create-assignment-btn {
            background-color: #ff5722; 
            color: white;
            padding: 12px 25px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .message {
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
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

        nav .nav-links li {
            position: relative;
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

            nav .nav-links li {
                width: 100%;
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
    <div class="dashboard">
    <nav>
        <div class="logo">E-Learning</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="about.php">About</a></li> 
            <li><a href="\elearning/logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <section id="createCourse">
            <center><h2>Create Course</h2></center>
            <form action="save_course.php" method="POST">
                <label for="course_name">Course Name:</label>
                <input type="text" id="course_name" name="course_name" required>
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
                <button type="submit">Create Course</button>
            </form>
        </section>

        <section id="deleteCourse">
            <center><h2>Delete Course</h2></center>
            <form action="delete_course.php" method="POST">
                <label for="courseIdToDelete">Select Course to Delete:</label>
                <select name="courseIdToDelete" id="courseIdToDelete" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($course as $courses): ?>
                        <option value="<?php echo htmlspecialchars($courses['id']); ?>">
                            <?php echo htmlspecialchars($courses['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Delete Course</button>
            </form>
        </section>

        <section id="embedVideo">
            <center><h2>Embed Video URL</h2></center>
            <form action="" method="POST">
                <label for="course_id">Select Course:</label>
                <select name="course_id" id="course_id" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($course as $courses): ?>
                        <option value="<?php echo htmlspecialchars($courses['id']); ?>">
                            <?php echo htmlspecialchars($courses['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="videoUrl">Video URL:</label>
                <input type="url" id="videoUrl" name="videoUrl" placeholder="Enter Video URL" required>
                <button type="submit">Embed Video</button>
            </form>

            <h3>Existing Videos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Video URL</th>
                        <th>Watch</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($video['course_name']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($video['video_url']); ?>" target="_blank">Watch</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section id="uploadPdf">
            <center><h2>Upload Notes</h2></center>
            <form action="upload_notes.php" method="POST" enctype="multipart/form-data">
                <label for="course_id">Select Course:</label>
                <select name="course_id" id="course_id" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($course as $courses): ?>
                        <option value="<?php echo htmlspecialchars($courses['id']); ?>">
                            <?php echo htmlspecialchars($courses['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="pdfFile">Upload PDF:</label>
                <input type="file" name="pdfFile" id="pdfFile" accept="application/pdf" required>
                <button type="submit">Upload PDF</button>
            </form>

            <center><h2>PDF Notes</h2></center>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Notes Title</th>
                        <th>Course Name</th>
                        <th>PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($notes)): ?>
                        <?php foreach ($notes as $note): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($note['pdf_name']); ?></td>
                                <td><?php echo htmlspecialchars($note['course_name']); ?></td>
                                <td>
                                    <?php if (!empty($note['pdf_path'])): ?>
                                        <a href="<?php echo htmlspecialchars($note['pdf_path']); ?>" target="_blank">View PDF</a>
                                    <?php else: ?>
                                        No PDF uploaded
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No assignments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table><br><br>

        </section>

        <section id="Assignments">
            <center><h2>Assignments</h2></center>
            <table>
                <thead>
                    <tr>
                        <th>Assignment Title</th>
                        <th>Course Name</th>
                        <th>Deadline</th>
                        <th>PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assignments)): ?>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['assignment_title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($assignment['deadline'])); ?></td>
                                <td>
                                    <?php if (!empty($assignment['pdf_document'])): ?>
                                        <a href="<?php echo htmlspecialchars($assignment['pdf_document']); ?>" target="_blank">View PDF</a>
                                    <?php else: ?>
                                        No PDF uploaded
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No assignments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="assignment_creation.php">
                <button type="button" class="create-assignment-btn">Create New Assignment</button>
            </a>
        </section>
    </main>
</div>
</body>
</html>
