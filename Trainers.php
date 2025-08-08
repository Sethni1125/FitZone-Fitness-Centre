<?php
$host = 'localhost';
$dbname = 'fitzone1';
$username = 'root';
$password = '';
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = $_POST['edit_id'] ?? null;
    $title = $_POST['title'];
    $full_name = $_POST['full_name'];
    $specialty = $_POST['specialty'];
    $bio = $_POST['bio'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $experience_years = $_POST['experience_years'];
    $certifications = $_POST['certifications'];
    $username_input = $_POST['username'];
    $password_input = $_POST['password'];
    $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);


    $image_path = $_POST['existing_image'] ?? '';

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = uniqid() . '-' . $_FILES['profile_image']['name'];
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_path = $target_dir . basename($image_name);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path);
    }

    if ($edit_id) {
        $stmt = $pdo->prepare("UPDATE instructors SET title=?, full_name=?, specialty=?, bio=?, email=?, phone=?, location=?, experience_years=?, certifications=?, profile_image=? WHERE id=?");
        $stmt->execute([$title, $full_name, $specialty, $bio, $email, $phone, $location, $experience_years, $certifications, $image_path, $edit_id]);
    } else {
        $check = $pdo->prepare("SELECT COUNT(*) FROM instructors WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Instructor with this email already exists!');</script>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO instructors (title, full_name, specialty, bio, email, phone, location, experience_years, certifications, profile_image, username, password)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");       
            $stmt->execute([$title, $full_name, $specialty, $bio, $email, $phone, $location, $experience_years, $certifications, $image_path, $username_input, $hashed_password]);

        }
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $get = $pdo->prepare("SELECT profile_image FROM instructors WHERE id = ?");
    $get->execute([$delete_id]);
    $instructor = $get->fetch();

    if ($instructor && file_exists($instructor['profile_image'])) {
        unlink($instructor['profile_image']);
    }

    $del = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
    $del->execute([$delete_id]);

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
?>

