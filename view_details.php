<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    die("Access denied.");
}

if (!isset($_GET['final_group_id'])) {
    die("Group ID not provided.");
}

$final_group_id = $_GET['final_group_id'];

// Get group info
$group_sql = "SELECT pg.*, u.name AS leader_name 
              FROM project_groups pg 
              JOIN users u ON u.erpid = pg.leader_erpid 
              WHERE pg.final_group_id = ?";
$stmt = $conn->prepare($group_sql);
$stmt->bind_param("s", $final_group_id);
$stmt->execute();
$group_result = $stmt->get_result();

if ($group_result->num_rows === 0) {
    die("Group not found.");
}

$group = $group_result->fetch_assoc();

// Fetch group members
$member_sql = "SELECT gm.member_erpid, u.name, u.rollno, u.email 
               FROM group_members gm 
               JOIN users u ON u.erpid = gm.member_erpid 
               WHERE gm.final_group_id = ?";
$stmt = $conn->prepare($member_sql);
$stmt->bind_param("s", $final_group_id);
$stmt->execute();
$members_result = $stmt->get_result();


$marks_sql = "SELECT erpid, marks FROM marks WHERE final_group_id = ?";
$stmt = $conn->prepare($marks_sql);
$stmt->bind_param("s", $final_group_id);
$stmt->execute();
$marks_result = $stmt->get_result();

$marks_data = [];
while ($row = $marks_result->fetch_assoc()) {
    $marks_data[$row['erpid']] = $row['marks'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['marks'] as $erpid => $mark) {
        $mark = intval($mark);
        $insert_sql = "REPLACE INTO marks (erpid, final_group_id, marks) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssi", $erpid, $final_group_id, $mark);
        $stmt->execute();
    }
    echo "<script>alert('Marks uploaded successfully!'); location.href='view_details.php?final_group_id=$final_group_id';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Group Details</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #ffe0e7 100%);
            min-height: 100vh;
            margin: 0;
            padding: 40px 10px;
            animation: bgMove 16s ease-in-out infinite alternate;
        }
        @keyframes bgMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255,255,255,0.97);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(59,130,246,0.1);
            padding: 40px 28px;
            backdrop-filter: blur(8px);
            animation: floatIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 {
            color: #2563eb;
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            letter-spacing: 1px;
        }
        .section-title {
            font-size: 1.25rem;
            color: #2563eb;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #93c5fd;
            padding-bottom: 7px;
            font-weight: 600;
            background: none;
        }
        .section-title i {
            color: #f857a6;
            font-size: 1.2em;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }
        .info-item {
            background: #fff;
            padding: 18px 16px;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            font-size: 1.07rem;
            color: #333;
        }
        .info-item strong {
            color: #2563eb;
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: transparent;
        }
        th {
            background: linear-gradient(135deg, #2563eb 0%, #f857a6 100%);
            color: #fff;
            padding: 13px;
            font-weight: 600;
            text-align: left;
            border: none;
            font-size: 1rem;
        }
        td {
            padding: 13px;
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
            font-size: 1rem;
        }
        tr:hover td {
            background: #f8fafc;
        }
        .download-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
        }
        .download-link:hover {
            color: #f857a6;
        }
        input[type='number'] {
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            width: 100px;
            transition: border-color 0.3s;
            font-size: 1rem;
        }
        input[type='number']:focus {
            border-color: #2563eb;
            outline: none;
        }
        .upload-btn {
            background: linear-gradient(135deg, #2563eb 0%, #f857a6 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 22px;
            font-size: 1.07rem;
            box-shadow: 0 2px 8px #2563eb22;
        }
        .upload-btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 24px #2563eb33;
        }
        @media (max-width: 900px) {
            .container { padding: 18px 4vw; }
            h2 { font-size: 1.4rem; }
            .section-title { font-size: 1.05rem; }
        }
        @media (max-width: 600px) {
            .container { padding: 8px 1vw; }
            .info-grid { grid-template-columns: 1fr; }
            .info-item { padding: 12px 8px; }
            th, td { padding: 8px 4px; font-size: 0.97rem; }
        }
    </style>
