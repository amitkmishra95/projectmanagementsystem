<?php
session_start();
include 'db.php';

if (!isset($_SESSION['erpid'])) {
    header('Location: user_login.php');
    exit;
}

$erpid = $_SESSION['erpid'];
$message = "";

// Fetch user data
$result = mysqli_query($conn, "SELECT * FROM users WHERE erpid = '$erpid'");
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $batch_start = mysqli_real_escape_string($conn, $_POST['batch_start']);
        $batch_end = mysqli_real_escape_string($conn, $_POST['batch_end']);
        $branch = mysqli_real_escape_string($conn, $_POST['branch']);
        $section = mysqli_real_escape_string($conn, $_POST['section']);

        $update = mysqli_query($conn, "UPDATE users SET name='$name', contact='$contact', batch_start='$batch_start', batch_end='$batch_end', branch='$branch', section='$section' WHERE erpid='$erpid'");
        $message = $update ? "Profile updated successfully." : "Error updating profile.";
    }

    if (isset($_POST['change_password'])) {
        $current = mysqli_real_escape_string($conn, $_POST['current_password']);
        $new = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        $user_result = mysqli_query($conn, "SELECT password FROM users WHERE erpid = '$erpid'");
        $user_row = mysqli_fetch_assoc($user_result);
        $stored_password = $user_row['password'];

        if ($new !== $confirm) {
            $message = "New passwords do not match.";
        } elseif ($current !== $stored_password) {
            $message = "Current password is incorrect.";
        } else {
            mysqli_query($conn, "UPDATE users SET password = '$new' WHERE erpid = '$erpid'");
            $message = "Password changed successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Open Sans', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
        }
        .profile-header {
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80') center center/cover no-repeat;
            min-height: 240px;
            border-radius: 0 0 36px 36px;
            position: relative;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            margin-bottom: 40px;
        }
        .profile-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 41, 59, 0.7);
            z-index: 1;
            border-radius: 0 0 36px 36px;
        }
        .profile-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 56px 0 28px 0;
            color: #fff;
        }
        .profile-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 2px 16px #8c52ff55;
            margin-bottom: 12px;
            background: #fff;
        }
        .profile-name {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: 1px;
            color: #ffd700;
        }
        .profile-id {
            font-size: 1.1rem;
            color: #e4e8ef;
            font-weight: 600;
        }
        .profile-card {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.12);
            color: #232526;
            border: none;
            margin-bottom: 32px;
        }
        .profile-section-title {
            font-family: 'Montserrat', Arial, sans-serif;
            color: #4f8cff;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 18px;
        }
        .form-label {
            font-weight: 600;
            color: #4f8cff;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 1.5px solid #bdbdbd;
            font-size: 1.07rem;
        }
        .btn-primary {
            background: linear-gradient(90deg, #4f8cff 60%, #8c52ff 100%);
            border: none;
            border-radius: 999px;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 12px 38px;
            box-shadow: 0 4px 16px #8c52ff22;
            transition: background 0.2s, box-shadow 0.2s, transform 0.18s;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(90deg, #8c52ff 60%, #4f8cff 100%);
            box-shadow: 0 8px 24px #4f8cff44;
            transform: translateY(-2px) scale(1.03);
        }
        .btn-warning {
            border-radius: 999px;
            font-weight: 700;
        }
        .alert-info {
            background: linear-gradient(90deg, #f7971e33 0%, #ffd20033 100%);
            color: #8c52ff;
            border-radius: 12px;
            font-weight: 600;
            border: none;
        }
        .icon-label {
            color: #8c52ff;
            margin-right: 8px;
        }
        hr {
            border-top: 2px solid #8c52ff44;
        }
        @media (max-width: 600px) {
            .profile-header {
                min-height: 130px;
                border-radius: 0 0 18px 18px;
            }
            .profile-avatar {
                width: 70px;
                height: 70px;
            }
            .profile-name {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Profile Header with Avatar -->
    <div class="profile-header mb-4">
        <div class="profile-overlay"></div>
        <div class="profile-content">
            <img class="profile-avatar"
                 src="https://api.dicebear.com/7.x/thumbs/svg?seed=<?= urlencode($user['name']) ?>"
                 alt="Profile Avatar">
            <div class="profile-name"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($user['name']) ?></div>
            <div class="profile-id"><i class="fas fa-id-card"></i> <?= htmlspecialchars($user['erpid']) ?></div>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <!-- Profile Info and Update Section -->
        <div class="profile-card p-4 mb-4">
            <h3 class="profile-section-title"><i class="fas fa-user-edit"></i> Edit Profile</h3>
            <form method="POST">
                <input type="hidden" name="update_profile" value="1">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-key icon-label"></i>ERP ID</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['erpid']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-envelope icon-label"></i>Email ID</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user icon-label"></i>Full Name</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-phone icon-label"></i>Contact Number</label>
                    <input type="text" class="form-control" name="contact" value="<?= htmlspecialchars($user['contact']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-calendar-plus icon-label"></i>Batch Start Year</label>
                        <input type="number" class="form-control" name="batch_start" value="<?= htmlspecialchars($user['batch_start']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-calendar-check icon-label"></i>Batch End Year</label>
                        <input type="number" class="form-control" name="batch_end" value="<?= htmlspecialchars($user['batch_end']) ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-code-branch icon-label"></i>Branch</label>
                        <input type="text" class="form-control" name="branch" value="<?= htmlspecialchars($user['branch']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-users icon-label"></i>Section</label>
                        <input type="text" class="form-control" name="section" value="<?= htmlspecialchars($user['section']) ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-save"></i> Update Profile</button>
            </form>
        </div>

        <hr class="my-4">

        <!-- Password Change Section -->
        <div class="profile-card p-4 mb-4">
            <h4 class="profile-section-title"><i class="fas fa-key"></i> Change Password</h4>
            <form method="POST">
                <input type="hidden" name="change_password" value="1">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-lock icon-label"></i>Current Password</label>
                    <input type="password" class="form-control" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-unlock icon-label"></i>New Password</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-unlock-alt icon-label"></i>Confirm New Password</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-warning mt-2"><i class="fas fa-key"></i> Change Password</button>
            </form>
        </div>
    </div>
</body>
</html>
