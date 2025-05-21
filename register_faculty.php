<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload Faculty - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome for icons (optional) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e3e6fd 0%, #f8fafd 100%);
            min-height: 100vh;
            margin: 0;
        }
        .hero-header {
            position: relative;
            background: url('https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            border-radius: 0 0 28px 28px;
            box-shadow: 0 8px 32px rgba(63,81,181,0.10);
            margin-bottom: 32px;
            min-height: 130px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(63, 81, 181, 0.75);
            border-radius: 0 0 28px 28px;
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            color: #fff;
            text-align: center;
            width: 100%;
        }
        .hero-content h1 {
            font-size: 2.1rem;
            font-weight: 800;
            margin-bottom: 0;
            letter-spacing: 1.2px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .hero-content .subtitle {
            font-size: 1.08rem;
            font-style: italic;
            color: #e3e6fd;
        }
        .upload-container {
            max-width: 420px;
            margin: 0 auto 40px auto;
            background: #fff;
            padding: 34px 28px 28px 28px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(63,81,181,0.12);
            text-align: center;
            animation: fadeInUp 0.8s;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .upload-container h2 {
            color: #3f51b5;
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .upload-container form {
            margin-bottom: 18px;
        }
        input[type="file"] {
            margin-top: 16px;
            padding: 8px 4px;
            border: 1px solid #c5cae9;
            border-radius: 7px;
            background: #f5f7fd;
            width: 100%;
            font-size: 1rem;
            transition: border 0.2s;
        }
        input[type="file"]:focus {
            border: 1.5px solid #3f51b5;
            outline: none;
        }
        button {
            margin-top: 22px;
            padding: 11px 28px;
            background: linear-gradient(90deg, #3f51b5 60%, #5c6bc0 100%);
            color: white;
            border: none;
            border-radius: 999px;
            font-weight: 600;
            font-size: 1.09rem;
            cursor: pointer;
            box-shadow: 0 4px 16px #3f51b522;
            transition: background 0.18s, transform 0.16s;
        }
        button:hover, button:focus {
            background: linear-gradient(90deg, #283593 60%, #5c6bc0 100%);
            transform: scale(1.03) translateY(-2px);
        }
        .image-box {
            margin-top: 22px;
        }
        .image-box img {
            width: 220px;
            max-width: 100%;
            border-radius: 11px;
            box-shadow: 0 4px 14px #3f51b510;
        }
        @media (max-width: 600px) {
            .hero-header { min-height: 80px; border-radius: 0 0 12px 12px;}
            .hero-content h1 { font-size: 1.2rem;}
            .upload-container { padding: 18px 6px 14px 6px; border-radius: 11px;}
        }
    </style>
</head>
<body>
    <div class="hero-header">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>
                <i class="fas fa-chalkboard-teacher"></i> Upload Faculty via Excel
            </h1>
            <div class="subtitle">
                Register multiple faculty members at once by uploading an Excel file.
            </div>
        </div>
    </div>
    <div class="upload-container">
        <h2>📥 Upload Excel to Register Faculty</h2>
        <form action="upload_faculty.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="excel_file" accept=".xlsx" required><br>
            <button type="submit"><i class="fas fa-cloud-upload-alt"></i> Upload & Register</button>
        </form>
        <div class="image-box">
            <img src="images/faculty.png" alt="Faculty Upload">
        </div>
    </div>
</body>
</html>
