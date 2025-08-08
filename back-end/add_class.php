<?php
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "fitzone1";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        $message = "❌ Database connection failed: " . $conn->connect_error;
    } else {
        $classType = $_POST['classType'] ?? '';
        $trainer = $_POST['classTrainer'] ?? '';
        $classDate = $_POST['classDate'] ?? '';
        $classTime = $_POST['classTime'] ?? '';
        $classDuration = $_POST['classDuration'] ?? '';
        $classDescription = $_POST['classDescription'] ?? '';
        
        $viewDetailsLink = $_POST['viewDetailsLink'] ?? '';
        $bookNowLink = $_POST['bookNowLink'] ?? '';

        $imageName = "";
        if (isset($_FILES['classImage']) && $_FILES['classImage']['error'] === UPLOAD_ERR_OK) {
            $imageTmp = $_FILES['classImage']['tmp_name'];
            $imageName = basename($_FILES['classImage']['name']);
            $uploadDir = '../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $targetPath = $uploadDir . $imageName;

            if (!move_uploaded_file($imageTmp, $targetPath)) {
                $message = "❌ Failed to upload image.";
                $imageName = ""; 
            }
        }

        if (empty($classType) || empty($trainer) || empty($classDate) || empty($classTime) || empty($classDuration) || empty($imageName)) {
            $message = "⚠️ Please fill in all required fields including the image.";
        } else {
            $stmt = $conn->prepare("INSERT INTO class (class_type, trainer, class_date, class_time, duration, description, image, view_details_link, book_now_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssssissss", $classType, $trainer, $classDate, $classTime, $classDuration, $classDescription, $imageName, $viewDetailsLink, $bookNowLink);
                if ($stmt->execute()) {
                    $message = "✅ Class added successfully!";
                } else {
                    $message = "❌ Error adding class: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "❌ Failed to prepare statement: " . $conn->error;
            }
        }

        $conn->close();
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Class | FitZone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form > div {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background-color: #00a86b;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            color: #e60000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Class</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div>
                <label for="classType">Class Type*</label>
                <input type="text" id="classType" name="classType" required>
            </div>
            <div>
                <label for="classTrainer">Trainer Name*</label>
                <input type="text" id="classTrainer" name="classTrainer" required>
            </div>
            <div>
                <label for="classDate">Class Date*</label>
                <input type="date" id="classDate" name="classDate" required>
            </div>
            <div>
                <label for="classTime">Class Time*</label>
                <input type="time" id="classTime" name="classTime" required>
            </div>
            <div>
                <label for="classDuration">Duration (in minutes)*</label>
                <input type="number" id="classDuration" name="classDuration" required>
            </div>
            <div>
                <label for="classDescription">Description</label>
                <textarea id="classDescription" name="classDescription" rows="3"></textarea>
            </div>
            <div>
                <label for="classImage">Class Image*</label>
                <input type="file" id="classImage" name="classImage" accept="image/*" required>
            </div>
            <div>
                <label for="viewDetailsLink">View Details Link*</label>
                <input type="text" id="viewDetailsLink" name="viewDetailsLink" placeholder="Link to view details" required>
            </div>
            <div>
                <label for="bookNowLink">Book Now Link*</label>
                <input type="text" id="bookNowLink" name="bookNowLink" placeholder="Link to book class" required>
            </div>
            <div style="text-align:center;">
                <button type="submit">Add Class</button>
            </div>
        </form>
    </div>
</body>
</html>
