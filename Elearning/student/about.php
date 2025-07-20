<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Your Platform Name</title>
    <link rel="stylesheet" href="about1.css">
</head>
<body>
    
    <nav>
        <div class="logo">E-Learning</div>
        <ul class="nav-links">
            <li><a href="new_courses.php">New Courses</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="\elearning/logout.php">Logout</a></li>
        </ul>
    </nav>

    
    <main>
     
        <section class="intro">
            <h2>Who We Are</h2>
            <p>Founded in [2024], E-learning platform was created by a team of passionate educators, technologists, and lifelong learners. We understand that the traditional education system doesn’t always cater to everyone’s needs, and we set out to change that.</p>
        </section>

       
        <section class="offerings">
            <h2>What We Offer</h2>
            <p>We offer a wide range of courses across various subjects, including:</p>
            <ul>
                <li>Technology</li>
                <li>Business</li>
                <li>Arts</li>
                <li>Science</li>
            </ul>
            <p>Our courses are crafted by industry experts and experienced educators, ensuring that you receive the most relevant and up-to-date information.</p>
        </section>

       
        <section class="commitment">
            <h2>Our Commitment to Quality</h2>
            <p>Quality is at the heart of everything we do. We continuously strive to enhance our course offerings and user experience. Our team actively seeks feedback from our learners to ensure that we meet their needs and expectations.</p>
        </section>

        
        <section class="community">
            <h2>Join Our Community</h2>
            <p>When you choose the e-learning platform, you’re not just enrolling in a course; you’re joining a community of learners.</p>
        </section>

       
        <section class="cta">
            <h2>Get Started Today!</h2>
            <p>We invite you to explore our platform and discover the endless possibilities that await you.</p>
        </section>

      
        <section class="feedback-section">
            <h2>We Value Your Feedback</h2>
            <p>Share your experience with us and help us improve our services!</p>
            <?php include 'feedback.php'; ?>
        </section>
    </main>

   
    <footer>
        <h2>Contact Us</h2>
        <p>If you have any questions or need assistance, feel free to reach out to us at <a href="contact.php">contact@elearn.com</a>.</p>
    </footer>
</body>
</html>
