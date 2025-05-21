<?php
session_start();
include("db.php");

$erpid = $_SESSION['erpid'];

// Get group_id of user
$groupQuery = mysqli_query($conn, "SELECT group_id FROM group_members WHERE member_erpid = '$erpid' AND status = 'Accepted'");
if (mysqli_num_rows($groupQuery) == 0) {
    echo "You are not part of any accepted group.";
    exit;
}
$groupRow = mysqli_fetch_assoc($groupQuery);
$group_id = $groupRow['group_id'];

// Get final_group_id and group_name
$groupInfoQuery = mysqli_query($conn, "SELECT final_group_id, group_name FROM project_groups WHERE group_id = '$group_id'");
$groupInfoRow = mysqli_fetch_assoc($groupInfoQuery);
$final_group_id = $groupInfoRow['final_group_id'];
$group_name = $groupInfoRow['group_name'] ?? "Group";

// Fetch uploaded documents using final_group_id
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
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
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
        <?php while ($row = mysqli_fetch_assoc($result)) { 
            // Assuming file_path stores full absolute path like C://xampp4/htdocs/pms/uploads/filename.ext
            // Extract file name to build URL relative to webroot /uploads/
            $fileName = basename($row['file_path']); 
            $webPath = "uploads/" . $fileName; // URL relative to your site root
            
            // Optionally verify if file exists on server
            $fullPath = "C:/xampp4/htdocs/pms/uploads/" . $fileName;
            if (!file_exists($fullPath)) {
                $fileExists = false;
            } else {
                $fileExists = true;
            }
        ?>
            <tr>
                <td>
                    <?php if ($fileExists): ?>
                        <a href="<?= htmlspecialchars($webPath) ?>" >View File</a>
                    <?php else: ?>
                        File missing
                    <?php endif; ?>
                </td>
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
