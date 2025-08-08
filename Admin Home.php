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

<!DOCTYPE html>
<html data-theme="light">
<head>
    <title>FitZone Admin - Edit Schedule</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/Adminhome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #f5f5f5;
            --text-color: #333;
            --card-bg: #ffffff;
            --header-color: #0c3c60;
            --border-color: #eee;
            --feature-bg: #e3f2fd;
            --hero-bg: #f0f9ff;
            --shadow-color: rgba(0,0,0,0.1);
            --btn-primary: #2e86de;
            --btn-text: #ffffff;
            --table-header: #f5f5f5;
            --footer-bg: #0c3c60;
            --footer-text: #ffffff;
            --sidebar-bg: #1a2a3a;
            --sidebar-active: #2e86de;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            --chart-bg: rgba(46, 134, 222, 0.2);
            --chart-border: #2e86de;
            --table-border: #ccc;
            --input-bg: #f8f9fa;
            --input-border: #ddd;
            --schedule-bg: #f5f5f5;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --text-color: #e0e0e0;
            --card-bg: #242424;
            --header-color: #4da6ff;
            --border-color: #444;
            --feature-bg: #1e3a5f;
            --hero-bg: #1a2a3a;
            --shadow-color: rgba(0,0,0,0.3);
            --btn-primary: #3498db;
            --btn-text: #ffffff;
            --table-header: #1e3a5f;
            --footer-bg: #1a2a3a;
            --footer-text: #ffffff;
            --sidebar-bg: #0c1620;
            --sidebar-active: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            --chart-bg: rgba(52, 152, 219, 0.2);
            --chart-border: #3498db;
            --table-border: #444;
            --input-bg: #333;
            --input-border: #555;
            --schedule-bg: #1a2a3a;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
            display: flex;
            min-height: 100vh;
        }

        .schedule-container {
            padding: 20px;
            background-color: var(--schedule-bg);
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px var(--shadow-color);
        }

        .dashboard-title {
            color: var(--header-color);
            margin-bottom: 15px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            box-shadow: 0 0 10px var(--shadow-color);
            border-radius: 8px;
            overflow: hidden;
            font-size: 0.85rem;
        }

        .schedule-table th, 
        .schedule-table td {
            border: 1px solid var(--table-border);
            padding: 6px 8px; 
            text-align: center;
            vertical-align: middle;
        }

        .schedule-table th {
            background-color: var(--table-header);
            color: var(--header-color);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 8px 6px; 
        }

        .schedule-input {
            width: 100%;
            max-width: 100px; 
            padding: 4px 6px; 
            border: 1px solid var(--input-border);
            border-radius: 4px;
            background-color: var(--input-bg);
            color: var(--text-color);
            transition: all 0.3s ease;
            font-size: 0.8rem; 
        }

        .schedule-input:focus {
            border-color: var(--btn-primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(46, 134, 222, 0.2);
        }

        .schedule-submit {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: var(--btn-primary);
            color: var(--btn-text);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .schedule-submit:hover {
            background-color: var(--sidebar-active);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .text-center {
            text-align: center;
            margin-top: 15px;
        }

        
        
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-dumbbell"></i>
                <span>FitZone Admin</span>
            </div>
        </div>
        
        <div class="admin-info">
            <div class="admin-avatar">
                <img src="../img/Sethni.jpeg" alt="Miss. Sethni Dahamsa" class="instructor-image">
            </div>
            <div class="admin-details">
                <div class="admin-name">Sethni Dahamda</div>
                <div class="admin-role">Administrator 1</div>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="#Dashboard" class="active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../front-end/All members.html" class="menu-toggle">
                    <i class="fas fa-users"></i>
                    <span>Members</span>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="../back-end/all_members.php">
                            <i class="fas fa-list"></i>
                            <span>All Members</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="../front-end/Class.php" class="menu-toggle">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Classes</span>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="../back-end/all_class.php">
                            <i class="fas fa-list"></i>
                            <span>All Classes</span>
                        </a>
                    </li>
                    <li>
                        <a href="../front-end/Class.php#schedule" onclick="goToClassSchedule(event)">
                            <i class="fas fa-clock"></i>
                            <span>Schedule</span>
                        </a>
                    </li>
                    <li>
                        <a href="../back-end/add_class.php">
                            <i class="fas fa-list"></i>
                            <span>Add Classes</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href=" " class="menu-toggle">
                    <i class="fas fa-user-tie"></i>
                    <span>Trainers</span>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="../front-end/Trainers.php">
                            <i class="fas fa-list"></i>
                            <span>All Trainers</span>
                        </a>
                    </li>
              </ul>
            </li>
            <li>
                <a href="../front-end/l.php" id="logoutBtn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>


    <div class="main-content" id="mainContent">
        <div class="top-bar">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="top-bar-right">
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="theme-toggle" id="themeToggle"></div>
            </div>
        </div>
        
        <h1 class="dashboard-title">Admin Dashboard</h1>
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon members-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number">1,845</h2>
                    <p class="stat-label">Active Members</p>
                    <div class="stat-change stat-increase">
                        <i class="fas fa-arrow-up"></i> 12% from last month
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon revenue-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number">$24,580</h2>
                    <p class="stat-label">Monthly Revenue</p>
                    <div class="stat-change stat-increase">
                        <i class="fas fa-arrow-up"></i> 8% from last month
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon classes-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number">68</h2>
                    <p class="stat-label">Weekly Classes</p>
                    <div class="stat-change stat-increase">
                        <i class="fas fa-arrow-up"></i> 5% from last month
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon trainers-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-info">
                    <h2 class="stat-number">24</h2>
                    <p class="stat-label">Active Trainers</p>
                    <div class="stat-change stat-decrease">
                        <i class="fas fa-arrow-down"></i> 2% from last month
                    </div>
                </div>
            </div>
        </div>
        
        <div class="charts-container">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Membership Growth</h3>
                    <span class="chart-period">Last 6 Months</span>
                </div>
                <div class="chart-container">
                    <canvas id="membershipChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Revenue Distribution</h3>
                    <span class="chart-period">Current Month</span>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="quick-actions">
            <div class="quick-action" id="addMemberAction">
                <div class="quick-action-icon add-member-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <p class="quick-action-text">Add Member</p>
            </div>
        </div>
        
        <div class="task-container">
            <div class="task-header">
                <h3 class="task-title">Today's Tasks</h3>
            </div>
            <div class="add-task">
                <input type="text" class="task-input" id="taskInput" placeholder="Add a new task...">
                <button class="task-submit" id="taskSubmit"><i class="fas fa-plus"></i></button>
            </div>
            <ul class="task-list" id="taskList">
                <li class="task-item completed">
                    <input type="checkbox" class="task-checkbox" checked>
                    <span class="task-text">Review new membership applications</span>
                    <button class="task-delete"><i class="fas fa-trash"></i></button>
                </li>
                <li class="task-item">
                    <input type="checkbox" class="task-checkbox">
                    <span class="task-text">Call equipment maintenance service</span>
                    <button class="task-delete"><i class="fas fa-trash"></i></button>
                </li>
                <li class="task-item">
                    <input type="checkbox" class="task-checkbox">
                    <span class="task-text">Prepare monthly financial report</span>
                    <button class="task-delete"><i class="fas fa-trash"></i></button>
                </li>
                <li class="task-item">
                    <input type="checkbox" class="task-checkbox">
                    <span class="task-text">Schedule staff meeting for next week</span>
                    <button class="task-delete"><i class="fas fa-trash"></i></button>
                </li>
            </ul>
        </div>

        <div class="schedule-container">
            <h2 class="dashboard-title">Edit Weekly Class Schedule</h2>
            
            <form method="POST">
                <table class="schedule-table">
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
                                    <td>
                                        <input type="text" 
                                               class="schedule-input"
                                               name="<?= $inputName ?>" 
                                               value="<?= htmlspecialchars($value) ?>"
                                               placeholder="Class name">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-center">
                    <button type="submit" class="schedule-submit">
                        <i class="fas fa-save"></i> Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <section id="addMembersAction">
        <div class="modal" id="addMemberModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Member</h3>
                <button class="modal-close" id="closeMemberModal">&times;</button>
            </div>
            <form id="addMemberForm">
                <div class="form-group">
                    <label for="memberName" class="form-label">Full Name</label>
                    <input type="text" id="memberName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="memberEmail" class="form-label">Email Address</label>
                    <input type="email" id="memberEmail" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="memberPhone" class="form-label">Phone Number</label>
                    <input type="tel" id="memberPhone" class="form-input">
                </div>
                <div class="form-group">
                    <label for="membershipType" class="form-label">Membership Type</label>
                    <select id="membershipType" class="form-select" required>
                        <option value="">Select Membership Type</option>
                        <option value="basic">Basic (Monthly)</option>
                        <option value="standard">Standard (Quarterly)</option>
                        <option value="premium">Premium (Annual)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Profile Image</label>
                    <input type="file" id="memberImage" name="memberImage" class="form-file-input" accept="image/*" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelMemberBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>

 

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    
    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebarBtn = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('overlay');
        const themeToggle = document.getElementById('themeToggle');
        const menuToggles = document.querySelectorAll('.menu-toggle');
        const addMemberModal = document.getElementById('addMemberModal');
        const addMemberAction = document.getElementById('addMemberAction');
        const closeMemberModal = document.getElementById('closeMemberModal');
        const cancelMemberBtn = document.getElementById('cancelMemberBtn');
        const taskInput = document.getElementById('taskInput');
        const taskSubmit = document.getElementById('taskSubmit');
        const taskList = document.getElementById('taskList');

        function goToClassSchedule() {
            window.location.href = "../front-end/Class.html#schedule";
        }

        toggleSidebarBtn.addEventListener('click', () => {
            const windowWidth = window.innerWidth;
            if (windowWidth <= 576) {
                sidebar.classList.toggle('mobile-visible');
                overlay.classList.toggle('active');
            } else if (windowWidth <= 768) {
                sidebar.classList.toggle('expanded');
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-visible');
            overlay.classList.remove('active');
        });

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }

        menuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const submenu = toggle.nextElementSibling;
                document.querySelectorAll('.submenu.open').forEach(menu => {
                    if (menu !== submenu) {
                        menu.classList.remove('open');
                        menu.previousElementSibling.classList.remove('active');
                    }
                });
                submenu.classList.toggle('open');
                toggle.classList.toggle('active');
            });
        });

        addMemberAction.addEventListener('click', () => {
            addMemberModal.style.display = 'flex';
        });

        closeMemberModal.addEventListener('click', () => {
            addMemberModal.style.display = 'none';
        });

        cancelMemberBtn.addEventListener('click', () => {
            addMemberModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === addMemberModal) {
                addMemberModal.style.display = 'none';
            }
        });

        taskSubmit.addEventListener('click', () => {
            const taskText = taskInput.value.trim();
            if (taskText !== '') {
                addTask(taskText);
                taskInput.value = '';
            }
        });

        taskInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const taskText = taskInput.value.trim();
                if (taskText !== '') {
                    addTask(taskText);
                    taskInput.value = '';
                }
            }
        });

        function addTask(text) {
            const li = document.createElement('li');
            li.className = 'task-item';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'task-checkbox';
            checkbox.addEventListener('change', function() {
                li.classList.toggle('completed', this.checked);
            });

            const span = document.createElement('span');
            span.className = 'task-text';
            span.textContent = text;

            const button = document.createElement('button');
            button.className = 'task-delete';
            button.innerHTML = '<i class="fas fa-trash"></i>';
            button.addEventListener('click', () => {
                li.remove();
            });

            li.appendChild(checkbox);
            li.appendChild(span);
            li.appendChild(button);
            taskList.appendChild(li);
        }

        taskList.addEventListener('click', (e) => {
            if (e.target.classList.contains('task-delete') || e.target.parentElement.classList.contains('task-delete')) {
                const li = e.target.closest('.task-item');
                if (li) {
                    li.remove();
                }
            }
        });

        function initCharts() {
            const membershipCtx = document.getElementById('membershipChart').getContext('2d');
            const membershipChart = new Chart(membershipCtx, {
                type: 'line',
                data: {
                    labels: ['November', 'December', 'January', 'February', 'March', 'April'],
                    datasets: [{
                        label: 'New Members',
                        data: [65, 78, 52, 83, 95, 112],
                        borderColor: '#2e86de',
                        backgroundColor: 'rgba(46, 134, 222, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Memberships', 'Classes', 'Personal Training', 'Other Services'],
                    datasets: [{
                        data: [65, 15, 12, 8],
                        backgroundColor: [
                            '#3498db',
                            '#2ecc71',
                            '#9b59b6',
                            '#e67e22'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const addMemberForm = document.getElementById('addMemberForm');
            if (addMemberForm) {
                addMemberForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData();
                    formData.append('memberName', document.getElementById('memberName').value);
                    formData.append('memberEmail', document.getElementById('memberEmail').value);
                    formData.append('memberPhone', document.getElementById('memberPhone').value);
                    formData.append('membershipType', document.getElementById('membershipType').value);
                    const imageFile = document.getElementById('memberImage').files[0];
                    if (imageFile) {
                        formData.append('memberImage', imageFile);
                    } else {
                        alert("Please select a profile image.");
                        return;
                    }

                    fetch('add_members.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                        return res.text();
                    })
                    .then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (err) {
                            throw new Error("JSON parse error: " + err.message + " | Response text: " + text);
                        }
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            addMemberForm.reset();
                            addMemberModal.style.display = 'none';
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(err => {
                        alert("An error occurred while adding the member. Check console for details.");
                    });
                });
            }
        });

        document.addEventListener('DOMContentLoaded', initCharts);
        document.addEventListener('DOMContentLoaded', function() {
            const htmlElement = document.documentElement;
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const savedTheme = localStorage.getItem('theme') || 
                            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            applyTheme(savedTheme);

            themeToggle.addEventListener('click', function() {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });

            function applyTheme(theme) {
                htmlElement.setAttribute('data-theme', theme);
                if (theme === 'dark') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
            }
        });
    </script>
</body>
</html>