<html lang="en" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <title>Instructors Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f5f5f5;
            --text-color: #333;
            --card-bg: #ffffff;
            --header-color: #0c3c60;
            --border-color: #eee;
            --feature-bg: #e3f2fd;
            --hero-bg: #f0f9ff;
            --shadow-color: rgba(0,0,0,0.1);
            --btn-primary: #2e86de;
            --btn-text: #ffffff;
            --table-header: #f5f5f5;
            --modal-bg: #ffffff;
            --input-bg: #ffffff;
            --input-text: #333;
            --nav-bg: #ffffff;
            --nav-text: #333;
            --nav-active: #007bff;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #e0e0e0;
            --card-bg: #242424;
            --header-color: #4da6ff;
            --border-color: #444;
            --feature-bg: #1e3a5f;
            --hero-bg: #1a2a3a;
            --shadow-color: rgba(0,0,0,0.3);
            --btn-primary: #3498db;
            --btn-text: #ffffff;
            --table-header: #333;
            --modal-bg: #333;
            --input-bg: #424242;
            --input-text: #e0e0e0;
            --nav-bg: #242424;
            --nav-text: #e0e0e0;
            --nav-active: #4da6ff;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .instructor-card {
            border-radius: 15px;
            box-shadow: 0 4px 10px var(--shadow-color);
            overflow: hidden;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .instructor-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
        }

        .instructor-info {
            padding: 15px;
            color: var(--text-color);
        }

        .instructor-info h5 {
            margin: 0;
            font-weight: bold;
            color: var(--header-color);
        }

        .icon {
            margin-right: 5px;
            color: var(--btn-primary);
        }

        .nav-tabs {
            border-color: var(--border-color);
        }

        .nav-tabs .nav-link {
            color: var(--nav-text);
            background-color: var(--nav-bg);
            border-color: var(--border-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--nav-active);
            background-color: var(--nav-bg);
            border-color: var(--border-color);
            border-bottom-color: transparent;
        }

        .modal-content {
            background-color: var(--modal-bg);
            color: var(--text-color);
        }

        .form-control, .form-select {
            background-color: var(--input-bg);
            color: var(--input-text);
            border-color: var(--border-color);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        .text-muted {
            color: var(--text-color) !important;
            opacity: 0.7;
        }

        .btn-primary {
            background-color: var(--btn-primary);
            border-color: var(--btn-primary);
            color: var(--btn-text);
        }

        .back-btn {
            margin-right: 10px;
            color: var(--text-color);
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: var(--btn-primary);
        }

        .back-btn i {
            margin-right: 5px;
            font-size: 16px;
        }

        /* Theme toggle switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--btn-primary);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .theme-toggle i {
            font-size: 1.2rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
        }
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--card-bg);
            box-shadow: 0 2px 10px var(--shadow-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
        }

        .theme-toggle svg {
            width: 24px;
            height: 24px;
            fill: var(--text-color);
        }

        #moon {
            display: block;
        }

        #sun {
            display: none;
        }

        [data-theme="dark"] #moon {
            display: none;
        }

        [data-theme="dark"] #sun {
            display: block;
        }

        /* Dark mode adjustments for neumorphic card */
        [data-theme="dark"] .neumorphic-card {
            background: #2d3748;
            box-shadow: 10px 10px 20px #1a202c, -10px -10px 20px #3a4a63;
        }

        [data-theme="dark"] .neumorphic-input {
            background: #2d3748;
            box-shadow: inset 8px 8px 16px #1a202c, inset -8px -8px 16px #3a4a63;
            color: #e0e0e0;
        }

        [data-theme="dark"] .neumorphic-button {
            background: #2d3748;
            box-shadow: 8px 8px 16px #1a202c, -8px -8px 16px #3a4a63;
            color: #e0e0e0;
        }

        [data-theme="dark"] .neumorphic-button:hover {
            box-shadow: inset 8px 8px 16px #1a202c, inset -8px -8px 16px #3a4a63;
        }

        [data-theme="dark"] h1 {
            color: #e0e0e0;
        }

        [data-theme="dark"] .user-type-option {
            background: #2d3748;
            box-shadow: 5px 5px 10px #1a202c, -5px -5px 10px #3a4a63;
            color: #e0e0e0;
        }

        [data-theme="dark"] .user-type-option.active {
            box-shadow: inset 5px 5px 10px #1a202c, inset -5px -5px 10px #3a4a63;
            color: #4da6ff;
        }


        /* Back button positioned at the bottom left */
        .back-btn {
            position: fixed;
            left: 20px;
            bottom: 20px;
            display: flex;
            align-items: center;
            color: var(--btn-text);
            background-color: var(--btn-primary);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            box-shadow: 0 2px 10px var(--shadow-color);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background-color: var(--header-color);
            transform: translateX(-5px);
        }

        .back-btn i {
            margin-right: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <a href="Admin Home.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Admin Home
            </a>
            <h3 class="mb-0 ms-3"><i class="fas fa-chalkboard-teacher me-2"></i>Instructors Management</h3>
        </div>
        
        <div class="theme-toggle" id="themeToggle">
        <svg id="moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05A1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/>
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
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active">Grid View</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="modal" data-bs-target="#instructorModal" onclick="resetForm()">Add Instructors</a>
        </li>
    </ul>

    <div class="row g-4">
        <?php
        $stmt = $pdo->query("SELECT * FROM instructors ORDER BY id DESC");
        while ($row = $stmt->fetch()):
        ?>
        <div class="col-md-4">
            <div class="card instructor-card">
                <img src="<?= htmlspecialchars($row['profile_image']) ?>" alt="Instructor Image">
                <div class="instructor-info">
                    <h5><?= htmlspecialchars($row['title'] . ' ' . $row['full_name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($row['specialty']) ?></p>
                    <p><?= htmlspecialchars($row['bio']) ?></p>
                    <p><i class="fas fa-map-marker-alt icon"></i><?= htmlspecialchars($row['location']) ?></p>
                    <p><i class="fas fa-phone icon"></i><?= htmlspecialchars($row['phone']) ?></p>
                    <p><i class="fas fa-envelope icon"></i><?= htmlspecialchars($row['email']) ?></p>
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <button class="btn btn-sm btn-warning" 
                                    onclick='editInstructor(<?= json_encode($row) ?>)'>
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="instructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Instructor Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="existing_image" id="existing_image">

                <div class="col-md-4">
                    <label>Profile Image</label>
                    <input type="file" name="profile_image" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Title</label>
                    <select name="title" id="title" class="form-select" required>
                        <option value="Mr.">Mr.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Miss.">Miss.</option>
                        <option value="Dr.">Dr.</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>

                <div class="col-md-6">
                    <label>Specialty</label>
                    <input type="text" name="specialty" id="specialty" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Experience (Years)</label>
                    <input type="number" name="experience_years" id="experience_years" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label>Bio</label>
                    <textarea name="bio" id="bio" class="form-control" rows="3" required></textarea>
                </div>
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Location</label>
                    <input type="text" name="location" id="location" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Certifications</label>
                    <input type="text" name="certifications" id="certifications" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Instructor</button>
            </div>
        </form>
    </div>
</div>

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


    function editInstructor(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('existing_image').value = data.profile_image;
        document.getElementById('title').value = data.title;
        document.getElementById('full_name').value = data.full_name;
        document.getElementById('specialty').value = data.specialty;
        document.getElementById('experience_years').value = data.experience_years;
        document.getElementById('bio').value = data.bio;
        document.getElementById('email').value = data.email;
        document.getElementById('phone').value = data.phone;
        document.getElementById('location').value = data.location;
        document.getElementById('certifications').value = data.certifications;
        var modal = new bootstrap.Modal(document.getElementById('instructorModal'));
        modal.show();
    }

    function resetForm() {
        document.querySelector('#instructorModal form').reset();
        document.getElementById('edit_id').value = '';
        document.getElementById('existing_image').value = '';
    }

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this instructor?")) {
            window.location.href = "?delete_id=" + id;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        
        themeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.documentElement.setAttribute('data-theme', 'dark');
                setCookie('theme', 'dark', 365);
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                setCookie('theme', 'light', 365);
            }
        });
        
        
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>