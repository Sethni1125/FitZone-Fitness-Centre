<?php

$host = "localhost";
$username = "root"; 
$password = ""; 
$database = "fitzone1"; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = "";
$email = "";
$before_stats = "";
$after_stats = "";
$experience = "";
$photos = array();
$success_message = "";
$error_message = "";
$new_contribution_id = null;

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['contribute-name']);
    $email = $conn->real_escape_string($_POST['contribute-email']);
    $before_stats = $conn->real_escape_string($_POST['contribute-before']);
    $after_stats = $conn->real_escape_string($_POST['contribute-after']);
    $experience = $conn->real_escape_string($_POST['contribute-experience']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else {
        // Insert contribution data into the database
        $sql = "INSERT INTO client_contributions (name, email, before_stats, after_stats, experience) 
                VALUES ('$name', '$email', '$before_stats', '$after_stats', '$experience')";
        
        if ($conn->query($sql) === TRUE) {
            $new_contribution_id = $conn->insert_id;
            $success_message = "Your contribution has been successfully submitted!";
            
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
                        $error_message .= "File size exceeds the limit (5MB) for " . $file_name . "<br>";
                        continue;
                    }
                    
                    // Check file type (allow only image files)
                    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
                    if (!in_array($file_ext, $allowed_types)) {
                        $error_message .= "Only JPG, JPEG, PNG, and GIF files are allowed. " . $file_name . " is not allowed.<br>";
                        continue;
                    }
                    
                    // Upload the file
                    if ($file_error === 0) {
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            // Determine if it's a before or after photo based on naming convention
                            $photo_type = (strpos(strtolower($file_name), 'before') !== false) ? 'before' : 
                                        ((strpos(strtolower($file_name), 'after') !== false) ? 'after' : NULL);
                            
                            // Insert file details into the database
                            $photo_sql = "INSERT INTO contribution_photos (contribution_id, photo_filename, photo_type) 
                                        VALUES ('$new_contribution_id', '$new_file_name', '$photo_type')";
                            
                            if ($conn->query($photo_sql) === TRUE) {
                                // Add to photos array for display
                                $photos[] = array(
                                    'filename' => $new_file_name,
                                    'path' => $upload_path,
                                    'type' => $photo_type,
                                    'original_name' => $file_name
                                );
                            } else {
                                $error_message .= "Database error when saving photo: " . $conn->error . "<br>";
                            }
                        } else {
                            $error_message .= "Failed to upload file: " . $file_name . "<br>";
                        }
                    } else {
                        $error_message .= "Error uploading file: " . $file_error . "<br>";
                    }
                }
            }
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch all contributions, including the newly added one
$contributions = array();
$sql = "SELECT * FROM client_contributions ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $contribution = $row;
        
        // Fetch photos for this contribution
        $contribution['photos'] = array();
        $photo_sql = "SELECT * FROM contribution_photos WHERE contribution_id = " . $row['id'];
        $photo_result = $conn->query($photo_sql);
        
        if ($photo_result->num_rows > 0) {
            while($photo_row = $photo_result->fetch_assoc()) {
                $contribution['photos'][] = array(
                    'filename' => $photo_row['photo_filename'],
                    'path' => "uploads/contributions/" . $photo_row['photo_filename'],
                    'type' => $photo_row['photo_type']
                );
            }
        }
        
        $contributions[] = $contribution;
    }
}

// Get theme setting from local storage
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

$conn->close();
?>

