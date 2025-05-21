<?php
session_start();
include 'db.php';

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admin WHERE faculty_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $designation = $_POST['designation'];

    $update_sql = "UPDATE admin SET name=?, contact=?, designation=? WHERE faculty_id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssss", $name, $contact, $designation, $admin_id);
    $update_stmt->execute();
    echo "<script>alert('Profile updated successfully'); window.location='admin_profile.php';</script>";
}

// Password update
if (isset($_POST['change_password'])) {
    $newpass = $_POST['new_password'];
    $confirmpass = $_POST['confirm_password'];
    if ($newpass === $confirmpass) {
        // Ideally here store hashed password, just following original logic
        $conn->query("UPDATE admin SET password='$newpass' WHERE faculty_id='$admin_id'");
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
<title>Admin Profile</title>
<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
    * {
        box-sizing: border-box;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #5661f9 0%, #53e3a6 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: fadeInBody 1s ease forwards;
        color: #333;
    }
    @keyframes fadeInBody {
        from {opacity: 0;}
        to {opacity: 1;}
    }
    .container {
        background: #fff;
        max-width: 430px;
        width: 90%;
        padding: 30px 40px 40px 40px;
        border-radius: 16px;
        box-shadow: 0 12px 24px rgb(0 0 0 / 0.15);
        position: relative;
        overflow: hidden;
    }
    .container::before,
    .container::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        filter: blur(70px);
        opacity: 0.25;
        z-index: 0;
    }
    .container::before {
        top: -80px;
        left: -80px;
        width: 200px;
        height: 200px;
        background: #5661f9;
        animation: pulse 6s ease-in-out infinite;
    }
    .container::after {
        bottom: -80px;
        right: -80px;
        width: 180px;
        height: 180px;
        background: #53e3a6;
        animation: pulse 5s ease-in-out infinite alternate;
    }
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.25;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.35;
        }
    }
    h2 {
        text-align: center;
        color: #5661f9;
        margin-bottom: 25px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.1px;
        z-index: 2;
        position: relative;
    }
    .icon {
        font-size: 60px;
        color: #53e3a6;
        display: block;
        margin: 0 auto 16px auto;
        animation: bounce 2s infinite;
        position: relative;
        z-index: 2;
    }
    @keyframes bounce {
        0%, 100% {transform: translateY(0);}
        50% {transform: translateY(-10px);}
    }
    form {
        margin-bottom: 30px;
        position: relative;
        z-index: 2;
    }
    label {
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        color: #444;
        user-select: none;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 18px;
        border-radius: 8px;
        font-size: 15px;
        border: 2px solid #ccd6f6;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        outline-offset: 0;
        box-shadow: inset 0 0 4px rgba(0,0,0,0.05);
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
        border-color: #5661f9;
        box-shadow: 0 0 8px #5661f9aa;
    }
    input[readonly] {
        background-color: #f0f2ff;
        border-color: #aab6ff;
        color: #888;
        cursor: not-allowed;
        user-select: text;
    }
    button {
        width: 100%;
        background: #5661f9;
        color: #fff;
        padding: 14px 0;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 6px 12px #5661f966;
        transition: background-color 0.3s ease, transform 0.2s ease;
        user-select: none;
    }
    button:hover {
        background: #4255d4;
        transform: scale(1.03);
    }
    button:active {
        transform: scale(0.97);
    }
    h3 {
        margin-bottom: 18px;
        text-align: center;
        font-weight: 700;
        color: #53e3a6;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        z-index: 2;
    }
    /* Responsive */
    @media (max-width: 480px) {
        .container {
            padding: 25px 20px 30px 20px;
        }
        .icon {
            font-size: 48px;
        }
    }
    /* Added for quote and symbol */
    .admin-top-symbol {
        text-align: center;
        margin-bottom: 18px;
        z-index: 2;
        position: relative;
    }
    .admin-top-symbol .fa-user-shield {
        font-size: 3rem;
        color: #5661f9;
        margin-bottom: 10px;
        animation: bounce 2s infinite;
    }
    .admin-top-symbol .admin-quote {
        font-size: 1.1rem;
        color: #53e3a6;
        font-style: italic;
        margin-top: 8px;
    }
</style>
</head>
<body>
<div class="container" role="main" aria-label="Admin Profile">
    <!-- Top symbol and quote START -->
    <div class="admin-top-symbol">
        <i class="fas fa-user-shield"></i>
        <div class="admin-quote">
            <i class="fas fa-quote-left"></i>
            With great power comes great responsibility
            <i class="fas fa-quote-right"></i>
        </div>
    </div>
    <!-- Top symbol and quote END -->

    <i class="fas fa-user-circle icon" aria-hidden="true"></i>
    <h2>Admin Profile</h2>

    <form method="post" aria-label="Update admin profile form">
        <label for="email">Email</label>
        <input type="email" id="email" value="<?= htmlspecialchars($admin['email']) ?>" readonly aria-readonly="true">

        <label for="facultyid">Faculty ID</label>
        <input type="text" id="facultyid" value="<?= htmlspecialchars($admin['faculty_Id']) ?>" readonly aria-readonly="true">

        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>

        <label for="contact">Contact</label>
        <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($admin['contact']) ?>" required>

        <label for="designation">Designation</label>
        <input type="text" id="designation" name="designation" value="<?= htmlspecialchars($admin['designation']) ?>" required>

        <button type="submit" name="update_profile" aria-label="Update profile">Update Profile</button>
    </form>

    <h3>Change Password</h3>
    <form method="post" aria-label="Change password form">
        <input type="password" name="new_password" placeholder="New Password" required aria-required="true" autocomplete="new-password">
        <input type="password" name="confirm_password" placeholder="Confirm Password" required aria-required="true" autocomplete="new-password">
        <button type="submit" name="change_password" aria-label="Change password">Change Password</button>
    </form>
</div>
</body>
</html>
