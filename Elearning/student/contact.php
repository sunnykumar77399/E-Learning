<?php

class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'elearning';
    public $conn;

    
    public function __construct() {
        $this->connect();
    }

    
    public function connect() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

   
    public function close() {
        $this->conn->close();
    }
}


class ContactForm {
    private $db;
    private $student_id;
    private $email;
    private $subject;
    private $message;

   
    public function __construct($student_id, $email, $subject, $message) {
        $this->db = new Database(); 
        $this->student_id = $student_id;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }

    
    public function submit() {
        
        $email = $this->db->conn->real_escape_string($this->email);
        $subject = $this->db->conn->real_escape_string($this->subject);
        $message = $this->db->conn->real_escape_string($this->message);

        
        $sql = "INSERT INTO contact (student_id, email, subject, message) VALUES ('$this->student_id', '$email', '$subject', '$message')";

        if ($this->db->conn->query($sql) === TRUE) {
            return "Your message has been sent successfully!";
        } else {
            return "Error: " . $this->db->conn->error;
        }
    }

    
    public function closeConnection() {
        $this->db->close();
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start(); 

    
    $student_id = $_SESSION['student_id']; 
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

   
    $contact = new ContactForm($student_id, $email, $subject, $message);

    
    $response = $contact->submit();
    $contact->closeConnection(); 

    echo "<script>alert('$response');</script>"; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Your Platform Name</title>
    <link rel="stylesheet" href="contact1.css"> 
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background: #007bff;
            color: #ffffff;
            padding: 40px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2.5em;
        }

        main {
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
        }

        .contact-form label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .contact-form input,
        .contact-form textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .contact-form button {
            padding: 10px;
            background: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .contact-form button:hover {
            background: #0056b3;
        }

        footer {
            text-align: center;
            padding: 20px;
            background: #007bff;
            color: #ffffff;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
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
    
    <header>
        <center><h1>Contact Us</h1></center>
    </header>

    <main>
        <form class="contact-form" action="contact.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2023 e-learning platform. All rights reserved.</p>
    </footer>
</body>
</html>
