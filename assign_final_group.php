<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    die("Access denied.");
}

// Get last assigned final_group_id
$last_id_result = $conn->query("SELECT MAX(final_group_id) AS last_id FROM project_groups WHERE final_group_id IS NOT NULL");
$last_id = ($last_id_result && $row = $last_id_result->fetch_assoc()) ? (int)$row['last_id'] : 1000;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_final_id'])) {
    $group_id = $_POST['group_id'];
    $new_final_group_id = $last_id + 1;

    // Step 1: Update project_groups with new final_group_id and status
    $update_group = $conn->prepare("UPDATE project_groups SET final_group_id = ?,status = 'active' WHERE group_id = ?");
    $update_group->bind_param("ss", $new_final_group_id, $group_id);
    $update_group->execute();

    // Step 2: Update related tables AFTER the project_groups table
    $update_members = $conn->prepare("UPDATE group_members SET final_group_id = ? WHERE group_id = ?");
    $update_members->bind_param("ss", $new_final_group_id, $group_id);
    $update_members->execute();

    // $update_requests = $conn->prepare("UPDATE group_request SET group_id = ? WHERE group_id = ?");
    // $update_requests->bind_param("ss", $new_final_group_id, $group_id);
    // $update_requests->execute();

    // Refresh last_id
    $last_id_result = $conn->query("SELECT MAX(final_group_id) AS last_id FROM project_groups WHERE final_group_id IS NOT NULL");
    $last_id = ($last_id_result && $row = $last_id_result->fetch_assoc()) ? (int)$row['last_id'] : 1000;

    header("Location: assign_final_group.php");
    exit;
}


// Fetch groups that are eligible
$sql = "SELECT 
            pg.group_id,
            pg.group_name,
            pg.project_title,
            pg.project_field,
            pg.status,
            u.name AS leader_name
        FROM project_groups pg
        JOIN users u ON pg.leader_erpid = u.erpid
        WHERE pg.final_group_id IS NULL AND pg.status = 'pending'
        AND NOT EXISTS (
            SELECT 1 FROM group_members gm 
            WHERE gm.group_id = pg.group_id AND gm.status != 'accepted'
        )";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Assign Final Group ID</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            margin: 0;
            color: #1e293b;
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .header h2 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 1.8rem;
        }

        .status-bar {
            background: #e0f2fe;
            text-align: center;
            padding: 1rem;
            font-size: 1.1rem;
            color: #075985;
            margin: 1rem auto;
            max-width: 90%;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            animation: slideIn 0.6s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .table-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        th, td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .assign-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(16,185,129,0.2);
        }

        .assign-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(16,185,129,0.2);
        }

        .mentor-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(59,130,246,0.2);
        }

        .group-id {
            font-family: 'Courier New', monospace;
            color: #3b82f6;
            font-weight: 500;
        }

        .action-cell {
            display: flex;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
            
            table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h2>
        <i class="fas fa-users-gear"></i>
        Final Group Assignment
    </h2>
</div>

<div class="status-bar">
    <i class="fas fa-id-card"></i>
    Last Assigned Final Group ID: <strong class="group-id"><?php echo htmlspecialchars($last_id); ?></strong>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Group ID</th>
                <th>Group Name</th>
                <th>Leader</th>
                <th>Project Title</th>
                <th>Field</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td class="group-id"><?php echo htmlspecialchars($row['group_id']); ?></td>
                <td><?php echo htmlspecialchars($row['group_name']); ?></td>
                <td><?php echo htmlspecialchars($row['leader_name']); ?></td>
                <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                <td><?php echo htmlspecialchars($row['project_field']); ?></td>
                <td class="action-cell">
                    <form method="POST">
                        <input type="hidden" name="group_id" value="<?php echo $row['group_id']; ?>">
                        <button type="submit" name="assign_final_id" class="btn assign-btn">
                            <i class="fas fa-id-card"></i>
                            Assign Final ID
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
