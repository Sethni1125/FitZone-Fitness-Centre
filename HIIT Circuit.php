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
    <title>FitZone Fitness Center - HIIT Circuit Training</title>
    <link rel="preconnect" href="//js-sec.indexww.com">
    <link rel="preconnect" href="//c.amazon-adsystem.com">
    <link rel="preconnect" href="//securepubads.g.doubleclick.net">
    <link rel="preconnect" href="//ak.sail-horizon.com">
    <link rel="dnsprefetch" href="//www.google-analytics.com">
    <link rel="stylesheet" href="../css/HC.css">
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
        
        <h2>HIIT Circuit Training</h2>
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
                <h2>HIIT Circuit Training</h2>
                <p>Maximize your fitness potential with high-intensity interval training designed to torch calories, boost metabolism, and build total-body strength in just 45 minutes.</p>
                <a href="#contribute" class="btn btn-secondary">Contribute</a>
            </div>
        
            
            <h3>About This Class</h3>
            <p>Our HIIT Circuit Training class is a dynamic, full-body workout that combines high-intensity interval training with circuit-style resistance exercises. This program is designed to challenge your cardiovascular endurance, build functional strength, and accelerate fat loss through scientifically-proven training methods.</p>
            
            <p>Whether you're looking to break through fitness plateaus, improve overall athletic performance, or transform your body composition, our HIIT Circuit Training offers an efficient and engaging approach to fitness that delivers remarkable results.</p>
            
            <div class="instructor-profile">
                <div class="instructor-image">
                    <img src="../img/Yuvindu1.jpegs" alt="Mr. Yuvindu Mihijaya">
                </div>
                <div class="instructor-info">
                    <h3>Mr. Yuvindu Mihijaya</h3>
                    <p>A certified HIIT and functional fitness specialist with over 8 years of experience, Kavindya has a proven track record of helping clients achieve breakthrough fitness results. She holds advanced certifications in Sports Performance Training and Metabolic Conditioning.</p>
                    <p>"My goal is to push you beyond your perceived limits while ensuring safety and proper form. Every workout is an opportunity to become a stronger, more resilient version of yourself."</p>
                </div>
            </div>
            
            <h3>Class Features</h3>
            <div class="class-features">
                <div class="feature">
                    <div class="feature-icon">🔥</div>
                    <h4>Metabolic Boost</h4>
                    <p>Maximize calorie burn and boost metabolism through strategically designed high-intensity intervals and compound movements.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💪</div>
                    <h4>Full-Body Conditioning</h4>
                    <p>Engage multiple muscle groups simultaneously with functional exercises that improve strength, endurance, and overall athletic performance.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">⏱️</div>
                    <h4>Time-Efficient Workouts</h4>
                    <p>Get maximum results in minimal time with 45-minute sessions that are scientifically proven to be more effective than traditional steady-state cardio.</p>
                </div>
            </div>
            
            <h3>Sample Workout</h3>
            <div class="workout-schedule">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Station</th>
                            <th>Exercise</th>
                            <th>Duration</th>
                            <th>Rest</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Burpees</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Full body explosive movement</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Kettlebell Swings</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Focus on hip drive and core engagement</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Mountain Climbers</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Maintain core stability and speed</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Box Jumps</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Explosive lower body power</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Battle Ropes</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Upper body and cardiovascular endurance</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Dumbbell Thrusters</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Full body compound movement</td>
                        </tr>
                    </tbody>
                </table>
                <p class="mt-3"><strong>Circuit Structure:</strong> Complete all 6 stations, rest 2 minutes, then repeat 3-4 times depending on fitness level.</p>
            </div>
            
            <h3 id="diet">Recommended Diet Plans</h3>
            <p>Nutrition plays a crucial role in supporting your HIIT training and recovery. Here are sample diet plans to complement your workouts:</p>
            
            <div class="diet-plan">
                <h4>Fat Loss & Performance Diet Plan</h4>
                <div class="diet-day">
                    <h5>Daily Macros Target:</h5>
                    <p>Protein: 1.6-2.0g per kg of body weight<br>
                    Carbohydrates: 3-5g per kg of body weight<br>
                    Fats: 0.5-1.0g per kg of body weight</p>
                    
                    <h5>Sample Day:</h5>
                    <div class="meal">
                        <strong>Breakfast:</strong>
                        <p>- 3 egg whites + 1 whole egg scrambled<br>
                        - 1/2 cup rolled oats with berries<br>
                        - 1 scoop whey protein</p>
                    </div>
                    <div class="meal">
                        <strong>Mid-Morning Snack:</strong>
                        <p>- Greek yogurt (1 cup)<br>
                        - 1/4 cup mixed nuts<br>
                        - Handful of blueberries</p>
                    </div>
                    <div class="meal">
                        <strong>Lunch:</strong>
                        <p>- 5oz grilled chicken breast<br>
                        - 1/2 cup quinoa<br>
                        - Large mixed green salad<br>
                        - 1 tbsp olive oil dressing</p>
                    </div>
                    <div class="meal">
                        <strong>Pre-Workout:</strong>
                        <p>- Rice cake with almond butter<br>
                        - Small banana</p>
                    </div>
                    <div class="meal">
                        <strong>Post-Workout:</strong>
                        <p>- Protein shake (30g protein)<br>
                        - 1 medium sweet potato</p>
                    </div>
                    <div class="meal">
                        <strong>Dinner:</strong>
                        <p>- 6oz salmon<br>
                        - 1 cup roasted vegetables<br>
                        - Small side salad<br>
                        - 1/2 avocado</p>
                    </div>
                    <div class="meal">
                        <strong>Evening Snack:</strong>
                        <p>- Casein protein<br>
                        - 1 tbsp natural peanut butter</p>
                    </div>
                </div>
            </div>
            
            <div class="diet-plan">
                <h4>Energy Optimization Diet Plan</h4>
                <div class="diet-day">
                    <h5>Daily Macros Target:</h5>
                    <p>Protein: 1.5-1.8g per kg of body weight<br>
                    Carbohydrates: 4-6g per kg of body weight<br>
                    Fats: 0.7-1.2g per kg of body weight</p>
                    
                    <h5>Sample Day:</h5>
                    <div class="meal">
                        <strong>Breakfast:</strong>
                        <p>- Protein smoothie (1 scoop whey, banana, spinach, almond milk)<br>
                        - 2 whole grain toast with avocado</p>
                    </div>
                    <div class="meal">
                        <strong>Mid-Morning Snack:</strong>
                        <p>- Protein bar<br>
                        - Apple<br>
                        - Handful of almonds</p>
                    </div>
                    <div class="meal">
                        <strong>Lunch:</strong>
                        <p>- Turkey and quinoa bowl<br>
                        - Mixed roasted vegetables<br>
                        - Lemon olive oil dressing</p>
                    </div>
                    <div class="meal">
                        <strong>Pre-Workout:</strong>
                        <p>- Energy balls (dates, nuts, protein powder)<br>
                        - Green tea</p>
                    </div>
                    <div class="meal">
                        <strong>Post-Workout:</strong>
                        <p>- Protein shake with quick-absorbing carbs<br>
                        - Banana</p>
                    </div>
                    <div class="meal">
                        <strong>Dinner:</strong>
                        <p>- Lean beef stir-fry<br>
                        - Brown rice<br>
                        - Mixed vegetables<br>
                        - Teriyaki sauce</p>
                    </div>
                    <div class="meal">
                        <strong>Evening Snack:</strong>
                        <p>- Cottage cheese<br>
                        - Berries</p>
                    </div>
                </div>
            </div>
            
            <h3>Class Schedule</h3>
            <p>HIIT Circuit Training is offered at the following times:</p>
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
                        <td>5:30 PM - 6:15 PM</td>
                        <td>Yuvindu Mihijaya</td>
                        <td>Functional Training Zone</td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td>6:00 AM - 6:45 AM</td>
                        <td>Yuvindu Mihijaya</td>
                        <td>Functional Training Zone</td>
                    </tr>
                    <tr>
                        <td>Wednesday</td>
                        <td>5:30 PM - 6:15 PM</td>
                        <td>Yuvindu Mihijaya</td>
                        <td>Functional Training Zone</td>
                    </tr>
                    <tr>
                        <td>Thursday</td>
                        <td>6:00 AM - 6:45 AM</td>
                        <td>Yuvindu Mihijaya</td>
                        <td>Functional Training Zone</td>
                    </tr>
                    <tr>
                        <td>Saturday</td>
                        <td>9:00 AM - 9:45 AM</td>
                        <td>Yuvindu Mihijaya</td>
                        <td>Functional Training Zone</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>Feedback</h3>
            <div class="testimonials">
                <div class="testimonial">
                    <div class="testimonial-text">
                        "I was skeptical about HIIT at first, but these classes have completely transformed my fitness. In just two months, I've lost 8kg, gained significant muscle definition, and feel more energetic than ever. The instructors are phenomenal at modifying exercises for different fitness levels."
                    </div>
                    <div class="testimonial-author">- Rashmi Fernando</div>
                </div>
                
            </div>
            
            <h3 id="contribute">Contribute Your Experience</h3>
            <p>Have you completed our HIIT Circuit Training program? Share your journey, challenges, and results to inspire others!</p>
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
                    <label for="contribute-before">Before Stats:</label>
                    <textarea id="contribute-before" name="contribute-before" rows="3" placeholder="Share your starting measurements, weight, fitness level, etc."></textarea>
                </div>
                <div class="form-group">
                    <label for="contribute-after">After Stats:</label>
                    <textarea id="contribute-after" name="contribute-after" rows="3" placeholder="Share your progress, weight loss, fitness improvements, etc."></textarea>
                </div>
                <div class="form-group">
                    <label for="contribute-experience">Your Experience:</label>
                    <textarea id="contribute-experience" name="contribute-experience" rows="5" placeholder="Share your journey, challenges, breakthroughs, and tips for others"></textarea>
                </div>
                <div class="form-group">
                    <label for="contribute-photo">Upload Before/After Photos (optional):</label>
                    <input type="file" id="contribute-photo" name="contribute-photo" multiple>
                </div>
                <a href="#" class="btn">Submit Your Story</a>
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