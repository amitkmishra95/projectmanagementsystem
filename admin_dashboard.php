<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}
$adminname = $_SESSION['admin_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }
        .admin-hero {
            position: relative;
            background: url('https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            color: #232526;
            border-radius: 0 0 36px 36px;
            text-align: center;
            padding: 70px 0 40px 0;
            margin-bottom: 40px;
            overflow: hidden;
        }
        .admin-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(163,191,250,0.90);
            z-index: 1;
        }
        .admin-hero-content {
            position: relative;
            z-index: 2;
        }
        .admin-hero i {
            font-size: 3.2rem;
            color: #4f8cff;
            margin-bottom: 16px;
        }
        .admin-hero h2 {
            font-size: 2.4rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-shadow: 1px 1px 3px rgba(255,255,255,0.7);
        }
        .admin-quote {
            font-size: 1.15rem;
            color: #4f8cff;
            margin-top: 18px;
            font-style: italic;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.6);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 10px 40px 10px;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.10);
            padding: 0 0 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.3s cubic-bezier(.4,2,.6,1), box-shadow 0.3s, filter 0.3s;
            cursor: pointer;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.7s;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .dashboard-card:hover {
            transform: translateY(-10px) scale(1.04) rotate(-1deg);
            box-shadow: 0 18px 60px rgba(0,0,0,0.18);
            filter: brightness(1.10) saturate(1.2);
        }
        .dashboard-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 22px 22px 0 0;
            margin-bottom: 0;
        }
        .icon-box {
            font-size: 2.2rem;
            margin-top: -32px;
            margin-bottom: 10px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            background: #eaf1fb;
            color: #4f8cff;
            border: 3px solid #fff;
            position: relative;
            z-index: 2;
        }
        .dashboard-card h5 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 7px;
            color: #232526;
            margin-top: 8px;
        }
        .dashboard-subtitle {
            font-size: 0.99rem;
            color: #6c757d;
            margin-bottom: 10px;
            font-weight: 400;
            min-height: 36px;
        }
        .dashboard-link {
            margin-top: 10px;
            display: inline-block;
            padding: 10px 26px;
            border-radius: 999px;
            background: linear-gradient(90deg, #4f8cff 60%, #8c52ff 100%);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s, box-shadow 0.2s, transform 0.18s;
            box-shadow: 0 4px 16px #8c52ff22;
        }
        .dashboard-link:hover, .dashboard-link:focus {
            background: linear-gradient(90deg, #8c52ff 60%, #4f8cff 100%);
            color: #fff;
            text-decoration: none;
            transform: translateY(-2px) scale(1.03);
        }
        /* Card-specific colors */
        .dashboard-card:nth-child(1) .icon-box { background: #eaf1fb; color: #4f8cff;}
        .dashboard-card:nth-child(2) .icon-box { background: #eafbf2; color: #17a673;}
        .dashboard-card:nth-child(3) .icon-box { background: #fff4e5; color: #ff9800;}
        .dashboard-card:nth-child(4) .icon-box { background: #fce4ec; color: #e91e63;}
        .dashboard-card:nth-child(5) .icon-box { background: #e8f5e9; color: #388e3c;}
        .dashboard-card:nth-child(6) .icon-box { background: #fffde7; color: #fbc02d;}
        .dashboard-card:nth-child(7) .icon-box { background: #ede7f6; color: #6c63ff;}
        @media (max-width: 600px) {
            .admin-hero { padding: 28px 0 18px 0; border-radius: 0 0 18px 18px;}
            .dashboard-grid { gap: 18px; }
            .dashboard-card { padding: 0 0 18px 0; }
            .dashboard-img { height: 80px; }
            .icon-box { font-size: 1.5rem; width: 40px; height: 40px; margin-top: -20px;}
        }
    </style>
</head>
<body>
    <div class="admin-hero">
        <div class="admin-hero-content">
            <i class="fas fa-user-shield"></i>
            <h2>👋 Welcome, <strong><?php echo htmlspecialchars($adminname); ?></strong>!</h2>
            <div class="admin-quote">
                <i class="fas fa-quote-left"></i>
                Leadership is not a position or a title, it is action and example.
                <i class="fas fa-quote-right"></i>
            </div>
        </div>
    </div>
    <div class="dashboard-grid">

        <!-- Admin Profile -->
        <div class="dashboard-card">
            <img class="dashboard-img" src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=crop&w=400&q=80" alt="Admin Profile">
            <div class="icon-box"><i class="fas fa-user-cog"></i></div>
            <h5>Admin Profile</h5>
            <div class="dashboard-subtitle">Manage your profile, password and preferences</div>
            <a href="admin_profile.php" class="dashboard-link"><i class="fas fa-arrow-right"></i> Go</a>
        </div>

        <!-- View Created Groups -->
        <div class="dashboard-card">
            <img class="dashboard-img" src="https://images.unsplash.com/photo-1503676382389-4809596d5290?auto=format&fit=crop&w=400&q=80" alt="Groups">
            <div class="icon-box"><i class="fas fa-users"></i></div>
            <h5>View Created Groups</h5>
            <div class="dashboard-subtitle">See all groups created within the system</div>
            <a href="view_created_groups.php" class="dashboard-link"><i class="fas fa-arrow-right"></i> Go</a>
        </div>

        <!-- Assign Final Group ID -->
        <div class="dashboard-card">
            <img class="dashboard-img" src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80" alt="Assign Group">
            <div class="icon-box"><i class="fas fa-user-tie"></i></div>
            <h5>Assign Final Group ID</h5>
            <div class="dashboard-subtitle">Allocate official group IDs to teams</div>
            <a href="assign_final_group.php" class="dashboard-link"><i class="fas fa-arrow-right"></i> Go</a>
        </div>

        <!-- Assign Mentor -->
        <div class="dashboard-card">
            <img class="dashboard-img" src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80" alt="Assign Mentor">
            <div class="icon-box"><i class="fas fa-users"></i></div>
            <h5>Assign Mentor</h5>
            <div class="dashboard-subtitle">Connect mentors to student groups</div>
            <a href="assign_mentor_submit.php" class="dashboard-link"><i class="fas fa-arrow-right"></i> Go</a>
        </div>

        <!-- Register Users -->
        <div class="dashboard-card">
            <img class="dashboard-img" src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80" alt="Register Users">
            <div class="icon-box"><i class="fas fa-user-plus"></i></div>
            <h5>Register Users</h5>
            <div class="dashboard-subtitle">Add new users one by one to the system</div>
            <a href="register_users.php" class="dashboard-link"><i class="fas fa-arrow-right"></i> Go</a>
        </div>

        <!-- Upload Users -->
        <div class="dashboard-card">
            <img class="dashboard-img" src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80" alt="Upload Users">
            <div class="icon-box"><i class="fas fa-file-upload"></i></div>
            <h5>Register Faculty</h5>
            <div class="dashboard-subtitle">Bulk upload users via CSV or Excel file</div>
            <a href="register_faculty.php" class="dashboard-link"><i class="fas fa-arrow-right"></i> Go</a>
        </div>
<!-- View Marks -->
<div class="dashboard-card">
    <img class="dashboard-img" src="https://images.unsplash.com/photo-1509228468518-c5eeecbff44a?auto=format&fit=crop&w=400&q=80" alt="View Marks">
    <div class="icon-box"><i class="fas fa-chart-bar"></i></div>
    <h5>View Marks</h5>
    <div class="dashboard-subtitle">See marks uploaded for all groups</div>
    <a href="admin_view_marks.php" class="dashboard-link"><i class="fas fa-arrow-right"></i> Go</a>
</div>

        <!-- Logout -->
        <div class="dashboard-card">
            <img class="dashboard-img" src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=400&q=80" alt="Logout">
            <div class="icon-box"><i class="fas fa-sign-out-alt"></i></div>
            <h5>Logout</h5>
            <div class="dashboard-subtitle">Sign out of your admin session securely</div>
            <a href="admin_logout.php" class="dashboard-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</body>
</html>
