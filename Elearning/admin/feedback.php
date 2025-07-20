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

    
    public function getAllFeedback() {
        $query = "SELECT * FROM feedback";
        $result = $this->conn->query($query);
        $feedback = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $feedback[] = $row;
            }
        }

        return $feedback;
    }

    
    public function deleteFeedback($id) {
        $query = "DELETE FROM feedback WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}


$db = new Database();
$conn = $db->connect();


if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $db->deleteFeedback($delete_id);
    header("Location: feedback.php"); 
    exit();
}


$feedback = $db->getAllFeedback();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        nav {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }

        nav .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav .nav-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        nav .nav-links li {
            display: inline;
            margin-right: 20px;
        }

        nav .nav-links li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .delete-btn {
            color: red;
            text-decoration: none;
            font-size: 14px;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">E-Learning Admin</div>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="\elearning/logout.php">Logout</a></li>
            
        </ul>
    </nav>

    <main>
        <h1>Student Feedback</h1>

        <div class="container">

            <?php if (empty($feedback)): ?>
                <p>No feedback available.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Student ID</th>
                        <th>Feedback</th>
                        <th>Rating</th>
                        <th>Submitted On</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($feedback as $item): ?>
                        <tr>
                            <td><?php echo $item['student_id']; ?></td>
                            <td><?php echo $item['feedback_text']; ?></td>
                            <td><?php echo $item['rating']; ?></td>
                            <td><?php echo $item['created_at']; ?></td>
                            <td>
                                <a href="?delete_id=<?php echo $item['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this feedback?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>
