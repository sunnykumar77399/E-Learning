<?php
class Database {
    private $host = "localhost";
    private $db_name = "elearning";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    public function getAllStudents() {
        $query = "SELECT student_id, password, age, email, phone, address, created_at FROM students";
        $result = $this->conn->query($query);
        $students = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }
        return $students;
    }

    public function getAllTeachers() {
        $query = "SELECT teacher_id, password, age, gender, phone, email, address FROM teacher";
        $result = $this->conn->query($query);
        $teachers = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $teachers[] = $row;
            }
        }
        return $teachers;
    }

   
   public function getAllMessages() {
    $query = "SELECT c.id, c.student_id, c.email, c.subject, c.message, c.created_at 
              FROM contact c 
              INNER JOIN students s ON c.student_id = s.student_id"; 
    $result = $this->conn->query($query);
    $messages = [];

    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    } else {
        
        $messages = [];
    }
    return $messages;
}



    public function deleteTeacher($teacher_id) {
        $query = "DELETE FROM teacher WHERE teacher_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $teacher_id);
        $stmt->execute();
    }
}
?>
