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
        
        <div class="hero">
                <h2>High-Intensity Cardio Workouts</h2>
                <p>Transform your fitness with our dynamic, science-backed cardio program designed to boost endurance, burn calories, and elevate your metabolic performance.</p>
                <a href="#contribute" class="btn btn-secondary">Contribute</a>
            </div>
        
            <h3>About This Class</h3>
            <p>Our High-Intensity Cardio Workouts are meticulously crafted to push your cardiovascular limits and dramatically improve your overall fitness. This class combines cutting-edge interval training techniques, functional movements, and heart-rate-targeted exercises to deliver maximum results in minimal time.</p>
            
            <p>Whether you're an athlete looking to enhance performance, a fitness enthusiast aiming to shed fat, or someone seeking to improve overall health, our program offers a challenging and rewarding experience tailored to diverse fitness levels.</p>
            
            <div class="instructor-profile">
                <div class="instructor-image">
                    <img src="../img/Maneesha1.jpeg" alt="Mrs. Maneesha Perera">
                </div>
                <div class="instructor-info">
                    <h3>Mrs. Maneesha Perera</h3>
                    <p>A certified HIIT and endurance training specialist with a background in sports science, Nethmi brings a unique blend of scientific knowledge and practical experience. She holds advanced certifications in metabolic conditioning and performance optimization.</p>
                    <p>"My training philosophy centers on smart, efficient workouts that challenge your body and mind. Every session is an opportunity to redefine your limits and discover your true potential."</p>
                </div>
            </div>
            
            <div class="instructor-profile">
                <div class="instructor-image">
                    <img src="../img/anuja.jpeg" alt="Mr. Anuja Rajakaruna">
                </div>
                <div class="instructor-info">
                    <h3>Mr. Anuja Rajakaruna</h3>
                    <p>An accomplished marathon runner and functional fitness expert, Dilshan specializes in developing comprehensive cardiovascular training programs. He emphasizes technique, mental resilience, and personalized progression.</p>
                    <p>"Cardio isn't just about burning calories, it's about building a resilient body and an unbreakable spirit. We'll transform your perception of what's possible."</p>
                </div>
            </div>
            
            <h3>Class Features</h3>
            <div class="class-features">
                <div class="feature">
                    <div class="feature-icon">❤️</div>
                    <h4>Heart Rate Optimization</h4>
                    <p>Utilize zone-based training to maximize cardiovascular efficiency and metabolic adaptation.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🏃</div>
                    <h4>Multi-Modal Training</h4>
                    <p>Integrate running, rowing, cycling, and bodyweight exercises for comprehensive fitness development.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">⏱️</div>
                    <h4>Interval Protocols</h4>
                    <p>Implement advanced interval techniques like Tabata, AMRAP, and pyramid training to boost endurance and burn fat.</p>
                </div>
            </div>
            
            <h3>Sample Workout</h3>
            <div class="workout-schedule">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exercise</th>
                            <th>Duration/Reps</th>
                            <th>Rest</th>
                            <th>Focus</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>High Knees</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Warm-up</td>
                            <td>Maintain high intensity, engage core</td>
                        </tr>
                        <tr>
                            <td>Burpees</td>
                            <td>30 sec</td>
                            <td>30 sec</td>
                            <td>Full Body</td>
                            <td>Complete range of motion, explosive movement</td>
                        </tr>
                        <tr>
                            <td>Mountain Climbers</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Core/Cardio</td>
                            <td>Keep hips low, rapid leg movement</td>
                        </tr>
                        <tr>
                            <td>Jump Squats</td>
                            <td>30 sec</td>
                            <td>30 sec</td>
                            <td>Lower Body</td>
                            <td>Maximum height, soft landing</td>
                        </tr>
                        <tr>
                            <td>Rowing Machine</td>
                            <td>500m Sprint</td>
                            <td>90 sec</td>
                            <td>Endurance</td>
                            <td>Maintain consistent stroke rate</td>
                        </tr>
                        <tr>
                            <td>Plank Jacks</td>
                            <td>45 sec</td>
                            <td>15 sec</td>
                            <td>Core Stability</td>
                            <td>Maintain plank position, explosive leg movement</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <h3 id="diet">Recommended Diet Plans</h3>
            <p>Nutrition plays a crucial role in supporting high-intensity cardio training. Here are sample diet plans to optimize your performance:</p>
            
            <div class="diet-plan">
                <h4>Performance Optimization Diet</h4>
                <div class="diet-day">
                    <h5>Daily Macros Target:</h5>
                    <p>Protein: 1.4-1.8g per kg of body weight<br>
                    Carbohydrates: 5-7g per kg of body weight<br>
                    Fats: 0.8-1.2g per kg of body weight</p>
                    
                    <h5>Sample Day:</h5>
                    <div class="meal">
                        <strong>Pre-Morning Workout:</strong>
                        <p>- Banana with almond butter<br>
                        - Small espresso shot</p>
                    </div>
                    <div class="meal">
                        <strong>Post-Workout Breakfast:</strong>
                        <p>- Protein smoothie (whey protein, mixed berries, spinach)<br>
                        - Whole grain toast with avocado</p>
                    </div>
                    <div class="meal">
                        <strong>Mid-Morning Snack:</strong>
                        <p>- Greek yogurt<br>
                        - Chia seeds<br>
                        - Handful of mixed nuts</p>
                    </div>
                    <div class="meal">
                        <strong>Lunch:</strong>
                        <p>- Grilled salmon<br>
                        - Quinoa salad<br>
                        - Roasted vegetables<br>
                        - Olive oil dressing</p>
                    </div>
                    <div class="meal">
                        <strong>Afternoon Snack:</strong>
                        <p>- Apple<br>
                        - Hard-boiled eggs<br>
                        - Hummus</p>
                    </div>
                    <div class="meal">
                        <strong>Dinner:</strong>
                        <p>- Lean turkey breast<br>
                        - Sweet potato<br>
                        - Steamed broccoli<br>
                        - Herb seasoning</p>
                    </div>
                    <div class="meal">
                        <strong>Evening Recovery:</strong>
                        <p>- Casein protein shake<br>
                        - Handful of tart cherries</p>
                    </div>
                </div>
            </div>
            
            <div class="diet-plan">
                <h4>Fat Loss and Endurance Diet</h4>
                <div class="diet-day">
                    <h5>Daily Macros Target:</h5>
                    <p>Protein: 1.6-2.0g per kg of body weight<br>
                    Carbohydrates: 3-5g per kg of body weight<br>
                    Fats: 0.5-1.0g per kg of body weight</p>
                    
                    <h5>Sample Day:</h5>
                    <div class="meal">
                        <strong>Early Morning:</strong>
                        <p>- Green tea<br>
                        - Small portion of overnight oats</p>
                    </div>
                    <div class="meal">
                        <strong>Post-Workout Breakfast:</strong>
                        <p>- Egg white omelet<br>
                        - Spinach and tomatoes<br>
                        - Small portion of whole grain toast</p>
                    </div>
                    <div class="meal">
                        <strong>Mid-Morning Snack:</strong>
                        <p>- Protein shake<br>
                        - Cucumber slices<br>
                        - Almonds</p>
                    </div>
                    <div class="meal">
                        <strong>Lunch:</strong>
                        <p>- Grilled chicken breast<br>
                        - Mixed green salad<br>
                        - Balsamic vinaigrette<br>
                        - Quinoa</p>
                    </div>
                    <div class="meal">
                        <strong>Afternoon Snack:</strong>
                        <p>- Protein bar<br>
                        - Celery sticks<br>
                        - Green apple</p>
                    </div>
                    <div class="meal">
                        <strong>Dinner:</strong>
                        <p>- Baked cod<br>
                        - Roasted vegetables<br>
                        - Cauliflower rice</p>
                    </div>
                    <div class="meal">
                        <strong>Evening Recovery:</strong>
                        <p>- Low-fat cottage cheese<br>
                        - Handful of berries</p>
                    </div>
                </div>
            </div>
            
            <h3>Class Schedule</h3>
            <p>High-Intensity Cardio Workouts are offered at the following times:</p>
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
                        <td>Tuesday</td>
                        <td>6:00 AM - 7:00 AM</td>
                        <td>Maneesha Perera</td>
                        <td>Cardio Studio</td>
                    </tr>
                    <tr>
                        <td>Wednesday</td>
                        <td>5:30 PM - 6:30 PM</td>
                        <td>Anuja Rajakaruna</td>
                        <td>Multi-Purpose Room</td>
                    </tr>
                    <tr>
                        <td>Friday</td>
                        <td>6:00 AM - 7:00 AM</td>
                        <td>Maneesha Perera</td>
                        <td>Cardio Studio</td>
                    </tr>
                    <tr>
                        <td>Saturday</td>
                        <td>9:00 AM - 10:00 AM</td>
                        <td>Anuja Rajakaruna</td>
                        <td>Multi-Purpose Room</td>
                    </tr>
                </tbody>
            </table>
            

    <div class="container">
        <div class="content">
        <form action="process_contribution1.php" method="post" enctype="multipart/form-data" class="contribution-form">
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

