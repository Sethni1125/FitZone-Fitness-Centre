<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "fitzone1"; 

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Handle form submission ---
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = "Pilates Core Focus";
    $date = $_POST['class_date'];
    $time = $_POST['class_time'];
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $requirements = $_POST['requirements'];

    $stmt = $conn->prepare("INSERT INTO class_bookings (class_name, class_date, class_time, full_name, email, special_requirements) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $class_name, $date, $time, $name, $email, $requirements);

    if ($stmt->execute()) {
        $successMessage = "Booking confirmed successfully!";
    } else {
        $errorMessage = "Booking failed. Please try again.";
    }
}
?>


<html data-theme="light">
<head>
    <title>FitZone - Pilates Core Focus Booking</title>
    <link rel="stylesheet" href="../css/5.css">
</head>
<body>
    <!-- Theme Toggle Button -->
    <div class="theme-toggle" id="themeToggle">
        <svg id="moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/>
        </svg>
        <svg id="sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12,17c-2.76,0-5-2.24-5-5s2.24-5,5-5s5,2.24,5,5S14.76,17,12,17z M12,9c-1.65,0-3,1.35-3,3s1.35,3,3,3s3-1.35,3-3S13.65,9,12,9z"/>
            <path d="M12,3c-0.55,0-1,0.45-1,1v2c0,0.55,0.45,1,1,1s1-0.45,1-1V4C13,3.45,12.55,3,12,3z"/>
            <path d="M12,21c-0.55,0-1,0.45-1,1v2c0,0.55,0.45,1,1,1s1-0.45,1-1v-2C13,21.45,12.55,21,12,21z"/>
            <path d="M4.93,5.93c-0.39,0.39-0.39,1.02,0,1.41l1.42,1.42c0.39,0.39,1.02,0.39,1.41,0s0.39-1.02,0-1.41L6.34,5.93C5.95,5.54,5.32,5.54,4.93,5.93z"/>
            <path d="M18.36,17.36c-0.39,0.39-0.39,1.02,0,1.41l1.42,1.42c0.39,0.39,1.02,0.39,1.41,0s0.39-1.02,0-1.41l-1.42-1.42C19.38,16.97,18.75,16.97,18.36,17.36z"/>
            <path d="M3,12c0-0.55,0.45-1,1-1h2c0.55,0,1,0.45,1,1s-0.45,1-1,1H4C3.45,13,3,12.55,3,12z"/>
            <path d="M21,12c0-0.55-0.45-1-1-1h-2c-0.55,0-1,0.45-1,1s0.45,1,1,1h2C20.55,13,21,12.55,21,12z"/>
            <path d="M4.93,18.07c0.39-0.39,0.39-1.02,0-1.41l-1.42-1.42c-0.39-0.39-1.02-0.39-1.41,0s-0.39,1.02,0,1.41l1.42,1.42C3.91,18.46,4.54,18.46,4.93,18.07z"/>
            <path d="M19.07,5.93c-0.39-0.39-1.02-0.39-1.41,0l-1.42,1.42c-0.39,0.39-0.39,1.02,0,1.41s1.02,0.39,1.41,0l1.42-1.42C19.46,6.95,19.46,6.32,19.07,5.93z"/>
        </svg>
    </div>

    <div class="container">
        <div class="mockup">
            <div class="mockup-header">
                <div class="logo">FitZone</div>
                <div class="nav">
                    <a href="../front-end/Home.html" class="nav-item">Home</a>
                    <a href="../front-end/About Us.html" class="nav-item">About</a>
                    <a href="../front-end/Class.php" class="nav-item active">Classes</a>
                    <a href="../front-end/Membership.html" class="nav-item">Membership</a>
                    <a href="../front-end/Contact Us.html" class="nav-item">Contact Us</a>
                    <a href="../front-end/l.php" class="nav-item btn">Sign In</a>
                </div>
            </div>

            <h2>Pilates Core Focus</h2>
            
            <div class="class-details">
                <div class="instructor-info">
                    <div class="instructor-image">
                        <img src="../img/P2.jpeg" alt="Pilates Core Focus">
                    </div>
                    <div>
                        <h3>Class Overview</h3>
                        <p>Elevate your core strength and flexibility with our specialized Pilates class. Designed to improve posture, balance, and body alignment, this class focuses on precise movements that target deep core muscles and enhance overall body control.</p>
                    </div>
                </div>
                
                <div>
                    <h4>Class Details</h4>
                    <ul>
                        <li><strong>Instructors:</strong> Ms. Elena Rodriguez, Ms. Sarah Thompson</li>
                        <li><strong>Duration:</strong> 45 minutes</li>
                        <li><strong>Difficulty Level:</strong> Intermediate</li>
                        <li><strong>Equipment Needed:</strong> Yoga mat, comfortable clothing, water bottle</li>
                    </ul>
                </div>
            </div>

            <h3>Book Your Class</h3>
            <?php if ($successMessage): ?>
                <p style="color:green;"><?= $successMessage ?></p>
            <?php elseif ($errorMessage): ?>
                <p style="color:red;"><?= $errorMessage ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="classDate">Select Date</label>
                    <input type="date" name="class_date" id="classDate" required>
                </div>
                <div class="form-group">
                    <label for="classTime">Time Slot</label>
                    <select name="class_time" id="classTime" required>
                        <option value="">-- Select a Time --</option>
                        <option value="6am">6:00 AM (Mon, Wed, Fri)</option>
                        <option value="5pm">5:00 PM (Mon, Wed, Fri)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" name="full_name" id="fullName" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label>Special Requirements</label>
                    <textarea name="requirements" rows="3" placeholder="Any special requirements..." required></textarea>
                </div>
                <input type="submit" value="Confirm Booking" class="btn" style="width: 100%;">
            </form>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Theme toggle button
            const themeToggle = document.getElementById('themeToggle');
            
            themeToggle.addEventListener('click', function() {
                // Get current theme
                const currentTheme = document.documentElement.getAttribute('data-theme');
                
                // Toggle theme
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                // Set new theme
                document.documentElement.setAttribute('data-theme', newTheme);
                
                // Save preference
                localStorage.setItem('theme', newTheme);
            });
        });
    </script>
</body>
</html>