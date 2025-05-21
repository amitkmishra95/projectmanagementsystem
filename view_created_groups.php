<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    die("Access denied.");
}

$sql = "SELECT 
            pg.final_group_id,
            pg.group_name,
            pg.project_title,
            pg.project_field,
            pg.mentor_name,
            u.name AS leader_name
        FROM project_groups pg
        JOIN users u ON u.erpid = pg.leader_erpid
        WHERE pg.final_group_id IS NOT NULL 
        AND NOT EXISTS (
            SELECT 1 FROM group_members gm 
            WHERE gm.group_id = pg.group_id AND gm.status != 'accepted'
        )";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Accepted Groups</title>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #e3f0ff 0%, #f8fcff 100%);
            min-height: 100vh;
            margin: 0;
            color: #232b38;
        }
        .header {
            text-align: center;
            padding: 44px 10px 22px 10px;
            background: linear-gradient(90deg, #e0ecff 60%, #e3f0ff 100%);
            animation: fadeIn 1.2s;
        }
        .header .fa-users {
            color: #3b82f6;
            font-size: 2.7rem;
            margin-bottom: 8px;
            animation: icon-bounce 1.8s infinite;
        }
        @keyframes icon-bounce {
            0%,100% { transform: translateY(0);}
            50% { transform: translateY(-9px);}
        }
        .header h1 {
            font-size: 1.7rem;
            font-weight: 700;
            margin: 10px 0 0 0;
            color: #2563eb;
            letter-spacing: 0.5px;
            animation: slideInDown 1s;
        }
        .header .subtitle {
            color: #5a6b8a;
            font-size: 1.04rem;
            margin-top: 7px;
            font-weight: 400;
            letter-spacing: 0.2px;
            animation: fadeInUp 1.3s;
        }
        @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
        @keyframes slideInDown { from {opacity:0; transform: translateY(-30px);} to {opacity:1; transform: translateY(0);} }
        @keyframes fadeInUp { from {opacity:0; transform: translateY(22px);} to {opacity:1; transform: translateY(0);} }
        .table-container {
            max-width: 1050px;
            margin: 32px auto 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 16px rgba(59,130,246,0.09);
            padding: 0 0 10px 0;
            animation: fadeInUp 1.1s;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
        }
        th, td {
            padding: 13px 10px;
            text-align: center;
            font-size: 1rem;
        }
        th {
            color: #2563eb;
            font-weight: 700;
            background: #e0ecff;
            border-bottom: 2px solid #bae6fd;
            letter-spacing: 0.3px;
        }
        tr {
            transition: background 0.16s;
        }
        tr:hover td {
            background: #e0f2fe;
        }
        td {
            border-bottom: 1px solid #e5eaf3;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .leader-badge {
            display: inline-block;
            background: linear-gradient(90deg, #60a5fa 60%, #a5b4fc 100%);
            color: #fff;
            border-radius: 999px;
            padding: 3px 14px 3px 10px;
            font-size: 0.98rem;
            font-weight: 600;
            letter-spacing: 0.2px;
            box-shadow: 0 1px 4px #60a5fa22;
            margin-left: 4px;
            vertical-align: middle;
            animation: fadeIn 1.2s;
        }
        .mentor-icon {
            color: #2563eb;
            margin-right: 5px;
            font-size: 1rem;
            vertical-align: middle;
        }
        .group-id {
            color: #3b82f6;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .view-link a {
            color: #1d4ed8;
            font-weight: 600;
            text-decoration: none;
            background: #e0ecff;
            padding: 6px 12px;
            border-radius: 12px;
            transition: 0.2s;
        }
        .view-link a:hover {
            background: #dbeafe;
            color: #1e3a8a;
        }
        @media (max-width: 700px) {
            .table-container { max-width: 98vw; }
            th, td { padding: 9px 2px; font-size: 0.95rem; }
        }
    </style>
</head>
<body>

<div class="header">
    <i class="fas fa-users"></i>
    <h1>Accepted Project Groups</h1>
    <div class="subtitle">All groups here are fully accepted and assigned a mentor.</div>
</div>
<a href="admin_dashboard.php" class="reset-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
<div class="table-container">
    <table>
        <thead>
        <tr>
            <th>Group ID</th>
            <th>Group Name</th>
            <th>Leader</th>
            <th>Project Title</th>
            <th>Field</th>
            <th>Mentor</th>
            <th>Details</th>
        </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="group-id"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($row['final_group_id']); ?></td>
            <td><?php echo htmlspecialchars($row['group_name']); ?></td>
            <td>
                <span class="leader-badge">
                    <i class="fas fa-crown"></i>
                    <?php echo htmlspecialchars($row['leader_name']); ?>
                </span>
            </td>
            <td><?php echo htmlspecialchars($row['project_title']); ?></td>
            <td><?php echo htmlspecialchars($row['project_field']); ?></td>
            <td>
                <i class="fas fa-chalkboard-teacher mentor-icon"></i>
                <?php echo htmlspecialchars($row['mentor_name']); ?>
            </td>
            <td class="view-link">
                <a href="view_details.php?final_group_id=<?php echo urlencode($row['final_group_id']); ?>">View Details</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
