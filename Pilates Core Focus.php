<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "fitzone1";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $name = $conn->real_escape_string($_POST['contribute-name']);
    $email = $conn->real_escape_string($_POST['contribute-email']);
    $before_stats = $conn->real_escape_string($_POST['contribute-before']);
    $after_stats = $conn->real_escape_string($_POST['contribute-after']);
    $experience = $conn->real_escape_string($_POST['contribute-experience']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit;
    }
    
    // Insert contribution data into the database
    $sql = "INSERT INTO client_contributions (name, email, before_stats, after_stats, experience) 
            VALUES ('$name', '$email', '$before_stats', '$after_stats', '$experience')";
    
    if ($conn->query($sql) === TRUE) {
        $contribution_id = $conn->insert_id;
        
        // Handle file uploads
        if (!empty($_FILES['contribute-photo']['name'][0])) {
            // Create upload directory if it doesn't exist
            $upload_dir = "uploads/contributions/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Count number of uploaded files
            $total_files = count($_FILES['contribute-photo']['name']);
            
            // Loop through each file
            for ($i = 0; $i < $total_files; $i++) {
                // Get the file details
                $file_name = $_FILES['contribute-photo']['name'][$i];
                $file_tmp = $_FILES['contribute-photo']['tmp_name'][$i];
                $file_size = $_FILES['contribute-photo']['size'][$i];
                $file_error = $_FILES['contribute-photo']['error'][$i];
                
                // Generate a unique file name to prevent overwrites
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $new_file_name = uniqid('contribution_') . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                
                // Check file size (limit to 5MB)
                if ($file_size > 5000000) {
                    echo "File size exceeds the limit (5MB)";
                    continue;
                }
                
                // Check file type (allow only image files)
                $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
                if (!in_array($file_ext, $allowed_types)) {
                    echo "Only JPG, JPEG, PNG, and GIF files are allowed";
                    continue;
                }
                
                // Upload the file
                if ($file_error === 0) {
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Determine if it's a before or after photo based on naming convention
                        // This is a simple approach - you might want to modify this based on your needs
                        $photo_type = (strpos(strtolower($file_name), 'before') !== false) ? 'before' : 
                                     ((strpos(strtolower($file_name), 'after') !== false) ? 'after' : NULL);
                        
                        // Insert file details into the database
                        $photo_sql = "INSERT INTO contribution_photos (contribution_id, photo_filename, photo_type) 
                                     VALUES ('$contribution_id', '$new_file_name', '$photo_type')";
                        $conn->query($photo_sql);
                    } else {
                        echo "Failed to upload file: " . $file_name;
                    }
                } else {
                    echo "Error uploading file: " . $file_error;
                }
            }
        }
        
        // Redirect to a thank you page or display success message
        header("Location: thank_you.php?contribution=success");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>


