<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone1";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleteSql = "DELETE FROM members WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=success");
        exit();
    } else {
        $deleteError = "Error deleting member: " . $conn->error;
    }
}

$sql = "SELECT id, full_name, email, phone, membership_type, created_at, profile_image, status FROM members ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Members Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #222;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        h1 {
            color: #3498db;
            padding: 10px 20px;
            margin: 0;
            border-bottom: 1px solid #333;
        }
        h2 {
            margin: 15px 0;
            padding: 10px 20px;
        }
        .filters {
            display: flex;
            gap: 15px;
            padding: 10px 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
        }
        .search-box {
            background: #333;
            border: none;
            padding: 8px 12px;
            color: #fff;
            border-radius: 4px;
            width: 200px;
        }
        .filter-select {
            background: #333;
            border: none;
            padding: 8px 12px;
            color: #fff;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 12px 20px;
            border-bottom: 1px solid #333;
        }
        td {
            padding: 12px 20px;
            border-bottom: 1px solid #333;
        }
        .member-info {
            display: flex;
            align-items: center;
        }
        .member-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            background-color: #555;
        }
        .member-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .member-email {
            font-size: 0.85em;
            color: #aaa;
        }
        .premium {
            color: #e74c3c;
        }
        .standard {
            color: #3498db;
        }
        .basic {
            color: #2ecc71;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            text-align: center;
        }
        .active {
            background-color: #2ecc71;
            color: #fff;
        }
        .inactive {
            background-color: #e74c3c;
            color: #fff;
        }
        .pending {
            background-color: #e67e22;
            color: #fff;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
    
        .delete-btn {
            color: #e74c3c;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn i {
            margin-right: 5px;
        }
        .alert {
            padding: 12px 20px;
            margin: 15px 20px;
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
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .modal-content {
            background-color: #333;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #555;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .modal-header h3 {
            margin: 0;
            color: #3498db;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #fff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            background: #444;
            border: 1px solid #555;
            border-radius: 4px;
            color: #fff;
            box-sizing: border-box;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #ccc;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #3498db;
            color: #fff;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }
        .confirm-dialog {
            text-align: center;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .profile-card {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            padding: 10px;
            background-color: #2e2e2e;
            border-radius: 10px;
            color: #fff;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .profile-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .profile-details p {
            margin: 0;
            font-size: 13px;
            color: #bbb;
        }      
    </style>
</head>
<body>
    <div class="container">
        <h1>Members Management</h1>
        <h2>All Members</h2>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'success'): ?>
            <div class="alert alert-success">
                Member has been successfully deleted.
            </div>
        <?php endif; ?>

        <?php if (isset($deleteError)): ?>
            <div class="alert alert-danger">
                <?= $deleteError ?>
            </div>
        <?php endif; ?>

        <div class="filters">
            <input type="text" id="searchMembers" class="search-box" placeholder="Search members...">
            <select id="statusFilter" class="filter-select">
                <option value="all">Status: All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </select>
            <select id="membershipFilter" class="filter-select">
                <option value="all">Membership: All</option>
                <option value="premium">Premium</option>
                <option value="standard">Standard</option>
                <option value="basic">Basic</option>
            </select>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Contact</th>
                        <th>Created At</th>
                        <th>Membership</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        $membershipClass = strtolower($row["membership_type"]);
                        $status = isset($row["status"]) ? strtolower($row["status"]) : "active";
                        $statusClass = $status;
                        $createdDate = date("d M Y", strtotime($row["created_at"]));
                        $imagePath = "uploads/" . $row["profile_image"];
                        $displayImage = (!empty($row["profile_image"]) && file_exists($imagePath)) ? $imagePath : "assets/default-profile.jpg";
                    ?>
                    <tr data-status="<?= $status ?>" data-membership="<?= $membershipClass ?>">
                        <td>
                            <div class="member-info">
                                <img src="<?= htmlspecialchars($displayImage) ?>" alt="<?= htmlspecialchars($row["full_name"]) ?>" class="member-image">
                                <div>
                                    <div class="member-name"><?= htmlspecialchars($row["full_name"]) ?></div>
                                    <div class="member-email"><?= htmlspecialchars($row["email"]) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($row["phone"]) ?></td>
                        <td><?= $createdDate ?></td>
                        <td class="<?= $membershipClass ?>"><?= ucfirst($row["membership_type"]) ?></td>
                        <td><span class="status-badge <?= $statusClass ?>"><?= ucfirst($status) ?></span></td>
                        <td class="actions">
                            <button class="delete-btn" onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-members">No members found.</div>
        <?php endif; ?>

        <a href="../front-end/Staff Home.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Deletion</h3>
                <span class="close">&times;</span>
            </div>
            <div class="confirm-dialog">
                <p>Are you sure you want to delete <span id="memberName"></span>?</p>
                <p>This action cannot be undone.</p>
                <div class="btn-group">
                    <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    <button class="btn btn-primary" id="cancelDeleteBtn">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchMembers').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const memberName = row.querySelector('.member-name').textContent.toLowerCase();
                const memberEmail = row.querySelector('.member-email').textContent.toLowerCase();

                row.style.display = (memberName.includes(searchValue) || memberEmail.includes(searchValue)) ? '' : 'none';
            });
        });

    
        document.getElementById('statusFilter').addEventListener('change', filterTable);
        document.getElementById('membershipFilter').addEventListener('change', filterTable);

        function filterTable() {
            const status = document.getElementById('statusFilter').value;
            const membership = document.getElementById('membershipFilter').value;
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const rowMembership = row.getAttribute('data-membership');

                const matchStatus = (status === 'all' || rowStatus === status);
                const matchMembership = (membership === 'all' || rowMembership === membership);

                row.style.display = (matchStatus && matchMembership) ? '' : 'none';
            });
        }

        let deleteModal = document.getElementById('deleteModal');
        let closeBtn = document.querySelector('.close');
        let cancelBtn = document.getElementById('cancelDeleteBtn');
        let confirmBtn = document.getElementById('confirmDeleteBtn');
        let memberId = null;

        function confirmDelete(id, name) {
            memberId = id;
            document.getElementById('memberName').textContent = name;
            deleteModal.style.display = 'block';
        }

        closeBtn.onclick = () => deleteModal.style.display = 'none';
        cancelBtn.onclick = () => deleteModal.style.display = 'none';
        confirmBtn.onclick = () => {
            if (memberId) window.location.href = '<?= $_SERVER['PHP_SELF'] ?>?delete=' + memberId;
        };
        window.onclick = function(event) {
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }

        
    </script>
</body>
</html>

<?php
$conn->close();
?>
