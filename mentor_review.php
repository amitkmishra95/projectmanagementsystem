<?php
include 'db.php';
session_start();
// assume mentor is logged in and $_SESSION['faculty_id'] is set
$faculty_id = $_SESSION['faculty_id'];

// Get all group uploads assigned to this mentor
$uploads = mysqli_query($conn, "
    SELECT gu.*, g.group_name 
    FROM group_upload gu 
    JOIN groups g ON gu.group_id = g.id 
    JOIN group_mentor gm ON gm.group_id = g.id 
    WHERE gm.faculty_id = $faculty_id
");

if (isset($_POST['update'])) {
    $id = $_POST['upload_id'];
    $status = $_POST['status'];
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    mysqli_query($conn, "UPDATE group_upload SET status='$status', feedback='$feedback' WHERE id=$id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mentor Upload Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3 class="mb-4">Review Group Uploads</h3>

    <table class="table table-bordered bg-white">
        <thead>
        <tr>
            <th>Group Name</th>
            <th>Member ERP</th>
            <th>Type</th>
            <th>File</th>
            <th>Status</th>
            <th>Feedback</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($uploads)): ?>
            <tr>
                <td><?= htmlspecialchars($row['group_name']) ?></td>
                <td><?= $row['member_erpid'] ?></td>
                <td><?= $row['file_type'] ?></td>
                <td><a href="<?= $row['file_path'] ?>" target="_blank">Download</a></td>
                <form method="POST">
                    <td>
                        <select name="status" class="form-select">
                            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="accepted" <?= $row['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                            <option value="rejected" <?= $row['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </td>
                    <td>
                        <textarea name="feedback" class="form-control" rows="2"><?= htmlspecialchars($row['feedback']) ?></textarea>
                    </td>
                    <td>
                        <input type="hidden" name="upload_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="update" class="btn btn-sm btn-primary">Update</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
