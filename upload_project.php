<?php
include 'db.php';

$feedback = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $student_name = $_POST['student_name'];
    $erpid = $_POST['erpid'];
    $rollno = $_POST['rollno'];
    $semester = $_POST['semester'];
    $session = $_POST['session'];

    $upload_dir = "E:/files/";
    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $upload_dir . time() . "_" . $file_name;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, file_path, student_name, uploaded_at, erpid, rollno, semester, session) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $title, $description, $target_file, $student_name, $erpid, $rollno, $semester, $session);
        $stmt->execute();
        $feedback = "<div class='alert alert-success mt-3'><i class='fas fa-check-circle'></i> Project uploaded successfully!</div>";
    } else {
        $feedback = "<div class='alert alert-danger mt-3'><i class='fas fa-exclamation-triangle'></i> File upload failed!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Project</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Open Sans', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
        }
        .hero-section {
            background: url('https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80') center center/cover no-repeat;
            min-height: 240px;
            border-radius: 0 0 36px 36px;
            position: relative;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            margin-bottom: 40px;
        }
        .hero-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 41, 59, 0.7);
            z-index: 1;
            border-radius: 0 0 36px 36px;
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
        }
        .hero-content p {
            font-size: 1.13rem;
            color: #ffd700;
            margin-top: 10px;
            font-weight: 600;
        }
        .upload-card {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.12);
            color: #232526;
            border: none;
            margin-bottom: 32px;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }
        .upload-title {
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
        .icon-label {
            color: #8c52ff;
            margin-right: 8px;
        }
        @media (max-width: 600px) {
            .hero-section {
                min-height: 130px;
                border-radius: 0 0 18px 18px;
            }
            .upload-card {
                border-radius: 12px;
                padding: 1.2rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section mb-4">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><i class="fas fa-upload"></i> Upload Project</h1>
            <p>Share your work and let your ideas shine!</p>
        </div>
    </div>
    <div class="container">
        <div class="upload-card p-4 mb-4">
            <h3 class="upload-title"><i class="fas fa-file-alt"></i> Project Submission Form</h3>
            <form method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-heading icon-label"></i>Project Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Project Title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-align-left icon-label"></i>Project Description</label>
                    <textarea name="description" class="form-control" placeholder="Project Description" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user icon-label"></i>Student Name</label>
                    <input type="text" name="student_name" class="form-control" placeholder="Student Name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-id-card icon-label"></i>ERP ID</label>
                    <input type="text" name="erpid" class="form-control" placeholder="ERP ID" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-list-ol icon-label"></i>Roll Number</label>
                    <input type="text" name="rollno" class="form-control" placeholder="Roll Number" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-graduation-cap icon-label"></i>Semester</label>
                    <input type="text" name="semester" class="form-control" placeholder="Semester" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-calendar-alt icon-label"></i>Session</label>
                    <input type="text" name="session" class="form-control" placeholder="Session (e.g., 2024-2025)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-paperclip icon-label"></i>Project File</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-upload"></i> Upload Project</button>
            </form>
            <?= $feedback ?>
        </div>
    </div>
</body>
</html>
