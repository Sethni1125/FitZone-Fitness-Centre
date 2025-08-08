<?php
header('Content-Type: application/json');

// Database credentials
$host = 'localhost';
$db = 'fitzone_db'; // Change to your DB name
$user = 'root';     // Your DB username
$pass = '';         // Your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get POST data
$classType = $_POST['classType'] ?? '';
$trainerId = $_POST['classTrainer'] ?? '';
$classDate = $_POST['classDate'] ?? '';
$classTime = $_POST['classTime'] ?? '';
$duration = $_POST['classDuration'] ?? '';
$description = $_POST['classDescription'] ?? '';

// Validate required fields
if (empty($classType) || empty($trainerId) || empty($classDate) || empty($classTime) || empty($duration)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO classes (class_type, trainer_id, class_date, class_time, duration, description)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$classType, $trainerId, $classDate, $classTime, $duration, $description]);

    echo json_encode(['status' => 'success', 'message' => 'Class added successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Insert failed: ' . $e->getMessage()]);
}
?>
