<?php
 require 'C:\xampp\htdocs\Elearning\connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $passwd = $_POST['passwd'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    
    $errors = [];

    if (!preg_match('/^[a-zA-Z0-9]{5,}$/', $username)) {
        $errors[] = "Username must be at least 5 characters long and contain only letters and numbers.";
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $passwd)) {
        $errors[] = "Password must be at least 8 characters long, contain one letter and one number.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match('/^\d{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    if (!is_numeric($age) || $age < 18 || $age > 100) {
        $errors[] = "Age must be a number between 18 and 100.";
    }

    if (empty($errors)) {
        $passwdHash = password_hash($passwd, PASSWORD_BCRYPT);

        
        $sql = "INSERT INTO teacher (teacher_id, password, age, phone, email, gender, address) 
                VALUES ('$username', '$passwdHash', '$age', '$phone', '$email', '$gender', '$address')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Teacher Register Successfully.');</script>";
            header("Location:\Elearning/login.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}


$conn->close();
?>
