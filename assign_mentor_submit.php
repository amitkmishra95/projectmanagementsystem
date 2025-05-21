<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    die("Access denied.");
}

// Handle mentor assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_group_id'], $_POST['mentor_erpid'])) {
    $final_group_id = $_POST['final_group_id'];
    $mentor_erpid = $_POST['mentor_erpid'];

    // Fetch mentor name from faculty table
    $stmt = $conn->prepare("SELECT name FROM faculty WHERE faculty_id = ?");
    $stmt->bind_param("s", $mentor_erpid);
    $stmt->execute();
    $stmt->bind_result($mentor_name);
    $stmt->fetch();
    $stmt->close();

    if ($mentor_name) {
        // Update project_groups with mentor ID and name
        $stmt = $conn->prepare("UPDATE project_groups SET mentor_erpid = ?, mentor_name = ? WHERE final_group_id = ?");
        $stmt->bind_param("ssi", $mentor_erpid, $mentor_name, $final_group_id);
        $stmt->execute();
        $stmt->close();

        header("Location: assign_mentor_submit.php");
        exit;
    }
}

// Fetch eligible groups
$sql = "SELECT 
            pg.final_group_id,
            pg.group_name,
            pg.project_title,
            pg.project_field,
            u.name AS leader_name
        FROM project_groups pg
        JOIN users u ON pg.leader_erpid = u.erpid
        WHERE pg.final_group_id IS NOT NULL AND pg.mentor_erpid IS NULL";

$result = $conn->query($sql);

// Fetch mentors from faculty table
$mentors = $conn->query("SELECT faculty_id, name FROM faculty");
$mentor_options = [];
while ($row = $mentors->fetch_assoc()) {
    $mentor_options[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Mentor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
            min-height: 100vh;
            margin: 0;
            color: #1e293b;
        }
        .hero-section {
            background: linear-gradient(90deg, #e0ecff 60%, #f8fafc 100%);
            border-radius: 0 0 36px 36px;
            box-shadow: 0 4px 24px rgba(59,130,246,0.08);
            min-height: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: fadeIn 1.2s;
        }
        .hero-img {
            width: 90px;
            height: 90px;
            margin-bottom: 10px;
            animation: floatY 3.2s ease-in-out infinite;
            user-select: none;
        }
        @keyframes floatY {
            0%,100% { transform: translateY(0);}
            50% { transform: translateY(-16px);}
        }
        .hero-title {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: 1px;
            color: #2563eb;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInDown 1s;
        }
        .hero-subtitle {
            font-size: 1.07rem;
            color: #3b82f6;
            font-style: italic;
            animation: fadeInUp 1.3s;
        }
        @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
        @keyframes slideInDown { from {opacity:0; transform: translateY(-30px);} to {opacity:1; transform: translateY(0);} }
        @keyframes fadeInUp { from {opacity:0; transform: translateY(22px);} to {opacity:1; transform: translateY(0);} }
        .table-card {
            max-width: 1100px;
            margin: -40px auto 40px auto;
            border-radius: 22px;
            background: rgba(255,255,255,0.97);
            box-shadow: 0 8px 32px rgba(59,130,246,0.10), 0 1.5px 8px #60a5fa22;
            animation: floatIn 1.1s cubic-bezier(.4,2,.6,1);
            backdrop-filter: blur(6px);
            padding: 1.5rem 1rem 2.5rem 1rem;
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(60px);}
            to { opacity: 1; transform: translateY(0);}
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }
        th, td {
            padding: 15px 16px;
            border-bottom: 1px solid #e3e8f7;
            text-align: center;
            vertical-align: middle;
            transition: background-color 0.3s;
        }
        th {
            background: #e0ecff;
            color: #2563eb;
            font-size: 1.05rem;
            font-weight: 700;
            border: none;
            letter-spacing: 0.3px;
        }
        tr {
            opacity: 0;
            transform: translateY(30px);
            animation: rowFadeIn 0.5s forwards;
        }
        tr:nth-child(even) { background: #f8fafc; }
        tr:nth-child(odd) { background: #fff; }
        @keyframes rowFadeIn {
            to { opacity: 1; transform: none; }
        }
        tr:hover td {
            background: #e0f2fe;
        }
        .final-group-id {
            font-family: 'Courier New', monospace;
            color: #3b82f6;
            font-weight: 600;
        }
        .assign-form {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }
        select {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            font-size: 1rem;
            transition: all 0.2s;
            min-width: 170px;
        }
        select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.14);
            outline: none;
        }
        .btn {
            padding: 0.55rem 1.1rem;
            background: linear-gradient(90deg, #60a5fa 60%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.2px;
            cursor: pointer;
            box-shadow: 0 2px 8px #60a5fa22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn:hover {
            background: linear-gradient(90deg, #2563eb 60%, #60a5fa 100%);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 6px 18px #2563eb22;
        }
        @media (max-width: 900px) {
            .table-card { max-width: 98vw; }
            th, td { padding: 10px 4px; font-size: 0.97rem; }
        }
        @media (max-width: 600px) {
            .hero-section { min-height: 110px; border-radius: 0 0 18px 18px;}
            .hero-title { font-size: 1.13rem;}
            .table-card { padding: 0.7rem 0.1rem 1.2rem 0.1rem;}
        }
    </style>
</head>
<body>

<div class="hero-section">
    <img class="hero-img" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Mentor Assignment" />
    <div class="hero-title">
        <i class="fas fa-user-tie"></i> Assign Project Mentors
    </div>
    <div class="hero-subtitle">
        Assign a mentor to each finalized group below.
    </div>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Group ID</th>
                <th>Group Name</th>
                <th>Leader</th>
                <th>Project Title</th>
                <th>Field</th>
                <th>Assign Mentor</th>
            </tr>
        </thead>
        <tbody>
            <?php $rowIndex = 0; while($row = $result->fetch_assoc()): $rowIndex++; ?>
            <tr style="animation-delay: <?= 0.1 * $rowIndex ?>s;">
                <td class="final-group-id"><?php echo htmlspecialchars($row['final_group_id']); ?></td>
                <td><?php echo htmlspecialchars($row['group_name']); ?></td>
                <td><?php echo htmlspecialchars($row['leader_name']); ?></td>
                <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                <td><?php echo htmlspecialchars($row['project_field']); ?></td>
                <td>
                    <form class="assign-form" method="POST">
                        <input type="hidden" name="final_group_id" value="<?php echo $row['final_group_id']; ?>">
                        <select name="mentor_erpid" required>
                            <option value="">Select Mentor</option>
                            <?php foreach($mentor_options as $mentor): ?>
                                <option value="<?php echo $mentor['faculty_id']; ?>">
                                    <?php echo $mentor['name'] . " (" . $mentor['faculty_id'] . ")"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn">
                            <i class="fas fa-check"></i>
                            Assign
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
