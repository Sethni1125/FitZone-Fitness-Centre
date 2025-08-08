<?php
$conn = new mysqli("localhost", "root", "", "fitzone1"); 

$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$times = ["6:00 AM", "8:00 AM", "10:00 AM", "12:00 PM", "5:00 PM", "7:00 PM"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($times as $time) {
        foreach ($days as $day) {
            $inputName = $day . "_" . str_replace(":", "", str_replace(" ", "", $time));
            $class = $_POST[$inputName];

            $check = $conn->query("SELECT * FROM class_schedule WHERE time_slot = '$time' AND day = '$day'");
            if ($check->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE class_schedule SET class_name = ? WHERE time_slot = ? AND day = ?");
            } else {
                $stmt = $conn->prepare("INSERT INTO class_schedule (class_name, time_slot, day) VALUES (?, ?, ?)");
            }
            $stmt->bind_param("sss", $class, $time, $day);
            $stmt->execute();
        }
    }
    echo "<script>alert('Schedule updated successfully!');</script>";
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Weekly Class Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f5f5f5;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        input[type="text"] {
            width: 100%;
            padding: 5px;
            border: none;
            background-color: #f0f0f0;
        }
        button {
            margin-top: 20px;
            padding: 10px 30px;
            background-color: #0077cc;
            color: white;
            border: none;
            cursor: pointer;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        button:hover {
            background-color: #005fa3;
        }
    </style>
</head>
<body>

<h2>Edit Weekly Class Schedule</h2>

<form method="POST">
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <?php foreach ($days as $day): ?>
                    <th><?= $day ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($times as $time): ?>
                <tr>
                    <td><?= $time ?></td>
                    <?php foreach ($days as $day): 
                        $inputName = $day . "_" . str_replace(":", "", str_replace(" ", "", $time));
                        $res = $conn->query("SELECT class_name FROM class_schedule WHERE time_slot = '$time' AND day = '$day'");
                        $row = $res->fetch_assoc();
                        $value = $row ? $row['class_name'] : '';
                    ?>
                        <td><input type="text" name="<?= $inputName ?>" value="<?= htmlspecialchars($value) ?>"></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit">Save Schedule</button>
</form>

</body>
</html>
