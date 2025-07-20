<?php
$login = false;
$showError = false;
require 'C:\xampp\htdocs\Elearning\connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql_teacher = "SELECT * FROM teacher WHERE teacher_id='$username'";
    $result_teacher = mysqli_query($conn, $sql_teacher);
    $teacher_data = mysqli_fetch_assoc($result_teacher);

    $sql_student = "SELECT * FROM students WHERE student_id='$username'";
    $result_student = mysqli_query($conn, $sql_student);
    $student_data = mysqli_fetch_assoc($result_student);

    $sql_admin = "SELECT * FROM admin WHERE admin_id='$username'";
    $result_admin = mysqli_query($conn, $sql_admin);
    $admin_data = mysqli_fetch_assoc($result_admin);

    if ($teacher_data && password_verify($password, $teacher_data['password'])) {
        $login = true;
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['teacher_id'] = $username;
        $_SESSION['teacher'] = true;
        header("Location: teacher/dashboard.php");
        exit();
    } elseif ($student_data && password_verify($password, $student_data['password'])) {
        $login = true;
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['student_id'] = $username;
        $_SESSION['student'] = true;
        header("Location: student/dashboard.php");
        exit();
    } elseif ($admin_data) {
        $login = true;
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['admin_id'] = $username;
        $_SESSION['admin'] = true;
        header("Location: admin/admin_dashboard.php");
        exit();
    } else {
        $showError = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .navbar {
            width: 100%;
            background-color: #004d40;
            color: #fff;
            padding: 1rem;
            text-align: center;
            position: absolute;
            top: 0;
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .alert {
            width: 90%;
            max-width: 400px;
            margin: 1rem auto;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .login-container {
            width: 90%;
            max-width: 400px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .login-container button {
            width: 100%;
            padding: 0.75rem;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-container button:hover {
            background-color: #218838;
        }

        .register-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .register-buttons a {
            text-decoration: none;
            flex: 1;
            margin: 0 5px;
        }

        .register-buttons button {
            width: 100%;
            padding: 0.75rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .register-buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">Online Elearning Platform</div>
    </nav>

    <?php 
        if ($login) {
            echo "<div class='alert alert-success'>You are logged in</div>";
        } elseif ($showError) {
            echo "<div class='alert alert-danger'>$showError</div>";
        }
    ?>
    
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST" id="loginForm">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form><br>
        <h3><center>Register :</center></h3>
        <div class="register-buttons"> 
            <a href="student/register.php">
                <button type="button">Student</button>
            </a>
            <a href="teacher/register_teacher1.php">
                <button type="button">Teacher</button>
            </a>
        </div>
    </div>
</body>
</html>
