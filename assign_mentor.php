<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    die("Access denied.");
}

if (!isset($_GET['group_id'])) {
    die("Group ID not provided.");
}

$group_id = $_GET['group_id'];

// Fetch all faculty members
$faculty_result = $conn->query("SELECT faculty_id, name, designation FROM faculty");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['faculty_id'])) {
    $faculty_id = $_POST['faculty_id'];

    $stmt = $conn->prepare("UPDATE project_groups SET faculty_id = ? WHERE group_id = ?");
    $stmt->bind_param("ss", $faculty_id, $group_id);
    $stmt->execute();

    header("Location: assign_final_group.php"); // Redirect back to group page
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Mentor</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0fff0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 102, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #006600;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            border: 1px solid #aaa;
        }

        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .back {
            text-align: center;
            margin-top: 15px;
        }

        .back a {
            color: #007bff;
            text-decoration: none;
        }

        .back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🧑‍🏫 Assign Mentor to Group: <?php echo htmlspecialchars($group_id); ?></h2>

    <form method="POST">
        <label for="faculty_id">Select Mentor</label>
        <select name="faculty_id" required>
            <option value="">-- Select Mentor --</option>
            <?php while ($row = $faculty_result->fetch_assoc()): ?>
                <option value="<?php echo $row['faculty_id']; ?>">
                    <?php echo htmlspecialchars($row['name'] . " - " . $row['designation']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Assign Mentor</button>
    </form>

    <div class="back">
        <a href="assign_final_group.php">← Back to Group Assignment</a>
    </div>
</div>

</body>
</html>
