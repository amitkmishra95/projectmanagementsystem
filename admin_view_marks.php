<?php
session_start();
include 'db.php';

$filterSemester = $_GET['semester'] ?? '';
$filterBranch = $_GET['branch'] ?? '';
$filterSection = $_GET['section'] ?? '';
$searchErpid = $_GET['erpid'] ?? '';

$query = "
    SELECT DISTINCT m.erpid, u.name, u.branch, u.section, u.semester, m.marks
    FROM marks m
    JOIN users u ON m.erpid = u.erpid
    WHERE 1
";

$params = [];
if (!empty($searchErpid)) {
    $query .= " AND m.erpid LIKE ?";
    $params[] = "%$searchErpid%";
}
if (!empty($filterSemester)) {
    $query .= " AND u.semester = ?";
    $params[] = $filterSemester;
}
if (!empty($filterBranch)) {
    $query .= " AND LOWER(u.branch) = ?";
    $params[] = strtolower($filterBranch);
}
if (!empty($filterSection)) {
    $query .= " AND u.section = ?";
    $params[] = $filterSection;
}

$stmt = $conn->prepare($query);
if ($params) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Assigned Marks - Admin View</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #e0ecff 0%, #e0f7fa 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            color: #232b45;
        }
        .header {
            background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
            color: #fff;
            padding: 36px 0 24px 0;
            text-align: center;
            border-radius: 0 0 36px 36px;
            box-shadow: 0 4px 24px rgba(37,99,235,0.09);
            animation: fadeInDown 1.1s;
        }
        .header i {
            font-size: 2.4rem;
            margin-bottom: 8px;
            color: #bae6fd;
        }
        .header h2 {
            margin: 8px 0 0 0;
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .header img {
            width: 75px;
            margin-top: 16px;
            animation: floatY 3s ease-in-out infinite;
        }
        @keyframes floatY {
            0%,100% { transform: translateY(0);}
            50% { transform: translateY(-10px);}
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .container {
            max-width: 1100px;
            margin: 40px auto 0 auto;
            background: rgba(255,255,255,0.98);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(59,130,246,0.10);
            padding: 38px 24px 32px 24px;
            backdrop-filter: blur(8px);
            animation: floatIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        form {
            margin-bottom: 32px;
            display: flex;
            flex-wrap: wrap;
            gap: 18px 24px;
            align-items: center;
            justify-content: flex-start;
            animation: fadeIn 1s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        label {
            font-weight: 600;
            color: #2563eb;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        select, input[type="text"] {
            font-size: 1rem;
            padding: 7px 12px;
            border-radius: 8px;
            border: 2px solid #e0ecff;
            background: #f8fafc;
            transition: border-color 0.2s;
            min-width: 90px;
        }
        select:focus, input[type="text"]:focus {
            border-color: #2563eb;
            outline: none;
        }
        button[type="submit"], .reset-link {
            background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 22px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            margin-left: 8px;
            box-shadow: 0 2px 8px #2563eb22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.18s;
            text-decoration: none;
            display: inline-block;
        }
        button[type="submit"]:hover, .reset-link:hover {
            background: linear-gradient(90deg, #38bdf8 60%, #2563eb 100%);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 24px #38bdf833;
            color: #fff;
        }
        .reset-link {
            margin-left: 0;
            margin-right: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: transparent;
            animation: fadeInUp 1.2s;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        th {
            background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
            color: #fff;
            padding: 13px;
            font-weight: 600;
            text-align: center;
            border: none;
            font-size: 1.05rem;
        }
        td {
            padding: 13px;
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
            font-size: 1.01rem;
            text-align: center;
        }
        tr:hover td {
            background: #e0f2fe;
        }
        .no-data {
            text-align: center;
            color: #888;
            font-size: 1.13rem;
            padding: 22px 0;
        }
        @media (max-width: 900px) {
            .container { padding: 12px 2vw; }
            .header h2 { font-size: 1.4rem; }
            th, td { padding: 8px 4px; font-size: 0.97rem; }
            form { gap: 10px 8px; }
        }
        @media (max-width: 600px) {
            .container { padding: 8px 1vw; }
            .header { padding: 22px 0 12px 0; }
            .header h2 { font-size: 1.05rem; }
            th, td { font-size: 0.95rem; }
            form { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="header">
        <i class="fas fa-chart-bar"></i>
        <h2>Students with Assigned Marks</h2>
        <img src="https://cdni.iconscout.com/illustration/premium/thumb/marks-report-3763622-3147697.png" alt="Marks Illustration">
    </div>
<a href="admin_dashboard.php" class="reset-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <div class="container">
        <form method="GET" action="">
            <?php $roman_semesters = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII']; ?>
            <label><i class="fas fa-graduation-cap"></i> Semester:
                <select name="semester">
                    <option value="">All</option>
                    <?php foreach ($roman_semesters as $sem): ?>
                        <option value="<?= $sem ?>" <?= ($filterSemester == $sem) ? 'selected' : '' ?>><?= $sem ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label><i class="fas fa-code-branch"></i> Branch:
                <select name="branch">
                    <option value="">All</option>
                    <option value="cse" <?= ($filterBranch == "cse") ? 'selected' : '' ?>>CSE</option>
                    <option value="ece" <?= ($filterBranch == "ece") ? 'selected' : '' ?>>ECE</option>
                    <option value="eee" <?= ($filterBranch == "eee") ? 'selected' : '' ?>>EEE</option>
                    <option value="me" <?= ($filterBranch == "me") ? 'selected' : '' ?>>ME</option>
                </select>
            </label>

            <label><i class="fas fa-layer-group"></i> Section:
                <select name="section">
                    <option value="">All</option>
                    <option value="A" <?= ($filterSection == "A") ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= ($filterSection == "B") ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= ($filterSection == "C") ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= ($filterSection == "D") ? 'selected' : '' ?>>D</option>
                    <option value="E" <?= ($filterSection == "E") ? 'selected' : '' ?>>E</option>
                </select>
            </label>

            <label><i class="fas fa-id-card"></i> ERP ID:
                <input type="text" name="erpid" value="<?= htmlspecialchars($searchErpid) ?>">
            </label>

            <button type="submit"><i class="fas fa-filter"></i> Filter</button>
            <a href="admin_view_marks.php" class="reset-link"><i class="fas fa-undo"></i> Reset</a>
        </form>

        <table>
    <tr>
        <th>ERP ID</th>
        <th>Name</th>
        <th>Branch</th>
        <th>Section</th>
        <th>Semester</th>
        <th>Marks</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['erpid']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars(strtoupper($row['branch'])) ?></td>
                <td><?= htmlspecialchars($row['section']) ?></td>
                <td><?= htmlspecialchars($row['semester']) ?></td>
                <td><?= htmlspecialchars($row['marks']) ?></td> <!-- Showing marks -->
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6" class="no-data">No data found.</td></tr>
    <?php endif; ?>
</table>

    </div>
</body>
</html>
