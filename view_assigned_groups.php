<?php
session_start();
include "db.php";
if (!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php");
    exit();
}
$faculty_id = $_SESSION['faculty_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Assigned Groups</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', 'Segoe UI', sans-serif;
      background: linear-gradient(120deg, #e0ecff 0%, #ffe0e7 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
      animation: bgMove 16s ease-in-out infinite alternate;
    }
    @keyframes bgMove {
      0% { background-position: 0% 50%; }
      100% { background-position: 100% 50%; }
    }
    .hero-header {
      background: #fff;
      border-radius: 0 0 36px 36px;
      box-shadow: 0 4px 24px rgba(59,130,246,0.08);
      min-height: 120px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      margin-bottom: 32px;
      animation: fadeIn 1.1s;
    }
    .hero-title {
      font-size: 2rem;
      font-weight: 900;
      letter-spacing: 1px;
      color: #2563eb;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 14px;
      animation: slideInDown 1s;
    }
    .hero-subtitle {
      font-size: 1.08rem;
      color: #f857a6;
      font-style: italic;
      animation: fadeInUp 1.2s;
    }
    @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
    @keyframes slideInDown { from {opacity:0; transform: translateY(-30px);} to {opacity:1; transform: translateY(0);} }
    @keyframes fadeInUp { from {opacity:0; transform: translateY(22px);} to {opacity:1; transform: translateY(0);} }
    .groups-container {
      max-width: 800px;
      margin: 40px auto 0 auto;
      padding: 0 12px;
    }
    .group-card {
      background: rgba(255,255,255,0.97);
      margin: 32px 0 0 0;
      padding: 28px 28px 18px 28px;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(59,130,246,0.08), 0 1.5px 8px #f857a622;
      animation: floatIn 1.1s cubic-bezier(.4,2,.6,1);
      backdrop-filter: blur(6px);
      transition: box-shadow 0.2s, transform 0.2s;
    }
    .group-card:hover {
      box-shadow: 0 12px 40px #2563eb22;
      transform: translateY(-4px) scale(1.01);
    }
    .group-title {
      color: #2563eb;
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      letter-spacing: 0.3px;
    }
    .members-list {
      list-style: none;
      padding-left: 0;
      margin: 0;
      margin-top: 8px;
      animation: fadeInUp 1.2s;
    }
    .members-list li {
      margin-bottom: 10px;
      color: #444;
      font-size: 1.06rem;
      display: flex;
      align-items: center;
      gap: 9px;
      animation: fadeInUp 0.7s;
    }
    .members-list li i {
      color: #f857a6;
      font-size: 1.1rem;
      min-width: 18px;
    }
    .document-status {
      margin-top: 16px;
      padding-top: 10px;
      border-top: 1px solid #ddd;
    }
    .document-status h4 {
      font-size: 1.1rem;
      color: #2563eb;
      margin-bottom: 10px;
    }
    .document-status ul {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }
    .document-status li {
      font-size: 1rem;
      color: #333;
      margin-bottom: 6px;
      display: flex;
      justify-content: space-between;
      border-bottom: 1px dashed #ccc;
      padding-bottom: 4px;
    }
    .status {
      font-weight: 600;
    }
    .status.pending { color: #f59e0b; }
    .status.approved { color: #16a34a; }
    .status.rejected { color: #dc2626; }
    .status.not-uploaded { color: #6b7280; }
    .no-groups {
      text-align: center;
      color: #888;
      font-size: 1.15rem;
      margin-top: 60px;
      padding: 30px;
      background: rgba(255,255,255,0.93);
      border-radius: 18px;
      box-shadow: 0 4px 18px #2563eb11;
      animation: fadeIn 1.3s;
    }
    @media (max-width: 600px) {
      .groups-container { padding: 0 2vw; }
      .group-card { padding: 16px 8px 10px 8px; }
      .group-title { font-size: 1.05rem; }
    }
  </style>
</head>
<body>

<div class="hero-header">
  <div class="hero-title">
    <i class="fas fa-users"></i>
    Finalized Groups Under Your Mentorship
  </div>
  <div class="hero-subtitle">
    Here are the groups and members assigned to you as a mentor.
  </div>
</div>

<div class="groups-container">
<?php
$result = $conn->query("SELECT final_group_id FROM project_groups WHERE mentor_erpid='$faculty_id'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $final_group_id = $row['final_group_id'];
        echo "<div class='group-card'>";
        echo "<div class='group-title'><i class='fas fa-layer-group'></i> Final Group ID: $final_group_id</div>";
        
        $members = $conn->query("SELECT member_erpid FROM group_members WHERE final_group_id='$final_group_id'");
        echo "<ul class='members-list'>";
        while ($m = $members->fetch_assoc()) {
            $erp = $m['member_erpid'];
            $user_query = $conn->query("SELECT name FROM users WHERE erpid='$erp' LIMIT 1");
            $user = $user_query->fetch_assoc();
            $name = $user ? $user['name'] : 'Unknown';
            echo "<li><i class='fas fa-user'></i> $name <span style='color:#bbb'>(ERP ID: $erp)</span></li>";
        }
        echo "</ul>";

        // Document Upload Status Section
        echo "<div class='document-status'><h4><i class='fas fa-file-alt'></i> Document Upload Status:</h4><ul>";
        $doc_types = ['synopsis', 'report', 'ppt', 'research_paper'];
        foreach ($doc_types as $type) {
            $doc = $conn->query("SELECT status FROM group_uploads WHERE final_group_id='$final_group_id' AND file_type='$type' LIMIT 1");
            if ($doc->num_rows > 0) {
                $status = $doc->fetch_assoc()['status'];
                $status_class = strtolower($status);
            } else {
                $status = "Not Uploaded";
                $status_class = "not-uploaded";
            }
            $label = ucfirst(str_replace('_', ' ', $type));
            echo "<li>$label <span class='status $status_class'>$status</span></li>";
        }
        echo "</ul></div>"; // end document-status

        echo "</div>"; // end group-card
    }
} else {
    echo "<div class='no-groups'><i class='fas fa-info-circle'></i> No groups assigned to you yet.</div>";
}
?>
</div>

</body>
</html>
