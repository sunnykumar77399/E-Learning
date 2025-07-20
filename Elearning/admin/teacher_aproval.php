<?php
session_start();
require 'C:\xampp\htdocs\Elearning\connection.php';
include_once 'Database.php';

class TeacherApproval {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllPendingRequests() {
        $query = "SELECT * FROM teacher_requests";
        $result = $this->conn->query($query);
        $requests = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
        }
        return $requests;
    }

    public function approveTeacher($teacherId) {
        $query = "SELECT * FROM teacher_requests WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $teacherId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
 
            $insertQuery = "INSERT INTO teacher (teacher_id, password, email, address, phone, gender, age)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bind_param(
                "ssssssi",
                $row['teacher_id'],
                $row['password'],
                $row['email'],
                $row['address'],
                $row['phone'],
                $row['gender'],
                $row['age']
            );

            if ($stmt->execute()) { 
                $deleteQuery = "DELETE FROM teacher_requests WHERE teacher_id = ?";
                $stmt = $this->conn->prepare($deleteQuery);
                $stmt->bind_param("s", $teacherId);
                $stmt->execute();
                return true;
            }
        }
        return false;
    }

    public function rejectTeacher($teacherId) {
        $query = "DELETE FROM teacher_requests WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $teacherId);
        return $stmt->execute();
    }
}
 
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}
 
$teacherApproval = new TeacherApproval();
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve'])) {
        $teacherId = $_POST['teacher_id'];
        if ($teacherApproval->approveTeacher($teacherId)) {
            echo "<script>
                    alert('Teacher approved and added to the teacher database!');
                    window.location.href = 'admin_dashboard.php';
                  </script>";
        } else {
            echo '<script>alert("Failed to approve the teacher.");</script>';
        }
    } elseif (isset($_POST['reject'])) {
        $teacherId = $_POST['teacher_id'];
        if ($teacherApproval->rejectTeacher($teacherId)) {
            echo '<script>alert("Teacher request rejected and deleted from the database.");</script>';
        } else {
            echo '<script>alert("Failed to reject the teacher request.");</script>';
        }
    }
}
 
$pendingRequests = $teacherApproval->getAllPendingRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Approval</title>
    <link rel="stylesheet" href="teacher_aproval.css">
</head>
<body>

    <h1>Admin Dashboard</h1>
    <h2>Pending Teacher Applications</h2>
    <table>
        <thead>
            <tr>
                <th>Teacher ID</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Certification</th>
                <th colspan="2"><center>Action</center></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pendingRequests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request['teacher_id']) ?></td>
                    <td><?= htmlspecialchars($request['address']) ?></td>
                    <td><?= htmlspecialchars($request['phone']) ?></td>
                    <td><?= htmlspecialchars($request['gender']) ?></td>
                    <td><?= htmlspecialchars($request['age']) ?></td>
                    <td>
                        <a href="/Elearning/teacher/<?= htmlspecialchars($request['certification_pdf']) ?>" target="_blank">View Certification</a>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="teacher_id" value="<?= htmlspecialchars($request['teacher_id']) ?>">
                            <button type="submit" name="approve">Approve</button>
                        </form>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="teacher_id" value="<?= htmlspecialchars($request['teacher_id']) ?>">
                            <button type="submit" name="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
