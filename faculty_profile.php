<?php
session_start();
include 'db.php';

$faculty_id = $_SESSION['faculty_id'];
$sql = "SELECT * FROM faculty WHERE faculty_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $designation = $_POST['designation'];
    $branch = $_POST['branch'];

    $update_sql = "UPDATE faculty SET name=?, contact=?, designation=?, branch=? WHERE faculty_id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssss", $name, $contact, $designation, $branch, $faculty_id);
    $update_stmt->execute();
    echo "<script>alert('Profile updated successfully'); window.location='faculty_profile.php';</script>";
}

// Password update
if (isset($_POST['change_password'])) {
    $newpass = $_POST['new_password'];
    $confirmpass = $_POST['confirm_password'];
    if ($newpass === $confirmpass) {
        $hashed =$newpass ;// password_hash($newpass, PASSWORD_BCRYPT);
        $conn->query("UPDATE faculty SET password='$hashed' WHERE faculty_id='$faculty_id'");
        echo "<script>alert('Password changed successfully');</script>";
    } else {
        echo "<script>alert('Passwords do not match');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Faculty Profile</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
<style>
  body {
    font-family: 'Poppins', Arial, sans-serif;
    background: linear-gradient(135deg, #e0ecff 0%, #e0f7fa 100%);
    margin: 0;
    padding: 0;
    min-height: 100vh;
    color: #1e293b;
  }
  .container {
    max-width: 450px;
    margin: 60px auto;
    background: rgba(255,255,255,0.98);
    padding: 36px 32px;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(59,130,246,0.1);
    backdrop-filter: blur(8px);
    position: relative;
    animation: floatIn 1.1s cubic-bezier(.4,2,.6,1);
  }
  @keyframes floatIn {
    from {opacity: 0; transform: translateY(40px);}
    to {opacity: 1; transform: translateY(0);}
  }
  .back-link {
    position: absolute;
    top: 20px;
    left: 24px;
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.3s;
  }
  .back-link:hover {
    color: #f857a6;
  }
  .profile-illustration {
    display: block;
    margin: 0 auto 16px auto;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #e0ecff;
    box-shadow: 0 4px 16px #bae6fd;
    animation: floatY 3s ease-in-out infinite;
    object-fit: cover;
  }
  @keyframes floatY {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
  }
  h2 {
    text-align: center;
    color: #2563eb;
    margin-bottom: 20px;
    font-weight: 700;
    letter-spacing: 1px;
    font-size: 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
  }
  h2 i {
    color: #60a5fa;
  }
  h3 {
    color: #f857a6;
    font-weight: 600;
    margin-top: 40px;
    margin-bottom: 12px;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  form {
    margin-top: 10px;
  }
  label {
    display: block;
    margin-top: 14px;
    color: #2563eb;
    font-weight: 600;
  }
  input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border: 2px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    background: #f8fafc;
    transition: border-color 0.3s;
  }
  input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
    border-color: #2563eb;
    outline: none;
    box-shadow: 0 0 6px #60a5fa88;
  }
  input[readonly] {
    background: #e0ecff;
    color: #64748b;
    cursor: not-allowed;
  }
  button {
    margin-top: 24px;
    width: 100%;
    padding: 12px 0;
    background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%);
    border: none;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
  }
  button:hover {
    background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
    transform: translateY(-3px) scale(1.03);
  }
  button i {
    font-size: 1.2rem;
  }
  @media (max-width: 600px) {
    .container {
      width: 95vw;
      padding: 24px 20px;
    }
    h2 {
      font-size: 1.6rem;
    }
    h3 {
      font-size: 1rem;
      margin-top: 30px;
    }
  }
</style>
</head>
<body>
<div class="container">
    <a href="faculty_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Faculty Illustration" class="profile-illustration" />
    <h2><i class="fas fa-user-tie"></i> Faculty Profile</h2>

    <form method="post">
        <label><i class="fas fa-envelope"></i> Email</label>
        <input type="email" value="<?= htmlspecialchars($faculty['email']) ?>" readonly>

        <label><i class="fas fa-id-card"></i> Faculty ID</label>
        <input type="text" value="<?= htmlspecialchars($faculty['faculty_id']) ?>" readonly>

        <label><i class="fas fa-user"></i> Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($faculty['name']) ?>" required>

        <label><i class="fas fa-phone"></i> Contact</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($faculty['contact']) ?>" required>

        <label><i class="fas fa-briefcase"></i> Designation</label>
        <input type="text" name="designation" value="<?= htmlspecialchars($faculty['designation']) ?>" required>

        <label><i class="fas fa-code-branch"></i> Branch</label>
        <input type="text" name="branch" value="<?= htmlspecialchars($faculty['branch']) ?>" required>

        <button type="submit" name="update_profile"><i class="fas fa-save"></i> Update Profile</button>
    </form>

    <h3><i class="fas fa-lock"></i> Change Password</h3>
    <form method="post">
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" name="change_password"><i class="fas fa-key"></i> Change Password</button>
    </form>
</div>
</body>
</html>
