<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "fitzone1";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first = $_POST["first_name"];
    $last = $_POST["last_name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $bmi = $_POST["bmi"];

    echo "Attempting to insert: $first, $last, $email, $username, [password-hashed], $bmi<br>";

    if (!isset($_POST["terms"])) {
        echo "<script>alert('You must agree to the Terms of Service and Privacy Policy');</script>";
    } else {
        $sql = "INSERT INTO sign_up (first_name, last_name, email, username, password, bmi) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("SQL prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $first, $last, $email, $username, $password, $bmi);

        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully!'); window.location.href='l.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>


<html data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - FitZon</title>
    <link rel="stylesheet" href="../css/S.css">
</head>
<body>

<div class="theme-toggle" onclick="toggleTheme()">
        <svg id="moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05A1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/>
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
    <div class="logo-container">
        <div class="logo">FitZon</div>
        <div class="logo-tagline">Stay fit, stay strong</div>
    </div>

    <div class="neumorphic-card">
        <h1>Create Account</h1>
        <form action="" method="post">
            <div class="input-group side-by-side">
                <input type="text" class="neumorphic-input" placeholder="First Name" name="first_name" required>
                <input type="text" class="neumorphic-input" placeholder="Last Name" name="last_name" required>
            </div>

            <div class="input-group">
                <input type="email" class="neumorphic-input" placeholder="Email Address" name="email" required>
            </div>

            <div class="input-group">
                <input type="text" class="neumorphic-input" placeholder="Username" name="username" required>
            </div>

            <div class="input-group">
                <input type="password" class="neumorphic-input" placeholder="Password" name="password" required>
            </div>

            <div class="input-group">
                <input type="password" class="neumorphic-input" placeholder="Confirm Password" name="confirm_password" required oninput="checkPasswords()">
            </div>

            <div class="input-group" id="bmi-group">
                <input type="text" class="neumorphic-input" placeholder="BMI" name="bmi" id="bmi-input">
            </div>

            <div class="checkbox-container">
                <input type="checkbox" name="terms" id="terms" required>
                <label for="terms" class="checkbox-label">
                    I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="neumorphic-button" id="submit-btn">Create Account</button>
        </form>

        <div class="links">
            Already have an account? <a href="../front-end/l.php">Log In</a>
        </div>
    </div>
</div>

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute("data-theme");
        html.setAttribute("data-theme", currentTheme === "dark" ? "light" : "dark");
    }

    function checkPasswords() {
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
        const submitBtn = document.getElementById('submit-btn');
        
        if (password !== confirmPassword && confirmPassword !== '') {
            document.querySelector('input[name="confirm_password"]').style.boxShadow = "0 0 0 2px #ff3860";
            submitBtn.disabled = true;
        } else {
            document.querySelector('input[name="confirm_password"]').style.boxShadow = "";
            submitBtn.disabled = false;
        }
    }

</script>

</body>
</html>
