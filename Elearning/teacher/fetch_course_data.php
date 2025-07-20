<?php
session_start();


require 'C:\xampp\htdocs\Elearning\connection.php';

function fetchCourseData($courseId, $conn) {
   
    $assignmentsSql = "SELECT assignment_title, deadline FROM assignments WHERE course_id = '2'";
    $stmt1 = $conn->prepare($assignmentsSql);
    $stmt1->bind_param('i', $courseId);
    $stmt1->execute();
    $assignments = $stmt1->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt1->close();

    
    $notesSql = "SELECT pdf_name, pdf_path FROM notes WHERE course_id = $courseId";
    $stmt2 = $conn->prepare($notesSql);
    $stmt2->bind_param('i', $courseId);
    $stmt2->execute();
    $notes = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();

    
    $videosSql = "SELECT video_url FROM videos WHERE course_id = $courseId";
    $stmt3 = $conn->prepare($videosSql);
    $stmt3->bind_param('i', $courseId);
    $stmt3->execute();
    $videos = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt3->close();

    return [
        'assignments' => $assignments ?: [],
        'notes' => $notes ?: [],
        'videos' => $videos ?: [],
    ];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
    if ($courseId) {
        $data = fetchCourseData($courseId, $conn);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Invalid Course ID']);
    }
    exit;
}


$coursesSql = "SELECT id, course_name FROM courses";
$coursesResult = $conn->query($coursesSql);
$courses = $coursesResult->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .course-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .course-card {
            padding: 20px;
            background-color: #ff5722;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .course-card:hover {
            background-color: #e64a19;
        }

        #course-details {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin: 5px 0;
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Courses</h1>
    <div class="course-cards">
        <?php foreach ($courses as $course): ?>
            <div class="course-card" data-course-id="<?= htmlspecialchars($course['id']) ?>">
                <?= htmlspecialchars($course['course_name']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="course-details">
        <h2>Assignments</h2>
        <table id="assignments-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <h2>Resources</h2>
        <ul id="pdf-list"></ul>

        <h2>Videos</h2>
        <ul id="video-list"></ul>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const courseCards = document.querySelectorAll(".course-card");
            const assignmentsTable = document.querySelector("#assignments-table tbody");
            const pdfList = document.querySelector("#pdf-list");
            const videoList = document.querySelector("#video-list");

            courseCards.forEach((card) => {
                card.addEventListener("click", function () {
                    const courseId = card.getAttribute("data-course-id");

                   
                    assignmentsTable.innerHTML = "";
                    pdfList.innerHTML = "";
                    videoList.innerHTML = "";

                    
                    fetch("", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `course_id=${courseId}`,
                    })
                        .then((response) => response.json())
                        .then((data) => {
                           
                            if (data.error) {
                                alert(data.error);
                                return;
                            }

                           
                            data.assignments.forEach((assignment) => {
                                const row = document.createElement("tr");
                                row.innerHTML = `
                                    <td>${assignment.assignment_title}</td>
                                    <td>${new Date(assignment.deadline).toLocaleDateString()}</td>
                                `;
                                assignmentsTable.appendChild(row);
                            });

                           
                            data.notes.forEach((note) => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `<a href="${note.pdf_path}" target="_blank">${note.pdf_name}</a>`;
                                pdfList.appendChild(listItem);
                            });

                           
                            data.videos.forEach((video) => {
                                const listItem = document.createElement("li");
                                listItem.innerHTML = `<a href="${video.video_url}" target="_blank">${video.video_url}</a>`;
                                videoList.appendChild(listItem);
                            });
                        })
                        .catch((error) => {
                            console.error("Error fetching course data:", error);
                        });
                });
            });
        });
    </script>
</body>
</html>
