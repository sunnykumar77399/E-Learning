<?php

session_start();
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'elearning';

    public function connect() {
        $conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
}


class Feedback {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function submitFeedback($student_id, $feedback_text, $rating) {
       
        $query = "INSERT INTO feedback (student_id, feedback_text, rating) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $student_id, $feedback_text, $rating);

        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully!');</script>";
        } else {
            return "Error: " . $stmt->error;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['student_id'];
    $feedback_text = $_POST['feedback_text'];
    $rating = $_POST['rating'];

    
    $feedback = new Feedback();
    $message = $feedback->submitFeedback($student_id, $feedback_text, $rating);

    
    echo $message;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback</title>
   
</head>
<body>

    <h1>Submit Feedback</h1>

    <form action="" method="POST"> 

        <label for="feedback_text">Feedback:</label><br>
        <textarea id="feedback_text" name="feedback_text" rows="4" cols="50" required></textarea><br><br>

        <label for="rating">Rating (1-5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required><br><br>

        <button type="submit">Submit Feedback</button>
    </form>

</body>
</html>
