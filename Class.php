<?php
$conn = new mysqli("localhost", "root", "", "fitzone1");

$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$times = ["6:00 AM", "8:00 AM", "10:00 AM", "12:00 PM", "5:00 PM", "7:00 PM"];

$scheduleData = [];
$result = $conn->query("SELECT * FROM class_schedule");
while ($row = $result->fetch_assoc()) {
    $scheduleData[$row['day']][$row['time_slot']] = $row['class_name'];
}
?>

<!DOCTYPE html>
<html data-theme="light">
<head>
    <title>Classes - FitZone Fitness Center</title>
    <link rel="stylesheet" href="../css/C.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
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
        <div class="content">
            <h1 class="title">
                FitZone Fitness Center
                <div class="aurora">
                    <div class="aurora_item"></div>
                    <div class="aurora_item"></div>
                    <div class="aurora_item"></div>
                    <div class="aurora_item"></div>
                </div>
            </h1>
        </div>
        
        <h2>Classes</h2>
        <div class="mockup">
            <div class="mockup-header">
                <div class="logo">FitZone</div>
                <div class="nav">
                <a href="../front-end/Home.html" class="nav-item">Home</a>
                    <a href="../front-end/About Us.html" class="nav-item">About</a>
                    <a href="../front-end/Class.php" class="nav-item active">Classes</a>
                    <a href="../front-end/Membership.html" class="nav-item">Membership</a>
                    <a href="../front-end/Contact Us.html" class="nav-item">Contact Us</a>
                    <a href="../front-end/Blog.html" class="nav-item">Blog</a>
                    <a href="../front-end/l.php" class="btn">Sign In</a>
                </div>
            </div>
            
            <div class="hero">
                <h2>Find Your Perfect Workout</h2>
                <p>Explore our wide range of classes led by certified instructors to help you reach your fitness goals.</p>
            </div>

        <h2>Our Instructors</h2>
        <div class="instructors">
            <div class="instructor-card">
                <img src="../img/anuja.jpeg" alt="Mr. Anuja Rajakaruna" class="instructor-image">
                <div class="instructor-name">Mr. Anuja Rajakaruna</div>
                <div class="instructor-specialty">Strength Training & Cardio</div>
                <p>Certified personal trainer with 10+ years of experience in high-intensity fitness programs.</p>
            </div>
            
            <div class="instructor-card">
                <img src="../img/Maneesha1.jpeg" alt="Mrs. Maneesha Perera" class="instructor-image">
                <div class="instructor-name">Mrs. Maneesha Perera</div>
                <div class="instructor-specialty">Yoga & Cardio</div>
                <p>Experienced yoga instructor specializing in Vinyasa Flow and mindful movement practices.</p>
            </div>

            <div class="instructor-card">
                <img src="../img/Nethmi.jpeg" alt="Miss. Nethmi Perera" class="instructor-image">
                <div class="instructor-name">Miss. Nethmi Perera</div>
                <div class="instructor-specialty">Yoga</div>
                <p>Experienced yoga instructor specializing in Vinyasa Flow and mindful movement practices.</p>
            </div>
            
            <div class="instructor-card">
                <img src="../img/Yuvindu1.jpeg" alt="Mr. Yuvindu Mihijaya" class="instructor-image">
                <div class="instructor-name">Mr. Yuvindu Mihijaya</div>
                <div class="instructor-specialty">HIIT Circuit</div>
                <p>Expert in high-intensity interval training and metabolic conditioning techniques.</p>
            </div>
            
            <div class="instructor-card">
                <img src="../img/Helindu1.jpeg" alt="Mr. Helindu Vitiyala" class="instructor-image">
                <div class="instructor-name">Mr. Helindu Vitiyala</div>
                <div class="instructor-specialty">Pilates & Core Training</div>
                <p>Dedicated Pilates instructor focusing on core strength and body alignment.</p>
            </div>
        </div>
        
        <div class="class-card">
            <div class="class-image-container">
                <div class="class-image">
                    <img src="../img/A2.jpeg" alt="Advanced Strength Training">
                </div>
            </div>
            <div class="class-details">
                <h3>Advanced Strength Training</h3>
                <p>Take your strength to the next level with compound movements and progressive overload techniques.</p>
                <p><strong>Instructor:</strong> Mr. Anuja Rajakaruna, Mr. Yuvindu Mihijaya | <strong>Duration:</strong> 60 minutes</p>
                <div class="class-actions">
                    <a href="../front-end/Advanced Strength Training.php" class="btn btn-secondary">View Details</a>
                    <a href="../front-end/1.php" class="btn">Book Now</a>
                </div>
            </div>
        </div>

        <div class="class-card">
            <div class="class-image-container">
                <div class="class-image">
                    <img src="../img/C2.jpeg" alt="Cardio Workouts">
                </div>
            </div>
            <div class="class-details">
                <h3>Cardio Workouts</h3>
                <p>Boost your cardiovascular health and endurance with a variety of high-energy cardio exercises and interval training.</p>
                <p><strong>Instructor:</strong> Mrs. Maneesha Perera, Mr. Anuja Rajakaruna | <strong>Duration:</strong> 50 minutes</p>
                <div class="class-actions">
                    <a href="../front-end/Cardio Workouts.php" class="btn btn-secondary">View Details</a>
                    <a href="../front-end/2.php" class="btn">Book Now</a>
                </div>
            </div>
        </div>

        <div class="class-card">
            <div class="class-image-container">
                <div class="class-image">
                    <img src="../img/Y2.jpeg" alt="Vinyasa Flow Yoga">
                </div>
            </div>
            <div class="class-details">
                <h3>Vinyasa Flow Yoga</h3>
                <p>A dynamic practice that links movement with breath, creating a flow between postures.</p>
                <p><strong>Instructor:</strong> Ms. Maneesha Perera, Miss. Nethmi Perera | <strong>Duration:</strong> 75 minutes</p>
                <div class="class-actions">
                    <a href="../front-end/Vinyasa Flow Yoga.html" class="btn btn-secondary">View Details</a>
                    <a href="../front-end/3.php" class="btn">Book Now</a>
                </div>
            </div>
        </div>

        <div class="class-card">
            <div class="class-image-container">
                <div class="class-image">
                    <img src="../img/H2.jpeg" alt="HIIT Circuit">
                </div>
            </div>
            <div class="class-details">
                <h3>HIIT Circuit</h3>
                <p>High-intensity interval training to maximize calorie burn and improve cardiovascular fitness.</p>
                <p><strong>Instructor:</strong> Mr. Yuvindu Mihijaya | <strong>Duration:</strong> 45 minutes</p>
                <div class="class-actions">
                    <a href="../front-end/HIIT Circuit.html" class="btn btn-secondary">View Details</a>
                    <a href="../front-end/4.php" class="btn">Book Now</a>
                </div>
            </div>
        </div>

        <div class="class-card">
            <div class="class-image-container">
                <div class="class-image">
                    <img src="../img/P2.jpeg" alt="Pilates Core Focus">
                </div>
            </div>
            <div class="class-details">
                <h3>Pilates Core Focus</h3>
                <p>Build core strength, improve posture, and enhance flexibility with controlled movements.</p>
                <p><strong>Instructor:</strong> Mr. Helindu Vitiyala | <strong>Duration:</strong> 60 minutes</p>
                <div class="class-actions">
                    <a href="../front-end/Pilates Core Focus.html" class="btn btn-secondary">View Details</a>
                    <a href="../front-end/5.php" class="btn">Book Now</a>
                </div>
            </div>
        </div>
    </div>


    <section id="schedule">
        <h3 class="schedule-table">Weekly Schedule</h3>    
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <?php foreach ($days as $day): ?>
                            <th><?= $day ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($times as $time): ?>
                        <tr>
                            <td><?= $time ?></td>
                            <?php foreach ($days as $day): 
                                $class = $scheduleData[$day][$time] ?? '-';
                            ?>
                                <td><?= htmlspecialchars($class) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h4>FitZone</h4>
                <p>Transform your body, transform your life with our state-of-the-art fitness center and expert trainers.</p>
                <div class="social-links">
                    <a href=" "><i class="fab fa-youtube"></i></a>
                    <a href=" "><i class="fab fa-instagram"></i></a>
                    <a href=" "><i class="fab fa-facebook-f"></i></a>
                    <a href=" "><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="../front-end/Home.html">Home</a></li>
                    <li><a href="../front-end/About Us.html">About Us</a></li>
                    <li><a href="../front-end/Class.php">Classes</a></li>
                    <li><a href="../front-end/Membership.html">Membership</a></li>
                    <li><a href="../front-end/Contact Us.html">Contact Us</a></li>
                    <a href="../front-end/Login.html" class="nav-item btn">Sign In</a>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Queen Street, Colombo 3</li>
                    <li><i class="fas fa-phone"></i> 011 325 6547</li>
                    <li><i class="fas fa-envelope"></i> fitzonefitness@gmail.com</li>
                    <li><i class="fas fa-clock"></i> Mon-Fri: 5am-10pm</li>
                    <li><i class="fas fa-clock"></i> Sat-Sun: 7am-8pm</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 FitZone Fitness Center. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            const themeToggle = document.getElementById('themeToggle');
            
            themeToggle.addEventListener('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';                
                document.documentElement.setAttribute('data-theme', newTheme);                
                localStorage.setItem('theme', newTheme);
            });
        });
    </script>
    
</body>
</html>