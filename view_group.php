<?php
session_start();
include 'db.php';

if (!isset($_SESSION['erpid'])) {
    header('Location: user_login.php');
    exit;
}
$erpid = $_SESSION['erpid'];

$sql = "
    SELECT DISTINCT pg.*
    FROM project_groups pg
    LEFT JOIN group_members gm ON pg.group_id = gm.group_id
    WHERE pg.leader_erpid = '$erpid' OR gm.member_erpid = '$erpid'
    ORDER BY pg.created_at DESC
";

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Project Groups</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Open Sans', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
        }
        .hero-header {
            background: url('https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=1200&q=80') center center/cover no-repeat;
            min-height: 220px;
            border-radius: 0 0 36px 36px;
            position: relative;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            margin-bottom: 40px;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 41, 59, 0.7);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            padding: 60px 0 30px 0;
        }
        .hero-content h1 {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 2.4rem;
            font-weight: 900;
            letter-spacing: 2px;
        }
        .hero-content p {
            font-size: 1.13rem;
            color: #ffd700;
            margin-top: 10px;
            font-weight: 600;
        }
        .group-card {
            border-radius: 22px;
            box-shadow: 0 4px 20px rgba(44,62,80,0.10);
            transition: 0.3s cubic-bezier(.4,2,.6,1);
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
            border: none;
            position: relative;
            overflow: hidden;
        }
        .group-card:hover {
            box-shadow: 0 8px 32px #8c52ff33;
            transform: translateY(-6px) scale(1.025);
            border: 2px solid #8c52ff;
        }
        .project-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px #4f8cff22;
            position: absolute;
            top: -29px;
            left: 50%;
            transform: translateX(-50%);
            background: #f3f4f6;
        }
        .card-body {
            padding-top: 38px;
        }
        .card-title {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #4f8cff;
            margin-bottom: 10px;
        }
        .project-meta {
            font-size: 0.97rem;
            color: #222;
            margin-bottom: 7px;
        }
        .card-footer {
            background: none;
            border-top: none;
            text-align: right;
        }
        .btn-primary {
            background: linear-gradient(90deg, #4f8cff 60%, #8c52ff 100%);
            border: none;
            border-radius: 999px;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 8px 24px;
            box-shadow: 0 4px 16px #8c52ff22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.18s;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(90deg, #8c52ff 60%, #4f8cff 100%);
            box-shadow: 0 8px 24px #4f8cff44;
            transform: translateY(-2px) scale(1.03);
        }
        .group-icon {
            font-size: 2.2rem;
            color: #8c52ff;
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            .hero-header {
                min-height: 120px;
                border-radius: 0 0 18px 18px;
            }
            .project-avatar {
                width: 38px;
                height: 38px;
                top: -19px;
            }
            .card-title {
                font-size: 1.07rem;
            }
        }
    </style>
</head>
<body>
    <div class="hero-header mb-5">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><i class="fas fa-users-group"></i> All Project Groups</h1>
            <p>Discover, connect, and get inspired by amazing teams!</p>
        </div>
    </div>
    <div class="container pb-5">
        <div class="group-header">
    <div class="header-overlay"></div>
    <div class="container group-content">
        <a href="user_dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    <br><br>
</div>
        <div class="row g-4">
            <?php while ($group = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card group-card shadow-sm mb-3">
                        <!-- Project avatar: use a random teamwork image for demo, or your own logic -->
                        <img class="project-avatar"
                             src="https://source.unsplash.com/collection/895539/<?= rand(200,999) ?>x<?= rand(200,999) ?>"
                             alt="Project Avatar">
                        <div class="card-body text-center">
                            <div class="group-icon"><i class="fas fa-users"></i></div>
                            <h4 class="card-title"><?= htmlspecialchars($group['group_name']) ?></h4>
                            <div class="project-meta"><strong>Title:</strong> <?= htmlspecialchars($group['project_title']) ?></div>
                            <div class="project-meta"><strong>Field:</strong> <?= htmlspecialchars($group['project_field']) ?></div>
                        </div>
                        <div class="card-footer">
                            <a href="group_details.php?id=<?= $group['group_id'] ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-right"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
