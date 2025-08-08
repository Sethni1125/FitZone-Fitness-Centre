<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "fitzone1";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Connection failed: " . $conn->connect_error
        ]);
        exit();
    }

    $fullName = $_POST["memberName"];
    $email = $_POST["memberEmail"];
    $phone = $_POST["memberPhone"];
    $type = $_POST["membershipType"];

    // --- Handle Image Upload ---
    $imageFileName = "";
    if (isset($_FILES["memberImage"]) && $_FILES["memberImage"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/"; // Make sure this folder exists and is writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = basename($_FILES["memberImage"]["name"]);
        $imageFileName = time() . "_" . $originalName;
        $targetPath = $uploadDir . $imageFileName;

        if (!move_uploaded_file($_FILES["memberImage"]["tmp_name"], $targetPath)) {
            echo json_encode([
                "status" => "error",
                "message" => "❌ Failed to upload image."
            ]);
            exit();
        }
    }

    // Insert into database including image
    $stmt = $conn->prepare("INSERT INTO members (full_name, email, phone, membership_type, profile_image) VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Prepare failed: " . $conn->error
        ]);
        $conn->close();
        exit();
    }

    $stmt->bind_param("sssss", $fullName, $email, $phone, $type, $imageFileName);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "✅ Member added successfully!"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Execute failed: " . $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
