<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone1";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$id = $full_name = $email = $phone = $membership_type = $status = $profile_image = "";
$errorMsg = $successMsg = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: members.php");
    exit();
}

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $membership_type = $_POST['membership_type'];
    $status = $_POST['status'];
    
    $uploadImage = false;
    $newImagePath = "";
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($fileExt, $allowed)) {
            $newFilename = uniqid() . '.' . $fileExt;
            $targetDir = "uploads/";
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $targetFile = $targetDir . $newFilename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                $uploadImage = true;
                $newImagePath = $targetFile;
            } else {
                $errorMsg = "Failed to upload image.";
            }
        } else {
            $errorMsg = "Invalid file format. Allowed formats: JPG, JPEG, PNG, GIF.";
        }
    }
    
    if (empty($errorMsg)) {
        $sql = "";
        if ($uploadImage) {
            $sql = "UPDATE members SET full_name = ?, email = ?, phone = ?, membership_type = ?, status = ?, profile_image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
        
            if (!$stmt) {
                die("Prepare failed: " . $conn->error); 
            }
        
            $stmt->bind_param("ssssssi", $full_name, $email, $phone, $membership_type, $status, $newImagePath, $id);
        } else {
            $sql = "UPDATE members SET full_name = ?, email = ?, phone = ?, membership_type = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
        
            if (!$stmt) {
                die("Prepare failed: " . $conn->error); 
            }
        
            $stmt->bind_param("sssssi", $full_name, $email, $phone, $membership_type, $status, $id);
        }
        
        
        if ($stmt->execute()) {
            $successMsg = "Member updated successfully!";
        } else {
            $errorMsg = "Error updating record: " . $stmt->error;
        }
    }}

        $sql = "SELECT * FROM members WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header("Location: members.php");
            exit();
        }

        $member = $result->fetch_assoc();
        $full_name = $member['full_name'];
        $email = $member['email'];
        $phone = $member['phone'];
        $membership_type = $member['membership_type'];
        $status = isset($member['status']) ? $member['status'] : 'active';
        $profile_image = isset($member['profile_image']) ? $member['profile_image'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Member</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #222;
            color: #fff;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        h1 {
            color: #3498db;
            padding: 10px 0;
            margin: 0 0 20px 0;
            border-bottom: 1px solid #333;
        }
        .form-container {
            background: #333;
            padding: 25px;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            border-radius: 4px;
            background: #444;
            color: #fff;
            box-sizing: border-box;
        }
        .current-image {
            margin-bottom: 15px;
        }
        .current-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #555;
        }
        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-secondary {
            background: #7f8c8d;
            color: white;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid #2ecc71;
        }
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-edit"></i> Edit Member</h1>
        
        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger"><?= $errorMsg ?></div>
        <?php endif; ?>
        
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success"><?= $successMsg ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $id ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="membership_type">Membership Type</label>
                    <select id="membership_type" name="membership_type" required>
                        <option value="premium" <?= $membership_type == 'premium' ? 'selected' : '' ?>>Premium</option>
                        <option value="standard" <?= $membership_type == 'standard' ? 'selected' : '' ?>>Standard</option>
                        <option value="basic" <?= $membership_type == 'basic' ? 'selected' : '' ?>>Basic</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Profile Image</label>
                    <?php if (!empty($profile_image)): ?>
                        <div class="current-image">
                            <p>Current Image:</p>
                            <img src="<?= $profile_image ?>" alt="Profile Image">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    <small>Leave empty to keep current image. Upload a new one to replace.</small>
                </div>
                
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    <a href="../back-end/all_members.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>