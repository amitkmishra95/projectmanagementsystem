 <?php
session_start();
include 'db.php';

if (!isset($_SESSION['erpid'])) {
    header('Location: user_login.php');
    exit;
}

$erpid = $_SESSION['erpid'];

// Fetch current user info for section and branch
$userInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT section, branch FROM users WHERE erpid = '$erpid'"));
$section = $userInfo['section'];
$branch = $userInfo['branch'];
$created_at = date('Y-m-d H:i:s');

// Fetch all other users
$users = [];
$result = mysqli_query($conn, "SELECT erpid, name FROM users WHERE erpid != '$erpid'");
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $project_title = mysqli_real_escape_string($conn, $_POST['project_title']);
    $project_field = mysqli_real_escape_string($conn, $_POST['project_field']);
    $member1 = $_POST['member1'];
    $member2 = $_POST['member2'];
    $member3 = $_POST['member3'];
    $status = 'pending';
    $final_group_id = null;

    // Insert into project_groups
    $stmt = $conn->prepare("INSERT INTO project_groups (group_name, project_title, project_field, created_by, created_at, leader_erpid, section, branch, status, final_group_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $group_name, $project_title, $project_field, $erpid, $created_at, $erpid, $section, $branch, $status, $final_group_id);
    $stmt->execute();
    $group_id = $stmt->insert_id;
    $stmt->close();

    // Add group creator to group_members
    $stmt = $conn->prepare("INSERT INTO group_members (group_id, member_erpid, status) VALUES (?, ?, ?)");
    $status = 'accepted';
    $stmt->bind_param("iss", $group_id, $erpid, $status);
    $stmt->execute();

    $status = 'pending';
    foreach ([$member1, $member2, $member3] as $member) {
        if (!empty($member)) {
            $stmt->bind_param("iss", $group_id, $member, $status);
            $stmt->execute();
        }
    }
    $stmt->close();

    // Insert into group_request table
    $stmt = $conn->prepare("INSERT INTO group_request (group_id, sender_erpid, receiver_erpid, status, request_time) VALUES (?, ?, ?, ?, ?)");
    $status = 'pending';
    $request_time = $created_at;
    foreach ([$member1, $member2, $member3] as $member) {
        if (!empty($member)) {
            $stmt->bind_param("issss", $group_id, $erpid, $member, $status, $request_time);
            $stmt->execute();
        }
    }
    $stmt->close();

    echo "<script>alert('Group created successfully!'); window.location.href='user_dashboard.php';</script>";
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Group | Project Collaboration</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Open Sans', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .hero-section {
            position: relative;
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80') center center/cover no-repeat;
            min-height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0 0 40px 40px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.22);
            overflow: hidden;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 41, 59, 0.68);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
        }
        .hero-content h1 {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 2.6rem;
            font-weight: 900;
            letter-spacing: 2px;
        }
        .hero-content p {
            font-size: 1.2rem;
            color: #ffd700;
            margin-top: 10px;
            font-weight: 600;
        }
        .group-form-section {
            margin-top: -70px;
            margin-bottom: 32px;
            z-index: 10;
            position: relative;
        }
        .card {
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            border: none;
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
        }
        .card-header {
            background: linear-gradient(90deg, #4f8cff 0%, #8c52ff 100%);
            color: #fff;
            border-radius: 24px 24px 0 0;
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            text-align: center;
            letter-spacing: 1px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 1.5px solid #bdbdbd;
            font-size: 1.07rem;
        }
        .btn-primary {
            background: linear-gradient(90deg, #4f8cff 60%, #8c52ff 100%);
            border: none;
            border-radius: 999px;
            font-weight: 700;
            font-size: 1.15rem;
            padding: 12px 38px;
            box-shadow: 0 4px 16px #8c52ff22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.18s;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(90deg, #8c52ff 60%, #4f8cff 100%);
            box-shadow: 0 8px 24px #4f8cff44;
            transform: translateY(-2px) scale(1.03);
        }
        .group-illustration {
            max-width: 140px;
            margin: 0 auto 16px auto;
            display: block;
            border-radius: 18px;
            box-shadow: 0 4px 18px #4f8cff33;
        }
        .teamwork-gallery {
            margin: 32px 0 0 0;
            display: flex;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        .teamwork-gallery img {
            width: 130px;
            height: 90px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 2px 12px #8c52ff22;
            border: 2px solid #fff;
            transition: transform 0.18s, box-shadow 0.18s;
        }
        .teamwork-gallery img:hover {
            transform: scale(1.07) rotate(-2deg);
            box-shadow: 0 6px 24px #4f8cff55;
            border-color: #8c52ff;
        }
        @media (max-width: 600px) {
            .hero-section {
                min-height: 180px;
                border-radius: 0 0 20px 20px;
            }
            .group-form-section {
                margin-top: -40px;
            }
            .teamwork-gallery img {
                width: 90px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><i class="fas fa-users"></i> Create Your Project Group</h1>
            <p>Team up, collaborate, and innovate together!</p>
        </div>
    </div>
    <div class="container group-form-section">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-friends"></i> Group Creation Form
                    </div>
                    <div class="card-body">
                        <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=400&q=80"
                             alt="Teamwork Illustration"
                             class="group-illustration mb-3">
                        <form method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label for="group_name" class="form-label">Group Name</label>
                                <input type="text" class="form-control" id="group_name" name="group_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="project_title" class="form-label">Project Title</label>
                                <input type="text" class="form-control" id="project_title" name="project_title" required>
                            </div>
                            <div class="mb-3">
                                <label for="project_field" class="form-label">Project Field</label>
                                <input type="text" class="form-control" id="project_field" name="project_field" required>
                            </div>
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <div class="mb-3">
                                    <label for="member<?= $i ?>" class="form-label">Select Member <?= $i ?></label>
                                    <select class="form-select" id="member<?= $i ?>" name="member<?= $i ?>">
                                        <option value="">-- Select Member --</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['erpid'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= $user['erpid'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endfor; ?>
                            <button type="submit" class="btn btn-primary w-100 mt-2">
                                <i class="fas fa-plus-circle"></i> Create Group
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Teamwork image gallery below the form -->
                <div class="teamwork-gallery mt-4">
                    <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80" alt="Teamwork 1">
                    <img src="https://images.unsplash.com/photo-1521737852567-6949f3f9f2b5?auto=format&fit=crop&w=400&q=80" alt="Teamwork 2">
                    <img src="https://images.unsplash.com/photo-1503676382389-4809596d5290?auto=format&fit=crop&w=400&q=80" alt="Teamwork 3">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=400&q=80" alt="Teamwork 4">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