</head>
<body>
<a href="admin_dashboard.php" class="reset-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
<a href="view-created_groups.php" class="reset-link"><i class="fas fa-sign-out-alt"></i> Back to previous page </a>
<div class="container">
    <h2>
        <i class="fas fa-users-cog"></i>
        Group Details - <?php echo htmlspecialchars($final_group_id); ?>
    </h2>

    <div class="section-title">
        <i class="fas fa-info-circle"></i>
        Project Information
    </div>
    <div class="info-grid">
        <div class="info-item">
            <strong>Group Name</strong>
            <?php echo htmlspecialchars($group['group_name']); ?>
        </div>
        <div class="info-item">
            <strong>Project Title</strong>
            <?php echo htmlspecialchars($group['project_title']); ?>
        </div>
        <div class="info-item">
            <strong>Project Field</strong>
            <?php echo htmlspecialchars($group['project_field']); ?>
        </div>
        <div class="info-item">
            <strong>Mentor</strong>
            <?php echo htmlspecialchars($group['mentor_name']); ?>
        </div>
        <div class="info-item">
            <strong>Group Leader</strong>
            <?php echo htmlspecialchars($group['leader_name']); ?>
        </div>
    </div>

    <div class="section-title">
        <i class="fas fa-users"></i>
        Group Members
    </div>
    <table>
        <thead>
            <tr>
                <th>ERP ID</th>
                <th>Name</th>
                <th>Roll No</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php while($member = $members_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($member['member_erpid']); ?></td>
                <td><?php echo htmlspecialchars($member['name']); ?></td>
                <td><?php echo htmlspecialchars($member['rollno']); ?></td>
                <td><?php echo htmlspecialchars($member['email']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="section-title">
        <i class="fas fa-file-archive"></i>
        Project Documents
    </div>
    <table>
        <thead>
            <tr>
                <th>ERP ID</th>
                <th>Document Type</th>
                <th>Download</th>
                <th>Upload Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch documents from group_uploads table using final_group_id
            $doc_sql = "SELECT * FROM group_uploads WHERE final_group_id = ?";
            $stmt = $conn->prepare($doc_sql);
            $stmt->bind_param("s", $final_group_id);
            $stmt->execute();
            $doc_result = $stmt->get_result();

            if ($doc_result->num_rows === 0) {
                echo "<tr><td colspan='4' style='text-align:center;color:#888;'>No documents uploaded yet.</td></tr>";
            } else {
                while ($doc = $doc_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($doc['member_erpid']) . "</td>";
                    echo "<td>" . htmlspecialchars($doc['file_type']) . "</td>";
                    echo "<td><a href='" . htmlspecialchars($doc['file_path']) . "' class='download-link' target='_blank'><i class='fas fa-download'></i>Download</a></td>";
                    echo "<td>" . htmlspecialchars($doc['uploaded_at']) . "</td>";
                    echo "</tr>";
                }
            }

            $stmt->close();
            ?>
        </tbody>
    </table>

    <div class="section-title"><i class="fas fa-users"></i> Group Members & Marks</div>
    <form method="POST">
    <table>
        <thead>
            <tr>
                <th>ERP ID</th>
                <th>Name</th>
                <th>Roll No</th>
                <th>Email</th>
                <th>Marks</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $members_result->data_seek(0); // reset pointer
            while($member = $members_result->fetch_assoc()): 
                $erpid = $member['member_erpid'];
                $mark = isset($marks_data[$erpid]) ? $marks_data[$erpid] : '';
            ?>
            <tr>
                <td><?php echo htmlspecialchars($erpid); ?></td>
                <td><?php echo htmlspecialchars($member['name']); ?></td>
                <td><?php echo htmlspecialchars($member['rollno']); ?></td>
                <td><?php echo htmlspecialchars($member['email']); ?></td>
                <td><input type="number" name="marks[<?php echo htmlspecialchars($erpid); ?>]" value="<?php echo htmlspecialchars($mark); ?>" min="0" max="100" required></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <button type="submit" class="upload-btn"><i class="fas fa-upload"></i> Save Marks</button>
    </form>
</div>

</body>
</html>
   