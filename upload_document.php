

<?php
include 'db.php';
session_start();

$erpid = $_SESSION['erpid'] ?? null;
$message = "";

// Fetch finalized group IDs the user belongs to
$finalized_groups = [];
if ($erpid) {
    $query = "
        SELECT DISTINCT pg.group_id AS group_id, pg.group_name
        FROM group_members gm
        JOIN project_groups pg ON gm.final_group_id = pg.group_id
        WHERE gm.member_erpid = '$erpid' AND gm.final_group_id IS NOT NULL
    ";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $finalized_groups[] = [
            'id' => $row['group_id'],
            'name' => $row['group_name']
        ];
    }
}

$doc_types = [
    'ppt' => 'PPT',
    'synopsis' => 'Synopsis',
    'report' => 'Report',
    'research paper' => 'Research Paper'
];

// Fetch user uploads status for each doc type per finalized group
$user_uploads = [];
if ($erpid) {
    $ids = array_map(fn($g) => $g['id'], $finalized_groups);
    if (count($ids) > 0) {
        $ids_str = implode(",", $ids);
        $upload_query = "
            SELECT final_group_id, file_type, status
            FROM group_uploads
            WHERE member_erpid = '$erpid' AND final_group_id IN ($ids_str)
        ";
        $upload_result = mysqli_query($conn, $upload_query);
        while ($row = mysqli_fetch_assoc($upload_result)) {
            $gid = $row['final_group_id'];
            $ftype = $row['file_type'];
            $status = $row['status'];
            if (!isset($user_uploads[$gid])) {
                $user_uploads[$gid] = [];
            }
            $user_uploads[$gid][$ftype] = $status; // could be pending, accepted, rejected
        }
    }
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_group_id'])) {
    $final_group_id = intval($_POST['final_group_id']);
    $file_type = $_POST['file_type'] ?? '';
    $file = $_FILES['file'] ?? null;
    $upload_dir = "C:/xampp4/htdocs/pms/uploads/";

    if ($file && $file['error'] === 0) {
        $safe_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($file['name']));
        $file_name = "group_" . $final_group_id . "_" . uniqid() . "_" . $safe_name;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $uploaded_at = date('Y-m-d H:i:s');
            $status = 'pending';
            $feedback = NULL;

            $stmt = mysqli_prepare($conn, "INSERT INTO group_uploads (final_group_id, member_erpid, file_type, file_path, uploaded_at, status, feedback) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issssss", $final_group_id, $erpid, $file_type, $file_path, $uploaded_at, $status, $feedback);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['upload_status'] = 'success';
        } else {
            $_SESSION['upload_status'] = 'failed';
        }
    } else {
        $_SESSION['upload_status'] = 'failed';
    }

    header("Location: upload_document.php");
    exit();
}

// Status Message
if (isset($_SESSION['upload_status'])) {
    $status = $_SESSION['upload_status'];
    $message = $status === 'success'
        ? "<div class='alert alert-success'>File uploaded successfully!</div>"
        : "<div class='alert alert-danger'>File upload failed!</div>";
    unset($_SESSION['upload_status']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Document for Finalized Group</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            min-height: 100vh;
        }
        .hero-header {
            background: url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80') center center/cover no-repeat;
            min-height: 220px;
            border-radius: 0 0 36px 36px;
            position: relative;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            margin-bottom: 40px;
            overflow: hidden;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(44, 62, 80, 0.68);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 60px 0 30px 0;
            color: #fff;
        }
        .hero-content h1 {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 2.4rem;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 0.2em;
        }
        .hero-content p {
            font-size: 1.13rem;
            color: #ffd700;
            margin-top: 10px;
            font-weight: 600;
        }
        .upload-container {
            max-width: 700px;
            margin: 80px auto 0 auto; /* Increased margin-top for more space */
            padding: 2.5rem 2rem 2rem 2rem;
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
            border-radius: 2rem;
            box-shadow: 0 12px 32px rgba(44,62,80,0.16), 0 2px 8px #b388ff44;
            animation: floatIn 1.1s cubic-bezier(.4,2,.6,1);
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(60px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .upload-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .upload-header i {
            font-size: 2.5rem;
            color: #8c52ff;
            margin-bottom: 1rem;
        }
        .form-label {
            font-weight: 600;
            color: #8c52ff;
        }
        .form-control, .form-select {
            border-radius: 0.7rem;
            padding: 0.75rem 1.25rem;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #8c52ff;
            box-shadow: 0 0 0 0.25rem rgba(140,82,255,0.15);
        }
        .custom-file-upload {
            border: 2px dashed #b388ff;
            border-radius: 0.7rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            background: #f3e7fa55;
            transition: all 0.3s ease;
        }
        .custom-file-upload:hover {
            border-color: #8c52ff;
            background-color: #f3e7fa99;
        }
        .upload-btn {
            background: linear-gradient(90deg, #8c52ff 60%, #43cea2 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 1.5rem;
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 16px #8c52ff22;
        }
        .upload-btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 24px #43cea255;
            background: linear-gradient(90deg, #43cea2 60%, #8c52ff 100%);
        }
        .alert-success {
            background: linear-gradient(90deg, #43cea222 0%, #185a9d22 100%);
            color: #185a9d;
        }
        .alert-danger {
            background: linear-gradient(90deg, #ff6b6b22 0%, #ff525222 100%);
            color: #cc0000;
        }
        .doc-icon {
            font-size: 1.3rem;
            margin-right: 8px;
            vertical-align: middle;
        }
        .ppt { color: #d24726; }
        .synopsis { color: #ffb300; }
        .report { color: #1976d2; }
        .research { color: #2e7d32; }
    </style>
</head>
<body>
    <div class="hero-header">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>
                <i class="fas fa-cloud-upload-alt"></i>
                Upload Document for Finalized Group
            </h1>
            <p>
                Share your project files with your group.<br>
                <span style="color:#ffd700;font-size:1.08em;">Accepted: PPT, Synopsis, Report, Research Paper</span>
            </p>
        </div>
    </div>

    <div class="upload-container">
    <?= $message ?>
    <?php if (count($finalized_groups) > 0): ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="final_group_id" class="form-label">Select Finalized Group</label>
                <select name="final_group_id" id="final_group_id" class="form-select" required>
                    <option value="" disabled selected>Select Group</option>
                    <?php foreach ($finalized_groups as $group): ?>
                        <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="file_type" class="form-label">Select Document Type</label>
                <select name="file_type" id="file_type" class="form-select" required>
                    <option value="" disabled selected>Select Document Type</option>
                    <?php
                    // Disable options for accepted or pending documents
                    foreach ($doc_types as $key => $label):
                        $status = null;
                        foreach ($finalized_groups as $group) {
                            $gid = $group['id'];
                            if (isset($user_uploads[$gid][$key])) {
                                $status = $user_uploads[$gid][$key];
                                break;
                            }
                        }
                        $disabled = ($status === 'accepted' || $status === 'pending');
                        ?>
                        <option value="<?= $key ?>" <?= $disabled ? 'disabled' : '' ?>>
                            <?= $label ?> <?= $disabled ? "(Already $status)" : "" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="file" class="form-label">Select File to Upload</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>

            <button type="submit" class="btn upload-btn">Upload Document</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            No finalized groups available. Please finalize your group before uploading documents.
        </div>
    <?php endif; ?>
</div>
</body>
</html>