<html data-theme="light">
<head>
    <title>FitZone Fitness Center - Pilates Core Focus</title>
    <link rel="stylesheet" href="../css/P.css">
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
        
        <h2>Pilates Core Focus</h2>
        <div class="mockup">
            <div class="mockup-header">
                <div class="logo">FitZone</div>
                <div class="nav">
                    <a href="../front-end/Home.html" class="nav-item ">Home</a>
                    <a href="../front-end/About Us.html" class="nav-item">About</a>
                    <a href="../front-end/Class.php" class="nav-item active">Classes</a>
                    <a href="../front-end/Membership.html" class="nav-item">Membership</a>
                    <a href="../front-end/Contact Us.html" class="nav-item">Contact Us</a>
                    <a href="../front-end/l.php" class="btn">Sign In</a>
                </div>
            </div>
            
            <div class="hero">
                <h2>Pilates Core Focus</h2>
                <p>Transform your body and mind through precision movements that strengthen your core, improve flexibility, and enhance overall body awareness.</p>
                <a href="#contribute" class="btn btn-secondary">Contribute</a>
            </div>
        
            <h3>About This Class</h3>
            <p>Our Pilates Core Focus class is designed to develop a strong, balanced, and flexible body through mindful movement and targeted core conditioning. This comprehensive program emphasizes proper alignment, breath control, and intentional muscle engagement to build core strength, improve posture, and enhance overall functional fitness.</p>
            
            <p>Whether you're looking to rehabilitate from an injury, prevent future muscle imbalances, or simply develop a stronger, more resilient body, this class provides a holistic approach to fitness that goes beyond traditional strength training.</p>
            
            <div class="instructor-profile">
                <div class="instructor-image">
                    <img src="../img/helindu.jpeg" alt="Mr. Helindu Vitiyala">
                </div>
                <div class="instructor-info">
                    <h3>Mr. Helindu Vitiyala</h3>
                    <p>A certified Pilates instructor with over 12 years of experience, Mayuri holds advanced certifications in Classical Pilates, Rehabilitation Pilates, and Mind-Body Movement Techniques. She specializes in creating personalized approaches that cater to individual body mechanics and fitness goals.</p>
                    <p>"Pilates is not just an exercise; it's a practice of mindful movement that transforms both body and mind. My goal is to help you develop strength from the inside out."</p>
                </div>
            </div>
            
            <h3>Class Features</h3>
            <div class="class-features">
                <div class="feature">
                    <div class="feature-icon">💪</div>
                    <h4>Core Strength</h4>
                    <p>Develop deep core muscles through targeted exercises that improve stability, posture, and overall body strength.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🧘</div>
                    <h4>Mind-Body Connection</h4>
                    <p>Learn to synchronize breath with movement, enhancing body awareness and mental focus during each exercise.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🌈</div>
                    <h4>Flexibility & Balance</h4>
                    <p>Improve flexibility, joint mobility, and overall body balance through controlled, precise movements.</p>
                </div>
            </div>
            
            <h3>Sample Workout</h3>
            <div class="workout-schedule">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exercise</th>
                            <th>Focus</th>
                            <th>Reps/Duration</th>
                            <th>Equipment</th>
                            <th>Key Technique</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Roll Up</td>
                            <td>Core Activation</td>
                            <td>8-10 reps</td>
                            <td>Mat</td>
                            <td>Slow, controlled vertebrae articulation</td>
                        </tr>
                        <tr>
                            <td>The Hundred</td>
                            <td>Core Endurance</td>
                            <td>100 pumps</td>
                            <td>Mat</td>
                            <td>Maintain stable core, precise arm pumps</td>
                        </tr>
                        <tr>
                            <td>Single Leg Circles</td>
                            <td>Hip Mobility</td>
                            <td>5-8 circles each direction</td>
                            <td>Mat</td>
                            <td>Control leg movement from core</td>
                        </tr>
                        <tr>
                            <td>Plank Variations</td>
                            <td>Total Core Stability</td>
                            <td>30-60 seconds</td>
                            <td>Mat/Reformer</td>
                            <td>Maintain neutral spine alignment</td>
                        </tr>
                        <tr>
                            <td>Side Leg Lifts</td>
                            <td>Oblique Strength</td>
                            <td>10-12 reps each side</td>
                            <td>Mat</td>
                            <td>Engage core, minimal hip rotation</td>
                        </tr>
                        <tr>
                            <td>Swan Dive</td>
                            <td>Back Strength</td>
                            <td>6-8 reps</td>
                            <td>Mat</td>
                            <td>Controlled back extension</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <h3 id="diet">Complementary Nutrition</h3>
            <p>Proper nutrition supports your Pilates practice by providing energy, supporting muscle recovery, and promoting overall wellness. Here are recommended dietary approaches:</p>
            
            <div class="diet-plan">
                <h4>Lean Muscle & Recovery Diet</h4>
                <div class="diet-day">
                    <h5>Daily Nutritional Focus:</h5>
                    <p>Anti-inflammatory foods<br>
                    Lean proteins<br>
                    Complex carbohydrates<br>
                    Hydration</p>
                    
                    <h5>Sample Day:</h5>
                    <div class="meal">
                        <strong>Breakfast:</strong>
                        <p>- Green smoothie (spinach, banana, plant protein)<br>
                        - Chia seed pudding<br>
                        - Handful of mixed berries</p>
                    </div>
                    <div class="meal">
                        <strong>Mid-Morning Snack:</strong>
                        <p>- Greek yogurt with almonds<br>
                        - Turmeric latte</p>
                    </div>
                    <div class="meal">
                        <strong>Lunch:</strong>
                        <p>- Quinoa salad with grilled chicken<br>
                        - Mixed greens<br>
                        - Olive oil and lemon dressing</p>
                    </div>
                    <div class="meal">
                        <strong>Pre-Workout:</strong>
                        <p>- Apple with almond butter<br>
                        - Herbal tea</p>
                    </div>
                    <div class="meal">
                        <strong>Post-Workout:</strong>
                        <p>- Protein smoothie<br>
                        - Electrolyte water</p>
                    </div>
                    <div class="meal">
                        <strong>Dinner:</strong>
                        <p>- Baked salmon<br>
                        - Roasted sweet potato<br>
                        - Steamed broccoli</p>
                    </div>
                    <div class="meal">
                        <strong>Evening Snack:</strong>
                        <p>- Magnesium-rich tart cherry juice<br>
                        - Small handful of walnuts</p>
                    </div>
                </div>
            </div>
            
            <h3>Class Schedule</h3>
            <p>Pilates Core Focus classes are offered at the following times:</p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Instructor</th>
                        <th>Room</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Monday</td>
                        <td>7:00 AM - 8:00 AM</td>
                        <td>Helindu Vitiyalatd>
                        <td>Pilates Studio</td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td>6:00 PM - 7:00 PM</td>
                        <td>Helindu Vitiyala</td>
                        <td>Pilates Studio</td>
                    </tr>
                    <tr>
                        <td>Wednesday</td>
                        <td>7:00 AM - 8:00 AM</td>
                        <td>Helindu Vitiyala</td>
                        <td>Pilates Studio</td>
                    </tr>
                    <tr>
                        <td>Thursday</td>
                        <td>6:00 PM - 7:00 PM</td>
                        <td>Helindu Vitiyala</td>
                        <td>Pilates Studio</td>
                    </tr>
                    <tr>
                        <td>Saturday</td>
                        <td>9:00 AM - 10:30 AM</td>
                        <td>Helindu Vitiyala</td>
                        <td>Pilates Studio</td>
                    </tr>
                </tbody>
            </table>
            
            
            <h3 id="contribute">Contribute Your Experience</h3>
            <p>Have you completed our Pilates Core Focus program? Share your journey, insights, and transformations to inspire others!</p>
            <div class="contribution-form">
                <div class="form-group">
                    <label for="contribute-name">Your Name:</label>
                    <input type="text" id="contribute-name" name="contribute-name" placeholder="Enter your name">
                </div>
                <div class="form-group">
                    <label for="contribute-email">Email Address:</label>
                    <input type="email" id="contribute-email" name="contribute-email" placeholder="Enter your email address">
                </div>
                <div class="form-group">
                    <label for="contribute-before">Before Experience:</label>
                    <textarea id="contribute-before" name="contribute-before" rows="3" placeholder="Share your initial fitness level, challenges, or goals"></textarea>
                </div>
                <div class="form-group">
                    <label for="contribute-after">After Experience:</label>
                    <textarea id="contribute-after" name="contribute-after" rows="3" placeholder="Share your progress, improvements, and personal breakthroughs"></textarea>
                </div>
                <div class="form-group">
                    <label for="contribute-experience">Your Pilates Journey:</label>
                    <textarea id="contribute-experience" name="contribute-experience" rows="5" placeholder="Describe your experience with the Pilates Core Focus class, what you learned, and how it impacted you"></textarea>
                </div>
                <div class="form-group">
                    <label for="contribute-photo">Upload Before/After Photos (optional):</label>
                    <input type="file" id="contribute-photo" name="contribute-photo" multiple>
                </div>
                <a href="" class="btn">Submit Your Story</a>
            </div>
        </div>
    </div>
    
    <script>
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        
        // Check for saved theme preference or use default
        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', savedTheme);
        
        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    </script>
</body>
</html>