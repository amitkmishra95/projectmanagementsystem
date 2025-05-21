<?php
session_start();
include 'db.php';

$group_id = intval($_GET['id']);

// Fetch group info
$group_result = mysqli_query($conn, "SELECT * FROM project_groups WHERE group_id = $group_id");
$group = mysqli_fetch_assoc($group_result);

if (!$group) {
    echo "Group not found.";
    exit;
}

$final_group_id = $group['final_group_id'];
$mentor_id = $group['mentor_erpid'];
$leader_erpid = $group['leader_erpid'];

// Get mentor name if assigned
$mentor_name = null;
if (!empty($mentor_id)) {
    $mentor_result = mysqli_query($conn, "SELECT name FROM faculty WHERE faculty_Id = '$mentor_id'");
    if ($mentor_row = mysqli_fetch_assoc($mentor_result)) {
        $mentor_name = $mentor_row['name'];
    }
}

// Fetch group members
$members_result = mysqli_query($conn, "
    SELECT gm.member_erpid, gm.status, u.name
    FROM group_members gm
    JOIN users u ON gm.member_erpid = u.erpid
    WHERE gm.group_id = $group_id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Group Details</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Open Sans', Arial, sans-serif;
            min-height: 100vh;
            color: #e4e8ef;
        }
        .group-header {
            background: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80') center center/cover;
            min-height: 220px;
            border-radius: 0 0 36px 36px;
            position: relative;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
        }
        .header-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 41, 59, 0.85);
            border-radius: 0 0 36px 36px;
        }
        .group-content {
            position: relative;
            z-index: 2;
            padding: 60px 0 30px 0;
        }
        .back-btn {
            background: linear-gradient(90deg, #4f8cff 0%, #8c52ff 100%);
            border: none;
            border-radius: 999px;
            padding: 8px 24px;
            color: white;
            font-weight: 600;
            transition: 0.3s ease;
        }
        .back-btn:hover {
            transform: translateX(-4px);
            box-shadow: 0 4px 16px #4f8cff44;
        }
        .group-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(240,240,240,0.95) 100%);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.12);
            color: #222;
            border: none;
            backdrop-filter: blur(4px);
        }
        .group-title {
            font-family: 'Montserrat', Arial, sans-serif;
            color: #4f8cff;
            font-weight: 900;
            letter-spacing: -0.5px;
        }
        .detail-badge {
            background: linear-gradient(90deg, #4f8cff30 0%, #8c52ff30 100%);
            border-radius: 12px;
            padding: 12px 18px;
            margin: 8px 0;
        }
        .member-table {
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            overflow: hidden;
        }
        .member-table thead {
            background: linear-gradient(90deg, #4f8cff 0%, #8c52ff 100%);
            color: white;
        }
        .member-table th {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
        }
        .status-accepted { background: #43cea222; color: #185a9d; }
        .status-pending { background: #ffd70022; color: #b8860b; }
        @media (max-width: 768px) {
            .group-header {
                min-height: 180px;
                border-radius: 0 0 24px 24px;
            }
        }
    </style>
</head>
<body>
<div class="group-header">
    <div class="header-overlay"></div>
    <div class="container group-content">
        <a href="view_group.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Groups
        </a>
    </div>
</div>

<div class="container">
    <div class="group-card p-4 mb-4">
        <div class="row">
            <div class="col-md-8">
                <h1 class="group-title mb-3"><?= htmlspecialchars($group['group_name']) ?></h1>
                <div class="detail-badge">
                    <i class="fas fa-tag me-2"></i>
                    <strong>Project Title:</strong> <?= htmlspecialchars($group['project_title']) ?>
                </div>
                <div class="detail-badge">
                    <i class="fas fa-layer-group me-2"></i>
                    <strong>Field:</strong> <?= htmlspecialchars($group['project_field']) ?>
                </div>
                <div class="detail-badge">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Group ID:</strong>
                    <?= $final_group_id ? htmlspecialchars($final_group_id) : 'Your group has not been approved yet.' ?>
                </div>
                <div class="detail-badge">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    <strong>Mentor:</strong>
                    <?= $mentor_name ? htmlspecialchars($mentor_name) : 'Your group has not been assigned a mentor yet.' ?>
                </div>
            </div>
        </div>
    </div>

    <div class="group-card p-4">
        <h4 class="group-title mb-4"><i class="fas fa-users me-2"></i>Members</h4>
        <div class="member-table">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th><i class="fas fa-id-card me-2"></i>ERP ID</th>
                    <th><i class="fas fa-user me-2"></i>Name</th>
                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($member = mysqli_fetch_assoc($members_result)): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($member['member_erpid']) ?>
                            <?= ($member['member_erpid'] === $leader_erpid) ? '<span class="badge bg-primary ms-2">Leader</span>' : '' ?>
                        </td>
                        <td><?= htmlspecialchars($member['name']) ?></td>
                        <td>
                            <span class="status-badge status-<?= $member['status'] ?>">
                                <?= ucfirst($member['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
