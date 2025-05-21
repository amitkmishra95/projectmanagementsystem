<?php
session_start();
include 'db.php'; // Update with your DB connection file

if (!isset($_SESSION['erpid'])) {
    die("Access denied.");
}

$erpid = $_SESSION['erpid'];

// Accept request if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_member_id'])) {
    $request_id = $_POST['group_member_id'];

    $update_sql = "UPDATE group_members SET status = 'accepted' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
}

// Fetch pending requests
$sql = "SELECT gm.id, gm.member_erpid, gm.group_id, u.name, u.erpid 
        FROM group_members gm
        LEFT JOIN users u ON gm.member_erpid = u.erpid
        WHERE gm.member_erpid = ? AND gm.status = 'pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $erpid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Group Join Requests</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            margin: 0;
        }
        .requests-header {
            background: linear-gradient(90deg, #8c52ff 0%, #4f8cff 100%);
            color: #fff;
            border-radius: 0 0 36px 36px;
            text-align: center;
            padding: 48px 0 32px 0;
            box-shadow: 0 8px 32px rgba(44,62,80,0.16);
            margin-bottom: 36px;
        }
        .requests-header i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            color: #ffd700;
        }
        .requests-card {
            background: #fff;
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(44,62,80,0.10);
            padding: 2.5rem 1.5rem;
            max-width: 700px;
            margin: -60px auto 0 auto;
            animation: fadeIn 0.7s cubic-bezier(.4,2,.6,1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 0;
        }
        th, td {
            padding: 14px 12px;
            text-align: center;
        }
        th {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-bottom: none;
        }
        tr {
            border-radius: 1rem;
            transition: background 0.16s;
        }
        tr:hover {
            background: #f3e7fa44;
        }
        td {
            background: #fafaff;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .accept-btn {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 8px 24px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.2s;
            box-shadow: 0 2px 8px #4f8cff22;
        }
        .accept-btn:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 8px 24px #43cea255;
        }
        @media (max-width: 700px) {
            .requests-card { padding: 1.2rem 0.3rem; border-radius: 1rem;}
            th, td { padding: 10px 6px; font-size: 0.98rem;}
            .requests-header { padding: 28px 0 16px 0; border-radius: 0 0 18px 18px;}
        }
    </style>
</head>
<body>
    <div class="requests-header">
        <i class="fas fa-user-friends"></i>
        <h2 class="mb-2">Pending Group Join Requests</h2>
        <div style="font-size:1.1rem; color:#ffd700;">Accept to join your group and start collaborating!</div>
    </div>
    <div class="requests-card">
        <div class="table-responsive">
            <table>
                <tr>
                    <th><i class="fas fa-users"></i> Group ID</th>
                    <th><i class="fas fa-user"></i> Your Name</th>
                    <th><i class="fas fa-id-card"></i> Your ERP ID</th>
                    <th><i class="fas fa-check-circle"></i> Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['group_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['erpid']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="group_member_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="accept-btn">
                                    <i class="fas fa-check"></i> Accept
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <!-- <label for="back">Back to Dashboard</label> -->
                <a href="user_dashboard.php"><button class="btn btn-primary">Back to Dashboard</button></a>
            </table>
        </div>
    </div>
</body>
</html>
