<?php
require 'C:\xampp\htdocs\Elearning\connection.php';

class TeacherRegistration {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function isDuplicate($teacher_id, $email) {
        $query = "SELECT 1 FROM teacher_requests WHERE teacher_id = ? OR email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $teacher_id, $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function saveCertification($file) {
        $target_dir = "/uploads/certifications/"; 
        $target_file = $target_dir . basename($file['name']);

       
        if (!file_exists(__DIR__ . $target_dir)) {
            mkdir(__DIR__ . $target_dir, 0755, true);
        }

        
        if (move_uploaded_file($file['tmp_name'], __DIR__ . $target_file)) {
            return $target_file; 
        }

        return false; 
    }

    public function registerTeacher($data) {
        $query = "INSERT INTO teacher_requests 
                  (teacher_id, password, email, phone, age, gender, address, certification_pdf)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $this->conn->error);
        }

        $stmt->bind_param(
            "ssssisss",
            $data['teacher_id'],
            $data['password'],
            $data['email'],
            $data['phone'],
            $data['age'],
            $data['gender'],
            $data['address'],
            $data['certification_pdf'] 
        );

        return $stmt->execute(); 
}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacherRegistration = new TeacherRegistration($conn);

   
    $teacher_id = trim($_POST['teacher_id']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    
    $certification_pdf = $teacherRegistration->saveCertification($_FILES['certification_pdf']);
    if (!$certification_pdf) {
        echo "<script>alert('Error uploading the certification file.');</script>";
        exit;
    }

   
    if ($teacherRegistration->isDuplicate($teacher_id, $email)) {
        echo "<script>alert('Teacher ID or Email already exists.');</script>";
        exit;
    }

    
    $data = [
        'teacher_id' => $teacher_id,
        'password' => $password,
        'email' => $email,
        'phone' => trim($_POST['phone']),
        'age' => intval($_POST['age']),
        'gender' => $_POST['gender'],
        'address' => trim($_POST['address']),
        'certification_pdf' => $certification_pdf
    ];

  
    try {
        if ($teacherRegistration->registerTeacher($data)) {
            echo "<script>alert('Your registration request has been submitted. Wait for admin approval.');</script>";
        } else {
            echo "<script>alert('Error during registration. Please try again.');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Teacher</title>
    <link rel="stylesheet" href="register_teacher.css">
    <style>
        .error {
            color: red;
            font-size: 0.9em;
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
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="teacher_id">Teacher ID:</label>
        <input type="text" id="teacher_id" name="teacher_id" placeholder="Enter Teacher ID" required>
        <span class="error"></span>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter Password" required>
        <span class="error"></span>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter Email Address" required>
        <span class="error"></span>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" placeholder="Enter 10-digit Phone Number" required>
        <span class="error"></span>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" placeholder="Enter Age" required>
        <span class="error"></span>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="address">Address:</label>
        <textarea id="address" name="address" placeholder="Enter Address" required></textarea>
        <span class="error"></span>

        <label for="certification_pdf">Certification PDF:</label>
        <input type="file" id="certification_pdf" name="certification_pdf" required>
        <span class="error"></span>

        <button type="submit">Submit Registration</button>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector("form");

            form.addEventListener("input", (e) => {
                const target = e.target;
                if (target.id === "teacher_id") validateInput(target, /^[a-zA-Z0-9]+$/, "Teacher ID must be alphanumeric.");
                else if (target.id === "password") validateInput(target, /(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}/, "Password must have 6+ characters, including a letter and a number.");
                else if (target.id === "email") validateInput(target, /^[^\s@]+@[^\s@]+\.[^\s@]+$/, "Enter a valid email.");
                else if (target.id === "phone") validateInput(target, /^\d{10}$/, "Phone number must have 10 digits.");
                else if (target.id === "age") validateAge(target);
                else if (target.id === "certification_pdf") validateFile(target);
            });

            function validateInput(input, pattern, message) {
                const error = input.nextElementSibling;
                error.textContent = pattern.test(input.value) ? "" : message;
            }

            function validateAge(input) {
                const error = input.nextElementSibling;
                const age = parseInt(input.value, 10);
                error.textContent = age >= 18 && age <= 65 ? "" : "Age must be between 18 and 65.";
            }

            function validateFile(input) {
                const error = input.nextElementSibling;
                error.textContent = input.files[0]?.name.endsWith(".pdf") ? "" : "Please upload a valid PDF file.";
            }
        });
    </script>
</body>
</html>
