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

    
    public function getStudentDetails($student_id) {
        $query = "SELECT * FROM students WHERE student_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $student_id); 
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

   
    public function updateStudentDetails($student_id, $email, $address, $phone, $age) {
        $query = "UPDATE students SET email = ?, address = ?, phone = ?, age = ? WHERE student_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssis", $email, $address, $phone, $age, $student_id);
        return $stmt->execute();
    }

    
    public function closeConnection() {
        $this->conn->close();
    }
}

class Student {
    private $db;

    
    public function __construct() {
        $this->db = new Database();
    }

   
    public function fetchStudent($student_id) {
        return $this->db->getStudentDetails($student_id);
    }

   
    public function updateStudent($student_id, $email, $address, $phone, $age) {
        return $this->db->updateStudentDetails($student_id, $email, $address, $phone, $age);
    }

    
    public function close() {
        $this->db->closeConnection();
    }
}
 
$studentObj = new Student();
 
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $student = $studentObj->fetchStudent($student_id);

    if (!$student) {
        echo "Student not found.";
        exit;
    }
 
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $age = $_POST['age'];
 
        if ($studentObj->updateStudent($student_id, $email, $address, $phone, $age)) {
            echo "<script>alert('Student details updated successfully!')</script>";
        } else {
            echo "Error updating student details.";
        }
    }
} else {
    echo "Student ID not provided.";
    exit;
}


$studentObj->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Details</title>
    <link rel="stylesheet" href="edit_details1.css">
</head>
<body>
<nav>
        <div class="logo">E-Learning</div>
        <ul class="nav-links">
            <li><a href="new_courses.php">New Courses</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="\elearning/logout.php">Logout</a></li>
        </ul>
    </nav>

    <h1>Edit Student Details</h1>

    <div class="container">
        <form method="POST" action="">

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($student['address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($student['age']); ?>" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Update Details">
            </div>

        </form>
    </div>

</body>
</html>
