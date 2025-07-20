<?php
require 'C:\xampp\htdocs\Elearning\connection.php';

class UserSignup {
    private $conn;
    public $error_message;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function userExists($username, $email, $phone) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE student_id = ? OR email = ? OR phone = ?");
        $stmt->bind_param("sss", $username, $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createUser($username, $password, $address, $email, $phone, $age) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO students (student_id, password, address, email, phone, age) 
                                       VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username, $hashed_password, $address, $email, $phone, $age);
        return $stmt->execute();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $signup = new UserSignup($conn);

    $username = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["username"]));
    $password = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["password"]));
    $address = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["address"]));
    $email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["email"]));
    $phone = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["phone"]));
    $age = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["age"]));

    $existingUser = $signup->userExists($username, $email, $phone);

    if ($existingUser) {
        if ($existingUser['student_id'] == $username) {
            $signup->error_message = "Username already exists.";
        } elseif ($existingUser['email'] == $email) {
            $signup->error_message = "Email already exists.";
        } elseif ($existingUser['phone'] == $phone) {
            $signup->error_message = "Phone number already exists.";
        }
    } else {
        if ($signup->createUser($username, $password, $address, $email, $phone, $age)) {
            echo "<script>
                    alert('Account created successfully!');
                    window.location.href = '/Elearning/login.php'; 
                  </script>";
            exit();
        } else {
            $signup->error_message = "Error creating account. Please try again.";
        }
    }

    if ($signup->error_message) {
        echo "<script>alert('{$signup->error_message}');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="register.css">
    <style>
       
        .error-message {
            color: red;
            font-size: 12px;
        }
    </style>
</head>
<body>
<nav>
        <div class="logo">E-Learning</div>
        <ul class="nav-links">
        
            <li><a href="\elearning/login.php">Login</a></li>
        </ul>
    </nav>
<div class="form-container">
    <h2>Sign Up</h2>
    <form action="" method="POST" id="signupForm">
        <div class="input-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" pattern="[a-zA-Z0-9]{3,20}" 
                   title="Username must be 3-20 characters long and contain only letters and numbers." required>
        </div>
        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" pattern=".{6,}" 
                   title="Password must be at least 6 characters long." required>
        </div>
        <div class="input-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
        </div>
        <div class="input-group">
            <label for="email">Email ID:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="input-group">
            <label for="phone">Phone No:</label>
            <input type="tel" id="phone" name="phone" pattern="\d{10}" 
                   title="Phone number must be exactly 10 digits." required>
        </div>
        <div class="input-group">
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" min="1" title="Please enter a valid age." required>
        </div>
        <button type="submit">Sign Up</button>
    </form>
</div>
</body>
</html>
