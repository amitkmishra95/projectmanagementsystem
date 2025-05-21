<?php
session_start();
include 'db.php';

if (!isset($_SESSION['erpid'])) {
    header('Location: user_login.php');
    exit;
}

$erpid = $_SESSION['erpid'];

// Handle accept/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        mysqli_query($conn, "UPDATE group_members SET status = 'accepted' WHERE group_id = $group_id AND erpid = '$erpid'");
        mysqli_query($conn, "UPDATE group_request SET status = 'accepted' WHERE group_id = $group_id AND receiver_erpid = '$erpid'");
    } elseif ($action === 'reject') {
        mysqli_query($conn, "DELETE FROM group_members WHERE group_id = $group_id AND erpid = '$erpid'");
        mysqli_query($conn, "UPDATE group_request SET status = 'rejected' WHERE group_id = $group_id AND receiver_erpid = '$erpid'");
    }

    echo "<script>alert('Response recorded.'); window.location.href='pending_group_requests.php';</script>";
    exit;
}

// Get pending requests
$query = "SELECT gr.group_id, pg.group_name, pg.project_title, u.name as sender_name
          FROM group_request gr
          JOIN project_groups pg ON gr.group_id = pg.id
          JOIN users u ON gr.sender_erpid = u.erpid
          WHERE gr.receiver_erpid = '$erpid' AND gr.status = 'pending'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pending Group Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Pending Group Requests</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Group Name</th>
                    <th>Project Title</th>
                    <th>Invited By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['group_name']) ?></td>
                    <td><?= htmlspecialchars($row['project_title']) ?></td>
                    <td><?= htmlspecialchars($row['sender_name']) ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="group_id" value="<?= $row['group_id'] ?>">
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No pending group requests.</div>
    <?php endif; ?>
</div>
</body>
</html>
