<?php
session_start();
include("db.php");

if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php");
    exit;
}

$faculty_id = $_SESSION['faculty_id'];

// Get groups assigned to this mentor
$groupsResult = $conn->query("SELECT final_group_id, group_name FROM project_groups WHERE mentor_erpid = '$faculty_id'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_id = $_POST['upload_id'];
    $status = $_POST['status'];
    $feedback = $conn->real_escape_string($_POST['feedback']);

    $updateSql = "UPDATE group_uploads SET status='$status', feedback='$feedback' WHERE id='$upload_id'";
    if ($conn->query($updateSql)) {
        $msg = "Feedback updated successfully.";
    } else {
        $msg = "Error updating feedback: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Review Uploaded Documents</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0ecff 0%, #ffe0e7 100%);
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            animation: bgMove 16s ease-in-out infinite alternate;
        }
        @keyframes bgMove {
            0% { background-position: 0% 50%;}
            100% { background-position: 100% 50%;}
        }

        .review-container {
            max-width: 1200px;
            margin: 0 auto;
            animation: floatIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #2563eb;
            font-size: 2.2rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideInLeft 0.8s;
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .group-card {
            background: rgba(255,255,255,0.97);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(59,130,246,0.08);
            margin-bottom: 40px;
            padding: 30px;
            backdrop-filter: blur(6px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e7ef;
        }

        th {
            background: linear-gradient(135deg, #2563eb 0%, #f857a6 100%);
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .status-select {
            width: 120px;
            padding: 8px 12px;
            border-radius: 8px;
            border: 2px solid #e0e7ef;
            background: white;
            transition: all 0.3s;
        }

        .status-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e7ef;
            border-radius: 8px;
            resize: vertical;
            transition: all 0.3s;
        }

        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .submit-btn {
            background: linear-gradient(135deg, #2563eb 0%, #f857a6 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37,99,235,0.2);
        }

        .file-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .file-link:hover {
            color: #f857a6;
        }

        .msg {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .success-msg {
            background: linear-gradient(135deg, #43cea222 0%, #185a9d22 100%);
            color: #185a9d;
        }

        .error-msg {
            background: linear-gradient(135deg, #ff6b6b22 0%, #ff525222 100%);
            color: #cc0000;
        }

        @media (max-width: 768px) {
            .group-card {
                overflow-x: auto;
            }
            
            table {
                min-width: 800px;
            }
            
            h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="review-container">
        <h2>
            <i class="fas fa-file-alt"></i>
            Review Uploaded Documents
        </h2>

        <?php if (isset($msg)): ?>
            <div class="msg <?= strpos($msg, 'Error') !== false ? 'error-msg' : 'success-msg' ?>">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <?php if ($groupsResult->num_rows == 0): ?>
            <div class="group-card">
                <p>No groups assigned to you.</p>
            </div>
        <?php else: ?>
            <?php while ($group = $groupsResult->fetch_assoc()): ?>
                <div class="group-card">
                    <h3 style="color: #f857a6; margin-bottom: 20px;">
                        <i class="fas fa-users"></i>
                        <?= htmlspecialchars($group['group_name']) ?>
                    </h3>
                    
                    <?php
                    $final_group_id = $group['final_group_id'];
                    $uploadsResult = $conn->query("SELECT gu.*, u.name AS member_name FROM group_uploads gu LEFT JOIN users u ON gu.member_erpid = u.erpid WHERE gu.final_group_id='$final_group_id' ORDER BY gu.uploaded_at DESC");
                    ?>

                    <?php if ($uploadsResult->num_rows == 0): ?>
                        <p>No uploads for this group yet.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Uploaded By</th>
                                    <th>Uploaded At</th>
                                    <th>Status</th>
                                    <th>Feedback</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($upload = $uploadsResult->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <a href="<?= htmlspecialchars($upload['file_path']) ?>" class="file-link">
                                            <i class="fas fa-file-pdf"></i>
                                            View Document
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($upload['member_name']) ?></td>
                                    <td><?= date("d M Y, h:i A", strtotime($upload['uploaded_at'])) ?></td>
                                    <td>
                                        <span style="color: <?= 
                                            $upload['status'] == 'Accepted' ? '#43cea2' : 
                                            ($upload['status'] == 'Rejected' ? '#f857a6' : '#666') 
                                        ?>">
                                            <?= htmlspecialchars($upload['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($upload['feedback'])) ?: "<em style='color:#666'>No feedback</em>" ?></td>
                                    <td>
                                        <form method="post" style="display: grid; gap: 12px;">
                                            <input type="hidden" name="upload_id" value="<?= $upload['id'] ?>">
                                            <select name="status" class="status-select" required>
                                                <option value="Pending" <?= $upload['status']=='Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Accepted" <?= $upload['status']=='Accepted' ? 'selected' : '' ?>>Accepted</option>
                                                <option value="Rejected" <?= $upload['status']=='Rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                            <textarea name="feedback" rows="3" placeholder="Write feedback..."><?= htmlspecialchars($upload['feedback']) ?></textarea>
                                            <button type="submit" class="submit-btn">
                                                <i class="fas fa-save"></i>
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>
