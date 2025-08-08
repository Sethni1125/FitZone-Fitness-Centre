<?php
$conn = new mysqli("localhost", "root", "", "fitzone1");

$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$times = ["6:00 AM", "8:00 AM", "10:00 AM", "12:00 PM", "5:00 PM", "7:00 PM"];

// Get schedule from database
$scheduleData = [];
$result = $conn->query("SELECT * FROM class_schedule");
while ($row = $result->fetch_assoc()) {
    $scheduleData[$row['day']][$row['time_slot']] = $row['class_name'];
}
?>

<html data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile - FitZone</title>
    <style>
        :root {
            --primary-color: #4a7bf9;
            --text-color: #333;
            --background-color: #e0e5ec;
            --card-background: #e0e5ec;
            --shadow-1: #c8d0e7;
            --shadow-2: #ffffff;
            --neumorphic-light: 5px 5px 10px var(--shadow-1), -5px -5px 10px var(--shadow-2);
            --neumorphic-pressed: inset 5px 5px 10px var(--shadow-1), inset -5px -5px 10px var(--shadow-2);
        }

        [data-theme="dark"] {
            --primary-color: #5d87fc;
            --text-color: #e0e0e0;
            --background-color: #1a1a1a;
            --card-background: #2a2a2a;
            --shadow-1: #151515;
            --shadow-2: #3c3c3c;
            --neumorphic-light: 5px 5px 10px var(--shadow-1), -5px -5px 10px var(--shadow-2);
            --neumorphic-pressed: inset 5px 5px 10px var(--shadow-1), inset -5px -5px 10px var(--shadow-2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .theme-toggle {
            position: absolute;
            right: 20px;
            top: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--card-background);
            box-shadow: var(--neumorphic-light);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .theme-toggle:active {
            box-shadow: var(--neumorphic-pressed);
        }

        .theme-toggle svg {
            width: 24px;
            height: 24px;
            fill: var(--text-color);
        }

        #moon {
            display: block;
        }

        #sun {
            display: none;
        }

        [data-theme="dark"] #moon {
            display: none;
        }

        [data-theme="dark"] #sun {
            display: block;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--shadow-1);
            margin-bottom: 40px;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .logo-tagline {
            font-size: 0.9rem;
            margin-top: 5px;
            color: var(--text-color);
            opacity: 0.8;
        }

        .back-link {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            opacity: 0.8;
        }

        .back-link svg {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            fill: var(--primary-color);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--card-background);
            box-shadow: var(--neumorphic-light);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 30px;
            position: relative;
        }

        .profile-avatar svg {
            width: 70px;
            height: 70px;
            fill: var(--primary-color);
        }

        .change-avatar {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            box-shadow: var(--neumorphic-light);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .change-avatar:hover {
            transform: scale(1.1);
        }

        .change-avatar svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        .profile-info h1 {
            font-size: 2rem;
            color: var(--text-color);
            margin-bottom: 5px;
        }

        .profile-role {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .profile-status {
            display: inline-block;
            padding: 5px 15px;
            background: var(--card-background);
            border-radius: 20px;
            box-shadow: var(--neumorphic-light);
            color: #10b981;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .section {
            background: var(--card-background);
            border-radius: 15px;
            box-shadow: var(--neumorphic-light);
            padding: 25px;
            margin-bottom: 30px;
        }

        .section h2, .section h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            border-bottom: 1px solid var(--shadow-1);
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .edit-button {
            background: var(--card-background);
            color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: var(--neumorphic-light);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .edit-button:hover {
            transform: translateY(-2px);
        }

        .edit-button:active {
            box-shadow: var(--neumorphic-pressed);
            transform: translateY(0);
        }

        .edit-button svg {
            width: 16px;
            height: 16px;
            margin-right: 5px;
            fill: var(--primary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 10px;
            background: var(--card-background);
            box-shadow: var(--neumorphic-pressed);
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: 2px solid var(--primary-color);
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--text-color);
            opacity: 0.8;
        }

        .info-value {
            font-size: 1.1rem;
            padding-left: 10px;
        }

        /* Schedule Table Styling */
        #schedule {
            background: var(--card-background);
            border-radius: 15px;
            box-shadow: var(--neumorphic-light);
            padding: 25px;
            margin-bottom: 30px;
        }

        .schedule-table {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            border-bottom: 1px solid var(--shadow-1);
            padding-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin: 15px 0;
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: center;
        }

        .table th {
            font-weight: 600;
            color: var(--primary-color);
            background: var(--card-background);
            box-shadow: var(--neumorphic-light);
            border-radius: 8px;
        }

        .table td {
            background: var(--card-background);
            box-shadow: var(--neumorphic-light);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .table tr:hover td {
            transform: translateY(-2px);
            box-shadow: 6px 6px 12px var(--shadow-1), -6px -6px 12px var(--shadow-2);
        }

        /* First column styling */
        .table td:first-child {
            font-weight: 500;
            color: var(--primary-color);
        }

        /* Classes styling */
        .table td:not(:first-child):not(:empty) {
            color: var(--text-color);
        }

        .table td:not(:first-child):empty::before {
            content: '-';
            opacity: 0.5;
        }

        .certification-list {
            list-style: none;
        }

        .certification-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--shadow-1);
        }

        .certification-item:last-child {
            border-bottom: none;
        }

        .certification-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--card-background);
            box-shadow: var(--neumorphic-light);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
        }

        .certification-icon svg {
            width: 24px;
            height: 24px;
            fill: var(--primary-color);
        }

        .certification-info {
            flex: 1;
        }

        .certification-name {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .certification-date {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .performance-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: var(--card-background);
            border-radius: 10px;
            box-shadow: var(--neumorphic-light);
            padding: 15px;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .schedule-availability {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-top: 20px;
        }

        .day-column {
            text-align: center;
        }

        .day-name {
            font-weight: 500;
            margin-bottom: 10px;
        }

        .time-slot {
            margin-bottom: 8px;
            padding: 8px 0;
            border-radius: 8px;
            background: var(--card-background);
            box-shadow: var(--neumorphic-light);
            font-size: 0.9rem;
        }

        .time-slot.available {
            color: #10b981;
        }

        .time-slot.unavailable {
            color: #ef4444;
            text-decoration: line-through;
            opacity: 0.7;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .neumorphic-button {
            background: var(--card-background);
            color: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: var(--neumorphic-light);
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .neumorphic-button:hover {
            transform: translateY(-2px);
        }

        .neumorphic-button:active {
            box-shadow: var(--neumorphic-pressed);
            transform: translateY(0);
        }

        .primary-button {
            background: var(--primary-color);
            color: white;
        }

        footer {
            margin-top: auto;
            text-align: center;
            padding: 20px;
            border-top: 1px solid var(--shadow-1);
            font-size: 0.9rem;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .performance-stats {
                grid-template-columns: 1fr 1fr;
            }
            
            .schedule-availability {
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: repeat(3, auto);
            }
            
            .day-column:nth-child(n+4) {
                margin-top: 20px;
            }
            
            .button-group {
                flex-direction: column;
                gap: 15px;
            }
            
            .neumorphic-button {
                width: 100%;
            }

            .table {
                font-size: 0.85rem;
            }

            .table th, .table td {
                padding: 8px 5px;
            }
        }

        @media (max-width: 480px) {
            .performance-stats {
                grid-template-columns: 1fr;
            }
            
            .schedule-availability {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: repeat(4, auto);
            }

            .table {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="theme-toggle" onclick="toggleTheme()">
        <svg id="moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05A1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
        <svg id="sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12,17c-2.76,0-5-2.24-5-5s2.24-5,5-5s5,2.24,5,5S14.76,17,12,17z M12,9c-1.65,0-3,1.35-3,3s1.35,3,3,3s3-1.35,3-3S13.65,9,12,9z"/>
            <path d="M12,3c-0.55,0-1,0.45-1,1v2c0,0.55,0.45,1,1,1s1-0.45,1-1V4C13,3.45,12.55,3,12,3z"/>
            <path d="M12,21c-0.55,0-1,0.45-1,1v2c0,0.55,0.45,1,1,1s1-0.45,1-1v-2C13,21.45,12.55,21,12,21z"/>
            <path d="M4.93,5.93c-0.39,0.39-0.39,1.02,0,1.41l1.42,1.42c0.39,0.39,1.02,0.39,1.41,0s0.39-1.02,0-1.41L6.34,5.93C5.95,5.54,5.32,5.54,4.93,5.93z"/>
            <path d="M18.36,17.36c-0.39,0.39-0.39,1.02,0,1.41l1.42,1.42c0.39,0.39,1.02,0.39,1.41,0s0.39-1.02,0-1.41l-1.42-1.42C19.38,16.97,18.75,16.97,18.36,17.36z"/>
            <path d="M3,12c0-0.55,0.45-1,1-1h2c0.55,0,1,0.45,1,1s-0.45,1-1,1H4C3.45,13,3,12.55,3,12z"/>
            <path d="M21,12c0-0.55-0.45-1-1-1h-2c-0.55,0-1,0.45-1,1s0.45,1,1,1h2C20.55,13,21,12.55,21,12z"/>
            <path d="M4.93,18.07c0.39-0.39,0.39-1.02,0-1.41l-1.42-1.42c-0.39-0.39-1.02-0.39-1.41,0s-0.39,1.02,0,1.41l1.42,1.42C3.91,18.46,4.54,18.46,4.93,18.07z"/>
            <path d="M19.07,5.93c-0.39-0.39-1.02-0.39-1.41,0l-1.42,1.42c-0.39,0.39-0.39,1.02,0,1.41s1.02,0.39,1.41,0l1.42-1.42C19.46,6.95,19.46,6.32,19.07,5.93z"/>
        </svg>
    </div>

    <div class="container">
        <header class="header">
            <div class="logo-container">
                <div class="logo">FitZone</div>
                <div class="logo-tagline">Transform Your Body, Transform Your Life</div>
            </div>
        </header>

        <div class="profile-header">
            <div class="profile-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,2A10,10,0,0,0,2,12a9.89,9.89,0,0,0,2.26,6.33l0,0a9.89,9.89,0,0,0,15.58,0l0,0A9.89,9.89,0,0,0,22,12,10,10,0,0,0,12,2Zm0,18a8,8,0,0,1-5.55-2.25,6,6,0,0,1,11.1,0A8,8,0,0,1,12,20ZM10,10a2,2,0,1,1,2,2A2,2,0,0,1,10,10Zm8.91,6A8,8,0,0,0,15,12.62a4,4,0,1,0-6,0A8,8,0,0,0,5.09,16,7.92,7.92,0,0,1,4,12a8,8,0,0,1,16,0A7.92,7.92,0,0,1,18.91,16Z"/></svg>
                <div class="change-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13a1,1,0,0,0-1,1v.38L16.52,12.9a2.79,2.79,0,0,0-3.93,0l-.7.7L9.41,11.12a2.85,2.85,0,0,0-3.93,0L4,12.6V7A1,1,0,0,1,5,6h7a1,1,0,0,0,0-2H5A3,3,0,0,0,2,7V19a3,3,0,0,0,3,3H17a3,3,0,0,0,3-3V14A1,1,0,0,0,19,13ZM5,20a1,1,0,0,1-1-1V15.43l2.9-2.9a.79.79,0,0,1,1.09,0l3.17,3.17,0,0L15.46,20Zm13-1a.89.89,0,0,1-.18.53L13.31,15l.7-.7a.77.77,0,0,1,1.1,0L18,17.21ZM22.71,4.29l-3-3a1,1,0,0,0-1.42,0l-3,3a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L18,4.41V10a1,1,0,0,0,2,0V4.41l1.29,1.3a1,1,0,0,0,1.42,0A1,1,0,0,0,22.71,4.29Z"/></svg>
                </div>
            </div>
            <div class="profile-info">
                <h1>Anuja Rajakaruna</h1>
                <div class="profile-role">Fitness Trainer</div>
                <div class="profile-status">Active</div>
            </div>
        </div>

        <div class="main-content">
            <section class="section">
                <h2>Personal Information</h2>
                
                <div id="personal-info-view">
                    <div class="info-group">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">Anuja Rajakaruna</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Email</div>
                        <div class="info-value">anuja.r@fitzone.com</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Phone</div>
                        <div class="info-value">+94 76 123 4567</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">June 15, 1992</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Address</div>
                        <div class="info-value">42 Palm Grove, Colombo 03, Sri Lanka</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Emergency Contact</div>
                        <div class="info-value">Malik Rajakaruna (Brother) - +94 77 987 6543</div>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2>
                    Professional Qualifications
                    <button class="edit-button" onclick="toggleEditMode('qualifications')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,12a1,1,0,0,0-1,1v6a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4h6a1,1,0,0,0,0-2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V13A1,1,0,0,0,21,12ZM6,12.76V17a1,1,0,0,0,1,1h4.24a1,1,0,0,0,.71-.29l6.92-6.93h0L21.71,8a1,1,0,0,0,0-1.42L17.47,2.29a1,1,0,0,0-1.42,0L13.23,5.12h0L6.29,12.05A1,1,0,0,0,6,12.76ZM16.76,4.41l2.83,2.83L18.17,8.66,15.34,5.83ZM8,13.17l5.93-5.93,2.83,2.83L10.83,16H8Z"/></svg>
                        Edit
                    </button>
                </h2>
                
                <div id="qualifications-view">
                    <ul class="certification-list">
                        <li class="certification-item">
                            <div class="certification-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V5A3,3,0,0,0,19,2Zm1,17a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4H19a1,1,0,0,1,1,1ZM17,9H7a1,1,0,0,0,0,2H17a1,1,0,0,0,0-2Zm-4,4H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2Z"/></svg>
                            </div>
                            <div class="certification-info">
                                <div class="certification-name">ACE Certified Personal Trainer</div>
                                <div class="certification-date">Obtained: June 2019 | Valid until: June 2025</div>
                            </div>
                        </li>
                        <li class="certification-item">
                            <div class="certification-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V5A3,3,0,0,0,19,2Zm1,17a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4H19a1,1,0,0,1,1,1ZM17,9H7a1,1,0,0,0,0,2H17a1,1,0,0,0,0-2Zm-4,4H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2Z"/></svg>
                            </div>
                            <div class="certification-info">
                                <div class="certification-name">NASM Corrective Exercise Specialist</div>
                                <div class="certification-date">Obtained: March 2020 | Valid until: March 2024</div>
                            </div>
                        </li>
                        <li class="certification-item">
                            <div class="certification-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V5A3,3,0,0,0,19,2Zm1,17a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4H19a1,1,0,0,1,1,1ZM17,9H7a1,1,0,0,0,0,2H17a1,1,0,0,0,0-2Zm-4,4H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2Z"/></svg>
                            </div>
                            <div class="certification-info">
                                <div class="certification-name">Yoga Alliance RYT 200 Certification</div>
                                <div class="certification-date">Obtained: November 2021 | Valid until: November 2026</div>
                            </div>
                        </li>
                        <li class="certification-item">
                            <div class="certification-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V5A3,3,0,0,0,19,2Zm1,17a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4H19a1,1,0,0,1,1,1ZM17,9H7a1,1,0,0,0,0,2H17a1,1,0,0,0,0-2Zm-4,4H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2Z"/></svg>
                            </div>
                            <div class="certification-info">
                                <div class="certification-name">CPR/AED Certified</div>
                                <div class="certification-date">Obtained: January 2023 | Valid until: January 2025</div>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div id="qualifications-edit" style="display: none;">
                    <form id="qualifications-form">
                        <div class="form-group">
                            <label class="form-label" for="cert1">Certification 1</label>
                            <input type="text" class="form-control" id="cert1" value="ACE Certified Personal Trainer">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cert1-date">Valid until</label>
                            <input type="date" class="form-control" id="cert1-date" value="2025-06-30">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="cert2">Certification 2</label>
                            <input type="text" class="form-control" id="cert2" value="NASM Corrective Exercise Specialist">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cert2-date">Valid until</label>
                            <input type="date" class="form-control" id="cert2-date" value="2024-03-31">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="cert3">Certification 3</label>
                            <input type="text" class="form-control" id="cert3" value="Yoga Alliance RYT 200 Certification">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cert3-date">Valid until</label>
                            <input type="date" class="form-control" id="cert3-date" value="2026-11-30">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="cert4">Certification 4</label>
                            <input type="text" class="form-control" id="cert4" value="CPR/AED Certified">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cert4-date">Valid until</label>
                            <input type="date" class="form-control" id="cert4-date" value="2025-01-31">
                        </div>
                        
                        <div class="button-group">
                            <button type="button" class="neumorphic-button" onclick="toggleEditMode('qualifications')">Cancel</button>
                            <button type="button" class="neumorphic-button primary-button" onclick="saveQualifications()">Save Changes</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="section">
                <h2>Performance Statistics</h2>
                <div class="performance-stats">
                    <div class="stat-card">
                        <div class="stat-value">98%</div>
                        <div class="stat-label">Client Satisfaction</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">47</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">156</div>
                        <div class="stat-label">Classes Conducted</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">92%</div>
                        <div class="stat-label">Attendance Rate</div>
                    </div>
                </div>
            </section>

        <section id="schedule" class="section">
            <h3 class="schedule-table">Weekly Schedule</h3>    
            <div style="overflow-x: auto;">
                <table class="table">
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
                                    $class = $scheduleData[$day][$time] ?? '-';
                                ?>
                                    <td><?= htmlspecialchars($class) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
            
        <div class="button-group">
            <a href="../front-end/l.php" class="neumorphic-button primary-button">Log Out</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 FitZone. All rights reserved.</p>
    </footer>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute("data-theme");
            html.setAttribute("data-theme", currentTheme === "dark" ? "light" : "dark");
        }
        
        function toggleEditMode(section) {
            const viewSection = document.getElementById(`${section}-view`);
            const editSection = document.getElementById(`${section}-edit`);
            
            if (viewSection.style.display === "none") {
                viewSection.style.display = "block";
                editSection.style.display = "none";
            } else {
                viewSection.style.display = "none";
                editSection.style.display = "block";
            }
        }
        
        function saveQualifications() {
            alert("Qualifications updated successfully!");
            toggleEditMode('qualifications');
        }
    </script>
</body>
</html>