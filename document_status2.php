<?php
session_start();
include("db.php");

$erpid = $_SESSION['erpid']; // Logged-in user's ERP ID

// Get group_id from group_members
$groupQuery = mysqli_query($conn, "SELECT group_id FROM group_members WHERE member_erpid = '$erpid' AND status = 'Accepted'");
if (mysqli_num_rows($groupQuery) == 0) {
    echo "You are not part of any accepted group.";
    exit;
}
$groupRow = mysqli_fetch_assoc($groupQuery);
$group_id = $groupRow['group_id'];

// Fetch group name
$groupNameResult = mysqli_query($conn, "SELECT group_name FROM project_groups WHERE group_id = '$group_id'");
$groupNameRow = mysqli_fetch_assoc($groupNameResult);
$group_name = $groupNameRow['group_name'] ?? "Group";

// Fetch uploaded documents for the group
$sql = "SELECT 
            gu.*, 
            u.name AS member_name, 
            u.erpid AS member_erpid
        FROM group_uploads gu
        LEFT JOIN users u ON gu.member_erpid = u.erpid
        WHERE gu.final_group_id = '$final_group_id'
        ORDER BY gu.uploaded_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group Uploads</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f8f9fa;
            color: #333;
        }
        h2 {
            color: #444;
            margin-bottom: 10px;
        }
        .group-name {
            font-size: 18px;
            margin-bottom: 20px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 14px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        a {
            color: #007bff;
            font-weight: bold;
        }
        .feedback {
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>

    <h2>Uploaded Documents</h2>
    <div class="group-name"><strong>Group Name:</strong> <?= htmlspecialchars($group_name) ?></div>

    <table>
        <tr>
            <th>File</th>
            <th>File Type</th>
            <th>Status</th>
            <th>Feedback</th>
            <th>Uploaded By</th>
            <th>ERP ID</th>
            <th>Uploaded At</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">View File</a></td>
                <td><?= htmlspecialchars($row['file_type']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td class="feedback"><?= $row['feedback'] ? htmlspecialchars($row['feedback']) : 'No feedback yet' ?></td>
                <td><?= htmlspecialchars($row['member_name']) ?></td>
                <td><?= htmlspecialchars($row['member_erpid']) ?></td>
                <td><?= date("d M Y, h:i A", strtotime($row['uploaded_at'])) ?></td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>
