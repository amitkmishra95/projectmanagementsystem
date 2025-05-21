<?php
session_start();
include("db.php");

if (!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php");
    exit;
}

$faculty_id = $_SESSION['faculty_id'];
$faculty_name = $_SESSION['faculty_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #002B5B;
            color: #fff;
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .sidebar .profile {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar .profile img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid #fff;
            margin-bottom: 10px;
        }

        .sidebar .profile p {
            margin: 0;
            font-weight: 500;
        }

        .sidebar a {
            display: block;
            padding: 12px 10px;
            color: #fff;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: #00509D;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .main {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }

        .main h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .dashboard-info {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        footer {
            margin-top: 40px;
            text-align: center;
            color: #777;
        }

        @media(max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #e0ecff 0%, #ffe0e7 100%);
            min-height: 100vh;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #2563eb 0%, #f857a6 100%);
            color: #fff;
            padding: 36px 20px 30px 20px;
            position: fixed;
            height: 100vh;
            box-shadow: 4px 0 24px rgba(59,130,246,0.09);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: sidebarFadeIn 1s;
        }
        @keyframes sidebarFadeIn {
            from { opacity: 0; transform: translateX(-40px);}
            to { opacity: 1; transform: translateX(0);}
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 32px;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 1.6rem;
            background: linear-gradient(90deg, #fff, #ffe0e7 80%);
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
        }

        .sidebar .profile {
            text-align: center;
            margin-bottom: 36px;
        }

        .sidebar .profile img {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            border: 3px solid #fff;
            margin-bottom: 12px;
            box-shadow: 0 4px 16px #f857a644;
            object-fit: cover;
            background: #fff;
            animation: floatY 3.2s ease-in-out infinite;
        }
        @keyframes floatY {
            0%,100% { transform: translateY(0);}
            50% { transform: translateY(-10px);}
        }

        .sidebar .profile p {
            margin: 0;
            font-weight: 600;
            font-size: 1.08rem;
            color: #fff;
            letter-spacing: 0.5px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            color: #fff;
            text-decoration: none;
            margin-bottom: 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1.05rem;
            transition: background 0.3s, box-shadow 0.2s, transform 0.18s;
            box-shadow: 0 2px 8px #f857a622;
        }

        .sidebar a:hover, .sidebar a:focus {
            background: linear-gradient(90deg, #f857a6 60%, #2563eb 100%);
            color: #fff;
            transform: translateX(4px) scale(1.04);
            box-shadow: 0 5px 18px #2563eb33;
            text-decoration: none;
        }

        .sidebar a i {
            font-size: 1.2rem;
        }

        .main {
            margin-left: 260px;
            padding: 48px 40px 30px 40px;
            width: calc(100% - 260px);
            min-height: 100vh;
            background: transparent;
            position: relative;
            z-index: 1;
        }

        .main h1 {
            margin-bottom: 30px;
            color: #2563eb;
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: 1px;
            animation: fadeInDown 1.1s;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px);}
            to { opacity: 1; transform: translateY(0);}
        }

        .dashboard-info {
            background: rgba(255,255,255,0.97);
            padding: 36px 28px 28px 28px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(59,130,246,0.08), 0 1.5px 8px #f857a622;
            text-align: center;
            animation: floatIn 1.2s cubic-bezier(.4,2,.6,1);
            backdrop-filter: blur(6px);
            margin-bottom: 40px;
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(60px);}
            to { opacity: 1; transform: translateY(0);}
        }

        .dashboard-info p {
            font-size: 1.12rem;
            color: #444;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .dashboard-info img {
            width: 70%;
            max-width: 400px;
            border-radius: 18px;
            box-shadow: 0 4px 18px #2563eb22;
            animation: floatY 3.2s ease-in-out infinite;
            margin-top: 10px;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
            font-size: 1rem;
            opacity: 0.9;
        }

        @media(max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                flex-direction: row;
                justify-content: space-between;
                padding: 18px 5vw;
            }
            .main {
                margin-left: 0;
                width: 100%;
                padding: 30px 6vw 20px 6vw;
            }
            .dashboard-info img { width: 90%; }
        }
        @media(max-width: 600px) {
            .sidebar {
                flex-direction: column;
                align-items: center;
                padding: 12px 2vw;
            }
            .main {
                padding: 18px 2vw 10px 2vw;
            }
            .dashboard-info {
                padding: 14px 4px 10px 4px;
            }
            .dashboard-info img { width: 99%; }
            .main h1 { font-size: 1.2rem; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>
        <i class="fas fa-chalkboard-teacher"></i>
        Faculty Panel
    </h2>
    <div class="profile">
        <img src="f.png" alt="Profile Picture">
        <p><?= htmlspecialchars($faculty_name) ?></p>
    </div>
    <a href="faculty_profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="view_assigned_groups.php"><i class="fas fa-users"></i> Assigned Groups</a>
    <a href="review_uploads.php"><i class="fas fa-folder-open"></i> Review Uploads</a>
    <a href="faculty_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <h1>
        <i class="fas fa-home"></i>
        Welcome to Your Dashboard, <?= htmlspecialchars($faculty_name) ?>
    </h1>
    <div class="dashboard-info">
        <p>
            <i class="fas fa-info-circle" style="color:#f857a6;"></i>
            You can manage your assigned groups, view uploaded files, provide feedback, and track project activities. Use the links on the left to navigate your dashboard.
        </p>
        <img src="fd.png" alt="Dashboard Illustration">
    </div>
    <footer>
        &copy; <?= date("Y") ?> Faculty Portal - All rights reserved.
    </footer>
</div>

</body>
</html>
