<?php 
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "fitzone1"; 
 
$conn = new mysqli($host, $username, $password, $dbname); 
 
if ($conn->connect_error) { 
    die("❌ Connection failed: " . $conn->connect_error); 
} 
 
$result = $conn->query("SELECT * FROM class"); 
?> 
 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>All Classes | FitZone</title> 
    <style> 
        body { 
            font-family: Arial, sans-serif; 
            background: #1e1e1e; /* Dark background as shown in your image */
            padding: 20px; 
            margin: 0;
            color: #fff;
        }
        
        .class-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .class-card {
            display: flex;
            background: #2a2a2a; /* Dark card background */
            border-radius: 6px;
            overflow: hidden;
        }
        
        .class-image {
            width: 280px;
            min-height: 230px;
            object-fit: cover;
        }
        
        .class-details {
            padding: 20px;
            flex: 1;
        }
        
        .class-title {
            font-size: 24px;
            color: #3498db; /* Blue color for titles */
            margin: 0 0 10px 0;
        }
        
        .class-description {
            margin: 10px 0 20px;
            color: #fff;
        }
        
        .class-meta {
            margin-bottom: 15px;
            color: #ccc;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            justify-content: flex-end;
        }
        
        .view-button, .book-button {
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        
        .view-button {
            background: transparent;
            border: 1px solid #3498db;
            color: #3498db;
        }
        
        .book-button {
            background: #3498db;
            color: white;
        }
    </style> 
</head> 
<body> 
    <div class="class-container">
        <?php if ($result && $result->num_rows > 0): ?> 
            <?php while ($row = $result->fetch_assoc()): ?> 
                <div class="class-card">
                    <?php if (!empty($row['image'])): ?> 
                        <img class="class-image" src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['class_type']) ?>"> 
                    <?php else: ?> 
                        <img class="class-image" src="../uploads/default-class.jpg" alt="Default Class Image"> 
                    <?php endif; ?>
                    
                    <div class="class-details">
                        <h3 class="class-title"><?= htmlspecialchars($row['class_type']) ?></h3>
                        
                        <p class="class-description">
                            <?= htmlspecialchars($row['description']) ?: "High-intensity training to improve fitness and health." ?>
                        </p>
                        
                        <div class="class-meta">
                            <strong>Instructor:</strong> <?= htmlspecialchars($row['trainer']) ?> | 
                            <strong>Duration:</strong> <?= htmlspecialchars($row['duration']) ?> minutes
                        </div>
                        
                        <div class="button-group">
                            <a class="view-button" href="<?= htmlspecialchars($row['view_details_link'] ?? 'class_details.php?id='.$row['id']) ?>">View Details</a>
                            <a class="book-button" href="<?= htmlspecialchars($row['book_now_link'] ?? 'book_class.php?id='.$row['id']) ?>">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?> 
        <?php else: ?> 
            <p>No classes found.</p>
        <?php endif; ?> 
    </div>
</body> 
</html> 
 
<?php 
$conn->close(); 
?>