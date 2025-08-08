<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "fitzone1";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$editClass = null;

// Handle form submission to update a class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'] ?? null;
    $class_name = $_POST['class_name'] ?? '';
    $instructor = $_POST['instructor'] ?? '';
    $day = $_POST['day'] ?? '';
    $time = $_POST['time'] ?? '';

    if ($id && $class_name && $instructor && $day && $time) {
        $stmt = $conn->prepare("UPDATE class_schedule SET class_name=?, instructor=?, day=?, time=? WHERE id=?");
        $stmt->bind_param("ssssi", $class_name, $instructor, $day, $time, $id);
        $stmt->execute();
        $stmt->close();
        echo "<script>window.location.href=window.location.href;</script>"; // Refresh
        exit();
    } else {
        echo "<p style='color:red;'>Please fill in all fields before updating.</p>";
    }
}

// Fetch class data
$result = $conn->query("SELECT * FROM class_schedule");

// Handle "Edit" click
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM class_schedule WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editClass = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f7f7f7;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 25px;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        form {
            margin-bottom: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        label {
            display: inline-block;
            width: 120px;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 250px;
            padding: 6px;
            margin-bottom: 12px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        a.edit-btn {
            background: #007bff;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<h2>Edit Class Schedule</h2>

<?php if ($editClass): ?>
<form method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($editClass['id']) ?>">
    <label>Class Name:</label>
    <input type="text" name="class_name" value="<?= htmlspecialchars($editClass['class_name']) ?>" required><br>
    <label>Instructor:</label>
    <input type="text" name="instructor" value="<?= htmlspecialchars($editClass['instructor']) ?>" required><br>
    <label>Day:</label>
    <input type="text" name="day" value="<?= htmlspecialchars($editClass['day']) ?>" required><br>
    <label>Time:</label>
    <input type="text" name="time" value="<?= htmlspecialchars($editClass['time']) ?>" required><br>
    <input type="submit" name="update" value="Update">
</form>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Class Name</th>
        <th>Instructor</th>
        <th>Day</th>
        <th>Time</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['class_name']) ?></td>
        <td><?= htmlspecialchars($row['instructor']) ?></td>
        <td><?= htmlspecialchars($row['day']) ?></td>
        <td><?= htmlspecialchars($row['time']) ?></td>
        <td><a class="edit-btn" href="?edit=<?= $row['id'] ?>">Edit</a></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
