<?php
session_start();
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'elearning';
    private $conn;

   
    public function __construct() {
        $this->connect();
    }

  
    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getTeacherDetails($teacher_id) {
        $query = "SELECT * FROM teacher WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

   
    public function updateTeacherDetails($teacher_id, $email, $address, $phone, $age) {
        $query = "UPDATE teacher SET email = ?, address = ?, phone = ?, age = ? WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssis", $email, $address, $phone, $age, $teacher_id);
        return $stmt->execute();
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

class Teacher {
    private $db;

    
    public function __construct() {
        $this->db = new Database();
    }

   
    public function fetchTeacher($teacher_id) {
        return $this->db->getTeacherDetails($teacher_id);
    }

    
    public function updateTeacher($teacher_id, $email, $address, $phone, $age) {
        return $this->db->updateTeacherDetails($teacher_id, $email, $address, $phone, $age);
    }

  
    public function close() {
        $this->db->closeConnection();
    }
}


$teacherObj = new Teacher();


if (isset($_SESSION['teacher_id'])) {
    $teacher_id = $_SESSION['teacher_id'];
    $teacher = $teacherObj->fetchTeacher($teacher_id);

    if (!$teacher) {
        echo "Teacher not found.";
        exit;
    }

 
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $age = $_POST['age'];

       
        if ($teacherObj->updateTeacher($teacher_id, $email, $address, $phone, $age)) {
            echo "<script>alert('Teacher details updated successfully!')</script>";
        } else {
            echo "Error updating teacher details.";
        }
    }
} else {
    echo "Teacher ID not provided.";
    exit;
}


$teacherObj->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher Details</title>
    <link rel="stylesheet" href="edit_details1.css">
</head>
<body>
<nav>
    <div class="logo">E-Learning</div>
    <ul class="nav-links"> 
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="\elearning/logout.php">Logout</a></li>
    </ul>
</nav>

<h1>Edit Teacher Details</h1>

<div class="container">
    <form method="POST" action="">

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($teacher['address']); ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($teacher['phone']); ?>" required>
        </div>

        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($teacher['age']); ?>" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Update Details">
        </div>

    </form>
</div>

</body>
</html>
