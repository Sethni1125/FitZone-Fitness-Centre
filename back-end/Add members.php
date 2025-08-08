<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

<?php
header('Content-Type: application/json');
include 'db_connection.php'; 

$full_name = isset($_POST['memberName']) ? trim($_POST['memberName']) : '';
$email = isset($_POST['memberEmail']) ? trim($_POST['memberEmail']) : '';
$phone = isset($_POST['memberPhone']) ? trim($_POST['memberPhone']) : '';
$type = isset($_POST['membershipType']) ? trim($_POST['membershipType']) : '';
$notes = isset($_POST['memberNotes']) ? trim($_POST['memberNotes']) : '';

if ($full_name === '' || $email === '' || $type === '') {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO members (full_name, email, phone, membership_type, notes) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $full_name, $email, $phone, $type, $notes);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Member added successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add member. Maybe email already exists.']);
}

$stmt->close();
$conn->close();
?>
