<?php

session_start();


require 'C:\xampp\htdocs\Elearning\connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    
    
    $assignmentsQuery = "SELECT assignment_title, pdf_document, deadline FROM assignments WHERE course_id = $course_id";
    $notesQuery = "SELECT pdf_name, pdf_path FROM notes WHERE course_id = $course_id";
    $videosQuery = "SELECT video_url FROM videos WHERE course_id = $course_id";
    
    $assignmentsResult = $conn->query($assignmentsQuery);
    $notesResult = $conn->query($notesQuery);
    $videosResult = $conn->query($videosQuery);

    
    $assignments = $assignmentsResult->fetch_all(MYSQLI_ASSOC);
    $notes = $notesResult->fetch_all(MYSQLI_ASSOC);
    $videos = $videosResult->fetch_all(MYSQLI_ASSOC);

    
    echo json_encode(['assignments' => $assignments, 'notes' => $notes, 'videos' => $videos]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Platform</title>
    <!-- <link rel="stylesheet" href="index.css"> -->
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        nav {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav .logo {
            font-size: 24px;
            font-weight: bold;
            float: left;
        }

        nav .nav-links {
            list-style: none;
            float: right;
            margin: 0;
        }

        nav .nav-links li {
            display: inline;
            margin: 0 10px;
        }

        nav .nav-links a {
            color: white;
            text-decoration: none;
            padding: 5px;
        }

        nav .nav-links a:hover {
            background-color: #444;
            border-radius: 4px;
        }

        nav .login-btn {
            float: right;
            background-color: #4CAF50;
            color: white;
            padding: 8px 20px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            margin-left: 10px;
        }

        nav .login-btn:hover {
            background-color: #45a049;
        }

        .hero {
            background-image: url('https://images.unsplash.com/photo-1698993026848-f67c1eb7f989?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8ZWR1Y2F0aW9uJTIwYmFja2dyb3VuZHxlbnwwfHwwfHx8MA%3D%3D');
            background-size: cover;
            background-position: center;
            height: 400px;
            color: white;
            text-align: center;
            padding: 80px 20px;
        }

        .hero .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
        }

        .course-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .course-card {
            background-color: white;
            padding: 20px;
            margin: 10px;
            width: 250px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-10px);
        }

        .enroll-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .enroll-btn:hover {
            background-color: #45a049;
        }

        .details-container {
            margin-top: 20px;
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .details-container.show {
            display: block;
            opacity: 1;
        }

        .section-btn {
            margin: 5px;
            padding: 10px;
            background-color: #f0f0f0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .section-btn:hover {
            background-color: #ddd;
        }

        .sections {
            display: none;
            margin-top: 15px;
        }

        .sections p {
            padding: 5px;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin: 5px 0;
        }

        .section-content a {
            color: #4CAF50;
            text-decoration: none;
        }

        .section-content a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">E-Learning</div>
        
        <a href="login.php" class="login-btn">Login</a>
    </nav>

    <div class="hero">
        <div class="overlay">
            <p>Explore our courses and start learning today!</p>
        </div>
    </div>

    <div class="course-container" id="courseContainer">
        <?php
    
        $sql = "SELECT id, course_name FROM courses ORDER BY course_name";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            
            while ($row = $result->fetch_assoc()) {
                echo '<div class="course-card">';
                echo '<h3>' . htmlspecialchars($row['course_name']) . '</h3>';
                echo '<p>Description of course</p>';

            
                if (!isset($_SESSION['user_id'])) {
                    echo '<button class="enroll-btn" onclick="alertLogin()">Enroll</button>';
                } else {
                    echo '<button class="enroll-btn" onclick="enrollCourse(' . $row['id'] . ')">Enroll</button>';
                }

                echo '<div class="details-container" id="details-' . $row['id'] . '">';
                echo '<div class="section-btn" onclick="showSection(\'resources\', ' . $row['id'] . ')">Resources</div>';
                echo '<div class="section-btn" onclick="showSection(\'assignments\', ' . $row['id'] . ')">Assignments</div>';
                echo '<div class="section-btn" onclick="showSection(\'videos\', ' . $row['id'] . ')">Videos</div>';
                echo '<div class="sections" id="resources-' . $row['id'] . '"></div>';
                echo '<div class="sections" id="assignments-' . $row['id'] . '"></div>';
                echo '<div class="sections" id="videos-' . $row['id'] . '"></div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p style="text-align: center;">No courses available at the moment.</p>';
        }

        $conn->close();
        ?>
    </div>

    <footer>
        <div class="footer-section">
            <h2>What Our Students Say</h2>
            <p class="student-testimonial">"This platform has transformed my learning experience!" - Manas</p>
            <p class="student-testimonial">"I love the variety of courses available!" - virat</p>
            <p class="student-testimonial">"The instructors are knowledgeable and very helpful!" - Mohit</p>
        </div>
        <div class="footer-section">
            <h2>About Us</h2>
            <p>We are dedicated to providing the best online learning experience. Our platform offers a wide range of courses designed to help you achieve your educational and career goals.</p>
        </div>
    </footer>

    <script>
        function enrollCourse(courseId) {
        
            var detailsSection = document.getElementById('details-' + courseId);
            detailsSection.classList.add('show');
        }

        function alertLogin() {
            alert("You need to login to enroll in courses.");
            window.location.href = "login.php";  
        }

        function showSection(section, courseId) {
            
            var sections = document.querySelectorAll('.sections');
            sections.forEach(function(section) {
                section.style.display = 'none';
            });

            
            document.getElementById(section + '-' + courseId).style.display = 'block';

            
            fetchCourseData(courseId, section);
        }

        function fetchCourseData(courseId, section) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `course_id=${courseId}`
            })
            .then(response => response.json())
            .then(data => {
                var sectionContent = '';
                if (section === 'assignments') {
                    data.assignments.forEach(function(assignment) {
                        if (assignment.pdf_path) {
                            sectionContent += `<p><a href="${assignment.pdf_path}" target="_blank">${assignment.assignment_title}</a> - Deadline: ${assignment.deadline}</p>`;
                        } else {
                            sectionContent += `<p>${assignment.assignment_title} - Deadline: ${assignment.deadline}</p>`;
                        }
                    });
                } else if (section === 'resources') {
                    data.notes.forEach(function(note) {
                        if (note.pdf_path) {
                            sectionContent += `<p><a href="${note.pdf_path}" target="_blank">${note.pdf_name}</a></p>`;
                        }
                    });
                } else if (section === 'videos') {
                    data.videos.forEach(function(video) {
                        sectionContent += `<p><a href="${video.video_url}" target="_blank">${video.video_url}</a></p>`;
                    });
                }

                document.getElementById(section + '-' + courseId).innerHTML = sectionContent;
            })
            .catch(error => console.error('Error fetching course data:', error));
        }
    </script>
</body>
</html>
