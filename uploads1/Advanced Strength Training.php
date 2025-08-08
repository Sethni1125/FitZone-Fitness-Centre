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
    <title>Thank You - FitZone Fitness Center</title>
    <link rel="stylesheet" href="../css/AST.css">
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
        
        <h2>Advanced Strength Training</h2>
        <div class="mockup">
            <div class="mockup-header">
                <div class="logo">FitZone</div>
                <div class="nav">
                    <a href="../front-end/Home.html" class="nav-item">Home</a>
                    <a href="../front-end/About Us.html" class="nav-item">About</a>
                    <a href="../front-end/class.php" class="nav-item active">Classes</a>
                    <a href="../front-end/Membership.html" class="nav-item">Membership</a>
                    <a href="../front-end/Contact Us.html" class="nav-item">Contact Us</a>
                    <a href="../front-end/l.php" class="nav-item btn">Sign In</a>
                </div>
            </div>
            
            <div class="hero">
                <h2>Advanced Strength Training</h2>
                <p>Take your strength to the next level with compound movements and progressive overload techniques designed to help you break through plateaus and achieve new personal records.</p>
                <a href="#contribute" class="btn btn-secondary">Contribute</a>
            </div>
        
            
            <h3>About This Class</h3>
            <p>Our Advanced Strength Training class is designed for individuals who have mastered the fundamentals of resistance training and are ready to take their fitness journey to the next level. This comprehensive program focuses on progressive overload techniques, compound movements, and targeted muscle development.</p>
            
            <p>Whether you're looking to increase your muscular strength, build lean muscle mass, or enhance your overall athletic performance, this class provides the structured approach and expert guidance needed to achieve your goals safely and effectively.</p>
            
            <div class="instructor-profile">
                <div class="instructor-image">
                    <img src="../img/Anuja.jpeg" alt="Anuja Rajakaruna">
                </div>
                <div class="instructor-info">
                    <h3>Mr. Anuja Rajakaruna</h3>
                    <p>With over 10 years of experience in strength and conditioning, Anuja holds certifications in Advanced Resistance Training, Sports Nutrition, and Functional Movement. He specializes in helping clients break through plateaus with scientifically-backed training methodologies.</p>
                    <p>"My philosophy is simple: consistent progressive overload combined with proper recovery leads to optimal results. I'm committed to helping each participant reach their full strength potential."</p>
                </div>
            </div>
            
            <div class="instructor-profile">
                <div class="instructor-image">
                    <img src="../img/Yuvindu1.jpeg" alt="Yuvindu Mihijaya">
                </div>
                <div class="instructor-info">
                    <h3>Mr. Yuvindu Mihijaya</h3>
                    <p>A former competitive powerlifter with a degree in Exercise Physiology, Yuvindu brings a wealth of practical and theoretical knowledge to each session. He specializes in perfect form and technique to maximize results while minimizing injury risk.</p>
                    <p>"I believe that strength training is for everyone, regardless of age or fitness level. My goal is to help you build not just a stronger body, but a stronger mindset that carries over into all aspects of life."</p>
                </div>
            </div>
            
            <h3>Class Features</h3>
            <div class="class-features">
                <div class="feature">
                    <div class="feature-icon">💪</div>
                    <h4>Compound Lifts</h4>
                    <p>Master the fundamental compound movements including squats, deadlifts, bench press, and overhead press with expert guidance on proper form.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">📈</div>
                    <h4>Progressive Overload</h4>
                    <p>Learn systematic approaches to progressive overload that ensure continuous gains in strength and muscle development.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">⏱️</div>
                    <h4>Periodization</h4>
                    <p>Follow a structured periodization program that cycles between different training phases to optimize results and prevent plateaus.</p>
                </div>
            </div>
            
            <h3>Sample Workout</h3>
            <div class="workout-schedule">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exercise</th>
                            <th>Sets</th>
                            <th>Reps</th>
                            <th>Rest</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Barbell Back Squat</td>
                            <td>5</td>
                            <td>5</td>
                            <td>3 min</td>
                            <td>Focus on depth and maintaining neutral spine</td>
                        </tr>
                        <tr>
                            <td>Bench Press</td>
                            <td>5</td>
                            <td>5</td>
                            <td>3 min</td>
                            <td>Full range of motion with proper scapular retraction</td>
                        </tr>
                        <tr>
                            <td>Barbell Row</td>
                            <td>3</td>
                            <td>8</td>
                            <td>2 min</td>
                            <td>Maintain neutral spine and squeeze shoulder blades</td>
                        </tr>
                        <tr>
                            <td>Overhead Press</td>
                            <td>3</td>
                            <td>8</td>
                            <td>2 min</td>
                            <td>Full lockout at top, avoid excessive arching</td>
                        </tr>
                        <tr>
                            <td>Romanian Deadlift</td>
                            <td>3</td>
                            <td>10</td>
                            <td>2 min</td>
                            <td>Focus on hamstring stretch and hip hinge</td>
                        </tr>
                        <tr>
                            <td>Weighted Pull-ups</td>
                            <td>3</td>
                            <td>AMRAP</td>
                            <td>2 min</td>
                            <td>As many reps as possible with proper form</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <h3 id="diet">Recommended Diet Plans</h3>
            <p>Proper nutrition is essential for supporting your strength training goals. Here are sample diet plans to complement your workouts:</p>
            
            <div class="diet-plan">
                <h4>Muscle Building Diet Plan</h4>
                <div class="diet-day">
                    <h5>Daily Macros Target:</h5>
                    <p>Protein: 1.6-2.2g per kg of body weight<br>
                    Carbohydrates: 4-7g per kg of body weight<br>
                    Fats: 0.5-1.5g per kg of body weight</p>
                    
                    <h5>Sample Day:</h5>
                    <div class="meal">
                        <strong>Breakfast:</strong>
                        <p>- 4 whole eggs scrambled with spinach and peppers<br>
                        - 1 cup oatmeal with 1 tbsp honey and berries<br>
                        - 1 banana</p>
                    </div>
                    <div class="meal">
                        <strong>Mid-Morning Snack:</strong>
                        <p>- Protein shake (30g protein)<br>
                        - 1 apple<br>
                        - 1/4 cup almonds</p>
                    </div>
                    <div class="meal">
                        <strong>Lunch:</strong>
                        <p>- 6oz grilled chicken breast<br>
                        - 1 cup brown rice<br>
                        - 2 cups mixed vegetables<br>
                        - 1 tbsp olive oil</p>
                    </div>
                    <div class="meal">
                        <strong>Pre-Workout Snack:</strong>
                        <p>- 1 cup Greek yogurt<br>
                        - 1/2 cup granola<br>
                        - 1 tbsp honey</p>
                    </div>
                    <div class="meal">
                        <strong>Post-Workout:</strong>
                        <p>- Protein shake with 30g protein<br>
                        - 1 banana</p>
                    </div>
                    <div class="meal">
                        <strong>Dinner:</strong>
                        <p>- 8oz salmon<br>
                        - 2 medium sweet potatoes<br>
                        - 2 cups broccoli<br>
                        - 1 tbsp coconut oil</p>
                    </div>
                    <div class="meal">
                        <strong>Evening Snack:</strong>
                        <p>- Cottage cheese (1 cup)<br>
                        - 1 tbsp peanut butter<br>
                        - 1/2 cup blueberries</p>
                    </div>
                </div>
            </div>
            
            <div class="diet-plan">
                <h4>Strength Performance Diet Plan</h4>
                <div class="diet-day">
                    <h5>Daily Macros Target:</h5>
                    <p>Protein: 1.8-2.0g per kg of body weight<br>
                    Carbohydrates: 5-6g per kg of body weight<br>
                    Fats: 1.0-1.5g per kg of body weight</p>
                    
                    <h5>Sample Day:</h5>
                    <div class="meal">
                        <strong>Breakfast:</strong>
                        <p>- 3 whole eggs + 3 egg whites<br>
                        - 2 slices whole grain toast<br>
                        - 1 avocado<br>
                        - 1 cup berries</p>
                    </div>
                    <div class="meal">
                        <strong>Mid-Morning Snack:</strong>
                        <p>- 1 cup Greek yogurt<br>
                        - 1/4 cup walnuts<br>
                        - 1 apple</p>
                    </div>
                    <div class="meal">
                        <strong>Lunch:</strong>
                        <p>- 6oz lean ground beef<br>
                        - 1 cup quinoa<br>
                        - 2 cups mixed vegetables<br>
                        - 1 tbsp olive oil</p>
                    </div>
                    <div class="meal">
                        <strong>Pre-Workout Meal (2 hours before):</strong>
                        <p>- 5oz chicken breast<br>
                        - 1 cup jasmine rice<br>
                        - 1 cup green beans</p>
                    </div>
                    <div class="meal">
                        <strong>Post-Workout:</strong>
                        <p>- Protein shake (30-40g protein)<br>
                        - 1 banana<br>
                        - 1 tbsp honey</p>
                    </div>
                    <div class="meal">
                        <strong>Dinner:</strong>
                        <p>- 8oz steak<br>
                        - 2 medium potatoes<br>
                        - 2 cups asparagus<br>
                        - 1 tbsp butter</p>
                    </div>
                    <div class="meal">
                        <strong>Evening Snack:</strong>
                        <p>- Casein protein shake (25-30g protein)<br>
                        - 1 tbsp almond butter</p>
                    </div>
                </div>
            </div>
            
            <h3>Class Schedule</h3>
            <p>Advanced Strength Training is offered at the following times:</p>
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
                        <td>6:00 AM - 7:00 AM</td>
                        <td>Anuja Rajakaruna</td>
                        <td>Main Weight Room</td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td>5:00 PM - 6:00 PM</td>
                        <td>Yuvindu Mihijaya</td>
                        <td>Main Weight Room</td>
                    </tr>
                    <tr>
                        <td>Wednesday</td>
                        <td>6:00 AM - 7:00 AM</td>
                        <td>Anuja Rajakaruna</td>
                        <td>Main Weight Room</td>
                    </tr>
                    <tr>
                        <td>Thursday</td>
                        <td>5:00 PM - 6:00 PM</td>
                        <td>Yuvindu Mihijaya</td>
                        <td>Main Weight Room</td>
                    </tr>
                    <tr>
                        <td>Saturday</td>
                        <td>8:00 AM - 9:30 AM</td>
                        <td>Anuja Rajakaruna</td>
                        <td>Main Weight Room</td>
                    </tr>
                </tbody>
            </table><br><br>
            
    <div class="container">
        <div class="content">
        <form action="process_contribution.php" method="post" enctype="multipart/form-data" class="contribution-form">
    <div class="form-group">
        <label for="contribute-name">Your Name:</label>
        <input type="text" id="contribute-name" name="contribute-name" placeholder="Enter your name" required>
    </div>
    <div class="form-group">
        <label for="contribute-email">Email Address:</label>
        <input type="email" id="contribute-email" name="contribute-email" placeholder="Enter your email address" required>
    </div>
    <div class="form-group">
        <label for="contribute-before">Before Stats:</label>
        <textarea id="contribute-before" name="contribute-before" rows="3" placeholder="Share your starting measurements, max lifts, etc." required></textarea>
    </div>
    <div class="form-group">
        <label for="contribute-after">After Stats:</label>
        <textarea id="contribute-after" name="contribute-after" rows="3" placeholder="Share your progress, improvements, max lifts, etc." required></textarea>
    </div>
    <div class="form-group">
        <label for="contribute-experience">Your Experience:</label>
        <textarea id="contribute-experience" name="contribute-experience" rows="5" placeholder="Share your journey, challenges, breakthroughs, and tips for others" required></textarea>
    </div>
    <div class="form-group">
        <label for="contribute-photo">Upload Before/After Photos (optional):</label>
        <input type="file" id="contribute-photo" name="contribute-photo[]" multiple>
        <small>Tip: Name your files with "before" or "after" to help us categorize them correctly</small>
    </div>
    <button type="submit" class="btn">Submit Your Story</button>
</form>
            <h1>Thank You for Your Contribution!</h1>
            <p>Your fitness journey and experience have been successfully submitted.</p>
            <p>Your story will inspire others in their fitness journey.</p>
            <a href="../front-end/class.php" class="btn">Back to Classes</a>
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

