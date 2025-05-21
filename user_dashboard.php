<?php
session_start();
include 'db.php'; // your database connection file

if (!isset($_SESSION['erpid'])) {
    header('Location: user_login.php');
    exit;
}

$erpid = $_SESSION['erpid'];
$query = "SELECT name FROM users WHERE erpid = '$erpid'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$name = $row['name'];
?>
<?php
//session_start();
//include 'db.php';

if (!isset($_SESSION['erpid'])) {
    header('Location: user_login.php');
    exit;
}

$erpid = $_SESSION['erpid'];
$query = "SELECT name FROM users WHERE erpid = '$erpid'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$name = $row['name'];

$projectCountResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM projects WHERE erpid = '$erpid'");
$total_projects = mysqli_fetch_assoc($projectCountResult)['total'];

$groupCountResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM project_groups WHERE leader_erpid = '$erpid'");
$total_groups = mysqli_fetch_assoc($groupCountResult)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e3f0ff 100%);
        font-family: 'Open Sans', Arial, sans-serif;
        color: #232946;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .container {
        margin-top: 48px;
        max-width: 1100px;
        width: 95%;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .image-section {
        text-align: center;
        margin-bottom: 38px;
    }
    .image-section img {
        max-width: 100%;
        border-radius: 28px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        user-select: none;
        max-height: 220px;
        object-fit: cover;
        animation: fadeIn 1.2s;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(40px);}
        to {opacity: 1; transform: translateY(0);}
    }
    .welcome-section {
        text-align: center;
        margin-bottom: 36px;
        color: #3d5af1;
        font-weight: 700;
        animation: fadeInUp 1.2s;
    }
    .welcome-section h2 {
        font-size: 2.3rem;
        font-family: 'Montserrat', Arial, sans-serif;
        font-weight: 700;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }
    .welcome-section p {
        font-size: 1.08rem;
        color: #232946bb;
    }
    @keyframes fadeInUp {
        from {opacity: 0; transform: translateY(30px);}
        to {opacity: 1; transform: translateY(0);}
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 32px;
        margin-bottom: 34px;
    }
    .dashboard-card {
        border-radius: 22px;
        padding: 0 0 28px 0;
        box-shadow: 0 10px 40px rgba(61,90,241,0.09);
        transition: all 0.3s cubic-bezier(.4,2,.6,1);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        background: #fff;
        color: #232946;
        position: relative;
        font-weight: 600;
        animation: cardPop 0.85s;
        overflow: hidden;
    }
    @keyframes cardPop {
        from {opacity: 0; transform: scale(0.95);}
        to {opacity: 1; transform: scale(1);}
    }
    .dashboard-card:hover {
        transform: translateY(-8px) scale(1.04);
        box-shadow: 0 15px 50px rgba(61,90,241,0.18);
        filter: brightness(1.05) saturate(1.1);
        z-index: 2;
    }
    .card-img-top {
        width: 100%;
        height: 110px;
        object-fit: cover;
        border-radius: 22px 22px 0 0;
        margin-bottom: 0;
        background: #e3f0ff;
        border-bottom: 1px solid #f0f2fa;
        transition: filter 0.3s;
    }
    .icon-box {
        font-size: 2.1rem;
        margin-top: -32px;
        margin-bottom: 10px;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(61,90,241,0.10);
        background: #e3f0ff;
        color: #3d5af1;
        border: 3px solid #fff;
        position: relative;
        z-index: 2;
    }
    .dashboard-card h5 {
        font-size: 1.18rem;
        font-family: 'Montserrat', Arial, sans-serif;
        font-weight: 700;
        margin-bottom: 10px;
        color: #232946;
        margin-top: 8px;
    }
    .btn-custom {
        padding: 10px 24px;
        font-size: 1.04rem;
        border-radius: 9999px;
        border: none;
        color: #fff;
        background: linear-gradient(90deg, #3d5af1 70%, #43cea2 100%);
        box-shadow: 0 8px 20px #3d5af122;
        transition: background 0.25s, box-shadow 0.25s, transform 0.18s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        text-decoration: none;
        margin-top: 18px;
    }
    .btn-custom:hover, .btn-custom:focus {
        background: linear-gradient(90deg, #43cea2 70%, #3d5af1 100%);
        box-shadow: 0 12px 28px #43cea244;
        color: #fff;
        text-decoration: none;
        transform: translateY(-2px) scale(1.03);
    }
    .btn-custom i {
        font-size: 1.2rem;
    }
    .btn-danger {
        background: linear-gradient(90deg, #ef4444 70%, #b91c1c 100%) !important;
        box-shadow: 0 8px 20px #ef4444bb !important;
    }
    .btn-danger:hover, .btn-danger:focus {
        background: linear-gradient(90deg, #b91c1c 70%, #ef4444 100%) !important;
        box-shadow: 0 12px 28px #b91c1cbb !important;
    }
    .stat-card {
        cursor: default !important;
        background: linear-gradient(120deg, #f3f7fa 0%, #e3f0ff 100%);
        border: 2px solid #3d5af1;
        color: #3d5af1;
    }
    .stat-value {
        font-size: 2.1rem;
        font-weight: 800;
        color: #3d5af1;
        margin-top: 4px;
        font-family: 'Montserrat', Arial, sans-serif;
        letter-spacing: 1px;
        transition: color 0.3s;
    }
    .stat-label {
        font-size: 1.03rem;
        color: #232946;
        font-weight: 600;
        margin-bottom: 4px;
    }
    footer {
        text-align: center;
        padding: 18px 10px 10px 10px;
        font-size: 0.98rem;
        color: #3d5af1;
        user-select: none;
        margin-top: 10px;
        background: #e3f0ff;
        border-top: 1px solid #c7d0e4;
    }
    @media (max-width: 600px) {
        .dashboard-card {
            padding: 0 0 14px 0;
        }
        .btn-custom {
            padding: 8px 14px;
            font-size: 1rem;
        }
        .icon-box {
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            margin-top: -18px;
        }
        .welcome-section h2 {
            font-size: 1.3rem;
        }
        .card-img-top {
            height: 70px;
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <section class="image-section" aria-label="Dashboard illustration">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80" alt="Students collaborating on projects" />
        </section>

        <section class="welcome-section" aria-label="Welcome message">
            <h2>👋 Welcome, <strong><?php echo htmlspecialchars($name); ?></strong>!</h2>
            <p>Your personalized project management dashboard</p>
        </section>

        <section class="dashboard-grid" aria-label="Dashboard actions">
            <!-- Each card now has a relevant image -->
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=400&q=80" alt="Create Group">
                <div class="icon-box"><i class="fas fa-users"></i></div>
                <h5>Create Group</h5>
                <a href="group.php" class="btn-custom"><i class="fas fa-plus-circle"></i> Go</a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=400&q=80" alt="Upload Documents">
                <div class="icon-box"><i class="fas fa-file-upload"></i></div>
                <h5>Upload Documents</h5>
                <a href="upload_document.php" class="btn-custom"><i class="fas fa-upload"></i> Upload</a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1453928582365-b6ad33cbcf64?auto=format&fit=crop&w=400&q=80" alt="Document Status">
                <div class="icon-box"><i class="fas fa-tasks"></i></div>
                <h5>Document Status</h5>
                <a href="document_status.php" class="btn-custom" style="background:linear-gradient(90deg,#ffb347 70%,#ffcc33 100%);color:#222;">
                    <i class="fas fa-info-circle"></i> View Status
                </a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=400&q=80" alt="My Uploaded Projects">
                <div class="icon-box"><i class="fas fa-folder-open"></i></div>
                <h5>My Uploaded Projects</h5>
                <a href="upload_project.php" class="btn-custom" style="background:linear-gradient(90deg,#43cea2 70%,#185a9d 100%);">
                    <i class="fas fa-folder"></i> View Mine
                </a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=400&q=80" alt="Manage Group">
                <div class="icon-box"><i class="fas fa-eye"></i></div>
                <h5>Manage Group</h5>
                <a href="view_group.php" class="btn-custom"><i class="fas fa-cogs"></i> Go</a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80" alt="Upload Project">
                <div class="icon-box"><i class="fas fa-upload"></i></div>
                <h5>Upload Project</h5>
                <a href="upload_project.php" class="btn-custom"><i class="fas fa-upload"></i> Upload</a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=crop&w=400&q=80" alt="View Profile">
                <div class="icon-box"><i class="fas fa-user"></i></div>
                <h5>View Profile</h5>
                <a href="user_profile.php" class="btn-custom" style="background:linear-gradient(90deg,#f7971e 70%,#ffd200 100%);color:#222;">
                    <i class="fas fa-user-circle"></i> Profile
                </a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80" alt="Accept Requests">
                <div class="icon-box"><i class="fas fa-eye"></i></div>
                <h5>Accept Requests</h5>
                <a href="accept_group_requests.php" class="btn-custom"><i class="fas fa-cogs"></i> Go</a>
            </div>
            <div class="dashboard-card">
                <img class="card-img-top" src="https://images.unsplash.com/photo-1503676382389-4809596d5290?auto=format&fit=crop&w=400&q=80" alt="Logout">
                <div class="icon-box"><i class="fas fa-sign-out-alt"></i></div>
                <h5>Logout</h5>
                <a href="logout.php" class="btn-custom btn-danger" style="background:linear-gradient(90deg,#f857a6 70%,#ff5858 100%);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </section>

        <section class="dashboard-grid" aria-label="Statistics cards" style="margin-bottom:40px;">
            <div class="dashboard-card stat-card">
                <div class="stat-label">Total Uploaded Projects</div>
                <div class="stat-value" id="total-projects" aria-live="polite" aria-atomic="true"><?php echo $total_projects; ?></div>
            </div>
            <div class="dashboard-card stat-card">
                <div class="stat-label">Total Created Groups</div>
                <div class="stat-value" id="total-groups" aria-live="polite" aria-atomic="true"><?php echo $total_groups; ?></div>
            </div>
        </section>
    </div>
    <footer>© <?php echo date("Y"); ?> Student Project Management System</footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