<html data-theme="<?php echo $theme; ?>">
<head>
    <title>Transformation Stories - FitZone Fitness Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #3498db;
            --text-color: #333;
            --background-color: #f5f8fa;
            --card-background: #ffffff;
            --border-color: #e1e8ed;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --highlight-color: rgba(52, 152, 219, 0.1);
        }
        
        [data-theme="dark"] {
            --primary-color: #3498db;
            --text-color: #f5f5f5;
            --background-color: #1a1a1a;
            --card-background: #2c2c2c;
            --border-color: #444;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --highlight-color: rgba(52, 152, 219, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color 0.3s, color 0.3s;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background-color: var(--card-background);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px var(--shadow-color);
            z-index: 1000;
        }


        
        .theme-toggle svg {
            width: 24px;
            height: 24px;
            fill: var(--text-color);
        }
        
        [data-theme="light"] #moon {
            display: block;
        }
        
        [data-theme="light"] #sun {
            display: none;
        }
        
        [data-theme="dark"] #moon {
            display: none;
        }
        
        [data-theme="dark"] #sun {
            display: block;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .title {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--primary-color);
            position: relative;
            display: inline-block;
        }
        
        h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        h3 {
            font-size: 1.2rem;
            margin: 0 0 10px;
            color: var(--primary-color);
        }
        
        .card {
            background-color: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 4px 12px var(--shadow-color);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .contributions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .contribution-card {
            background-color: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 2px 8px var(--shadow-color);
            padding: 15px;
            font-size: 0.9rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .contribution-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px var(--shadow-color);
        }
        
        .new-contribution {
            border: 2px solid var(--primary-color);
            position: relative;
        }
        
        .new-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .message {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }
        
        .error {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--error-color);
            color: var(--error-color);
        }
        
        .detail-section {
            margin-bottom: 10px;
        }
        
        .detail-label {
            font-weight: bold;
            font-size: 0.8rem;
            margin-bottom: 2px;
            display: block;
            color: var(--primary-color);
        }
        
        .detail-content {
            background-color: rgba(52, 152, 219, 0.05);
            padding: 8px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            white-space: pre-line;
            font-size: 0.85rem;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .photos-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .photo-card {
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 4px var(--shadow-color);
            position: relative;
        }
        
        .photo-card img {
            width: 100%;
            display: block;
            object-fit: cover;
            height: 120px;
        }
        
        .photo-type {
            position: absolute;
            top: 5px;
            right: 5px;
            padding: 3px 6px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .photo-type.before {
            background-color: rgba(52, 152, 219, 0.8);
            color: white;
        }
        
        .photo-type.after {
            background-color: rgba(46, 204, 113, 0.8);
            color: white;
        }
        
        .photo-name {
            display: none; /* Hide the filename to save space */
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #888;
            grid-column: span 3;
        }
        
        .date-display {
            color: #888;
            font-size: 0.7rem;
            margin-bottom: 8px;
        }
        
        .member-name {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .read-more {
            text-align: center;
            margin-top: auto;
            margin-bottom: 5px;
        }
        
        .read-more-btn {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        
        .read-more-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Aurora effect similar to your original page */
        .aurora {
            position: absolute;
            top: -50px;
            left: 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            z-index: -1;
        }
        
        .aurora_item {
            position: absolute;
            width: 300%;
            height: 300%;
            background: linear-gradient(135deg, #3498db, #2ecc71, #3498db, #9b59b6);
            background-size: 400% 400%;
            opacity: 0.2;
            border-radius: 40%;
            animation: aurora-animation 15s infinite linear;
            transform-origin: center;
        }
        
        .aurora_item:nth-child(1) {
            animation-delay: -5s;
        }
        
        .aurora_item:nth-child(2) {
            animation-delay: -10s;
            animation-duration: 20s;
        }
        
        .aurora_item:nth-child(3) {
            animation-delay: -15s;
            animation-duration: 25s;
        }
        
        @keyframes aurora-animation {
            0% {
                transform: rotate(0deg) translate(-30%, -30%);
                background-position: 0% 0%;
            }
            100% {
                transform: rotate(360deg) translate(-30%, -30%);
                background-position: 100% 100%;
            }
        }
        
        /* Modal styles for showing full contribution details */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }
        
        .modal-content {
            background-color: var(--card-background);
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px var(--shadow-color);
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .modal-photos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .modal-photo img {
            height: 180px;
        }
        
        @media (max-width: 992px) {
            .contributions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .empty-state {
                grid-column: span 2;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .title {
                font-size: 1.8rem;
            }
            
            h2 {
                font-size: 1.4rem;
            }
            
            .card {
                padding: 15px;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .contributions-grid {
                grid-template-columns: 1fr;
            }
            
            .empty-state {
                grid-column: span 1;
            }
        }
    </style>
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
        <div class="header">
            <h1 class="title">
                FitZone Fitness Center
                <div class="aurora">
                    <div class="aurora_item"></div>
                    <div class="aurora_item"></div>
                    <div class="aurora_item"></div>
                </div>
            </h1>
            <h2>Transformation Stories</h2>
        </div>
        
        <div class="card">
            <?php if (!empty($success_message)): ?>
                <div class="message success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="message error">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="contributions-grid">
                <?php if (count($contributions) > 0): ?>
                    <?php foreach ($contributions as $index => $contribution): ?>
                        <div class="contribution-card <?php echo ($new_contribution_id == $contribution['id']) ? 'new-contribution' : ''; ?>" 
                            data-id="<?php echo $contribution['id']; ?>">
                            
                            <?php if ($new_contribution_id == $contribution['id']): ?>
                                <span class="new-badge">NEW</span>
                            <?php endif; ?>
                            
                            <div class="date-display">
                                <?php echo date('M j, Y', strtotime($contribution['created_at'])); ?>
                            </div>
                            
                            <div class="member-name">
                                <?php echo htmlspecialchars($contribution['name']); ?>
                            </div>
                            
                            <div class="detail-section">
                                <div class="detail-label">Before:</div>
                                <div class="detail-content">
                                    <?php 
                                    $before_text = htmlspecialchars($contribution['before_stats']);
                                    echo (strlen($before_text) > 100) ? substr($before_text, 0, 100) . '...' : $before_text; 
                                    ?>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <div class="detail-label">After:</div>
                                <div class="detail-content">
                                    <?php 
                                    $after_text = htmlspecialchars($contribution['after_stats']);
                                    echo (strlen($after_text) > 100) ? substr($after_text, 0, 100) . '...' : $after_text; 
                                    ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($contribution['photos'])): ?>
                                <div class="photos-container">
                                    <?php 
                                    // Show max 2 photos in the grid view
                                    $photo_count = min(count($contribution['photos']), 2);
                                    for ($i = 0; $i < $photo_count; $i++): 
                                        $photo = $contribution['photos'][$i];
                                    ?>
                                        <div class="photo-card">
                                            <img src="<?php echo $photo['path']; ?>" alt="Fitness journey photo">
                                            <?php if (!empty($photo['type'])): ?>
                                                <div class="photo-type <?php echo $photo['type']; ?>"><?php echo $photo['type']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="read-more">
                                <button class="read-more-btn" onclick="openModal(<?php echo $contribution['id']; ?>)">Read More</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No contributions yet</h3>
                        <p>Be the first to share your fitness journey!</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="btn-container">
                <a href="../front-end/class.php" class="btn">Back to Classes</a>
                <a href="../front-end/Cardio Workouts.php#container" class="btn">Share Your Story</a>
            </div>
        </div>
    </div>
    
    <!-- Modal for showing full contribution details -->
    <div id="contributionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle"></h3>
            <div class="date-display" id="modalDate"></div>
            
            <div class="detail-section">
                <div class="detail-label">Before Stats:</div>
                <div class="detail-content" id="modalBefore"></div>
            </div>
            
            <div class="detail-section">
                <div class="detail-label">After Stats:</div>
                <div class="detail-content" id="modalAfter"></div>
            </div>
            
            <div class="detail-section">
                <div class="detail-label">Experience:</div>
                <div class="detail-content" id="modalExperience"></div>
            </div>
            
            <h3>Transformation Photos</h3>
            <div class="modal-photos" id="modalPhotos"></div>
        </div>
    </div>
    
    <script>
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        
        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme', newTheme);
            document.cookie = "theme=" + newTheme + ";path=/;max-age=31536000"; // Save for 1 year
        });
        
        // Initialize contribution data for modals
        const contributionData = <?php echo json_encode($contributions); ?>;
        const modal = document.getElementById('contributionModal');
        
        // Open modal with contribution details
        function openModal(id) {
    // Find the contribution by ID
    const contribution = contributionData.find(item => item.id == id);
    if (!contribution) return;
    
    // Populate modal with data
    document.getElementById('modalTitle').textContent = contribution.name;
    document.getElementById('modalDate').textContent = new Date(contribution.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('modalBefore').textContent = contribution.before_stats;
    document.getElementById('modalAfter').textContent = contribution.after_stats;
    document.getElementById('modalExperience').textContent = contribution.experience;
    
    // Display photos
    const photosContainer = document.getElementById('modalPhotos');
    photosContainer.innerHTML = '';
    
    if (contribution.photos && contribution.photos.length > 0) {
        contribution.photos.forEach(photo => {
            const photoDiv = document.createElement('div');
            photoDiv.className = 'photo-card modal-photo';
            
            const img = document.createElement('img');
            img.src = photo.path;
            img.alt = 'Transformation photo';
            photoDiv.appendChild(img);
            
            if (photo.type) {
                const typeSpan = document.createElement('div');
                typeSpan.className = 'photo-type ' + photo.type;
                typeSpan.textContent = photo.type;
                photoDiv.appendChild(typeSpan);
            }
            
            photosContainer.appendChild(photoDiv);
        });
    } else {
        photosContainer.innerHTML = '<p>No photos available</p>';
    }
    
    // Show the modal
    modal.style.display = 'block';
}

function closeModal() {
    modal.style.display = 'none';
}

// Close the modal when clicking outside of the modal content
window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}
</script>
</body>
</html>