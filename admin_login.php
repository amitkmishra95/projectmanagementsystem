<?php session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['admin_id'] = $row['faculty_Id'];
            $_SESSION['admin_name'] = $row['name'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid password'); window.location.href='admin_login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email'); window.location.href='admin_login.php';</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login - Student Project Management</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0; padding: 0;
            min-height: 100vh;
            font-family: 'Roboto Mono', monospace;
            background: linear-gradient(120deg, #f8fafc 0%, #e3e6fd 100%);
            color: #1a1a1a;
        }
        .login-wrapper {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            padding: 20px;
        }
        .login-illustration {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(120deg, #e3e6fd 60%, #f8fafc 100%);
            min-height: 100vh;
            border-radius: 0 22px 22px 0;
            box-shadow: 8px 0 40px #7c3aed22;
        }
        .login-illustration img.main {
            width: 320px;
            max-width: 90vw;
            border-radius: 24px;
            box-shadow: 0 8px 40px #7c3aed22;
            animation: floatImg 2.8s infinite ease-in-out alternate;
            background: #f8fafc;
            margin-bottom: 1.5rem;
        }
        .login-illustration img.decor {
            width: 120px;
            max-width: 80vw;
            margin-top: 1rem;
            opacity: 0.7;
            filter: drop-shadow(0 0 3px #7c3aed88);
            animation: floatImgDecor 4s infinite ease-in-out alternate;
        }
        @keyframes floatImg {
            from { transform: translateY(0);}
            to { transform: translateY(-18px);}
        }
        @keyframes floatImgDecor {
            from { transform: translateY(0);}
            to { transform: translateY(-10px);}
        }
        .login-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: transparent;
            border-radius: 22px;
            box-shadow: 0 8px 32px #7c3aed22;
            padding: 48px 38px 38px 38px;
            max-width: 400px;
            width: 100%;
        }
        .login-container {
            width: 100%;
            text-align: center;
            animation: fadeInUp 1s;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .login-container h2 {
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 2.1rem;
            letter-spacing: 1.2px;
            color: #7c3aed;
            text-shadow: 0 0 10px #7c3aed33;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .login-container h2 i {
            color: #7c3aed;
            font-size: 1.5em;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }
        label {
            font-weight: 700;
            font-size: 1rem;
            text-align: left;
            color: #7c3aed;
            margin-bottom: 4px;
        }
        input[type="email"], input[type="password"] {
            padding: 13px 14px;
            border-radius: 8px;
            border: 2px solid #c4b5fd;
            background: #f3e8fd;
            color: #4b3a71;
            font-size: 1rem;
            transition: background-color 0.3s, border-color 0.3s;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            background: #ede9fe;
            border-color: #7c3aed;
            box-shadow: 0 0 6px #7c3aed33;
        }
        button[type="submit"] {
            padding: 14px 0;
            background: linear-gradient(90deg, #7c3aed 60%, #a78bfa 100%);
            border: none;
            border-radius: 999px;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            cursor: pointer;
            margin-top: 8px;
            box-shadow: 0 4px 16px #7c3aed33;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.18s;
        }
        button[type="submit"]:hover {
            background: linear-gradient(90deg, #a78bfa 60%, #7c3aed 100%);
            color: #fff;
            transform: scale(1.03) translateY(-2px);
        }
        .links {
            margin-top: 24px;
            font-size: 0.95rem;
        }
        .links a {
            color: #7c3aed;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.25s;
        }
        .links a:hover {
            color: #a78bfa;
        }
        @media (max-width: 900px) {
            .login-wrapper {
                flex-direction: column;
            }
            .login-illustration {
                min-height: 250px;
                border-radius: 0 0 22px 22px;
                box-shadow: none;
                padding-top: 40px;
            }
            .login-illustration img.main {
                width: 260px;
                margin-bottom: 1rem;
            }
            .login-illustration img.decor {
                width: 90px;
            }
            .login-form-panel {
                max-width: 90vw;
                padding: 32px 20px 40px 20px;
                border-radius: 22px;
                box-shadow: 0 8px 24px #7c3aed22;
                min-height: auto;
                margin-top: 20px;
            }
        }
        @media (max-width: 600px) {
            .login-container h2 {
                font-size: 1.8rem;
            }
            input[type="email"], input[type="password"] {
                font-size: 0.9rem;
                padding: 12px 12px;
            }
            button[type="submit"] {
                font-size: 1rem;
                padding: 12px 0;
            }
        }
    </style>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="login-wrapper">
    <div class="login-illustration" aria-hidden="true">
      <img class="main" src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Admin Shield Icon" />
      <img class="decor" src="https://cdn.pixabay.com/photo/2018/05/08/08/46/abstract-3384794_960_720.png" alt="" />
    </div>
    <div class="login-form-panel">
      <section class="login-container" aria-label="Admin Login Form">
        <h2><i class="fas fa-user-shield"></i> Admin Login</h2>
        <form action="admin_login.php" method="post" autocomplete="off">
          <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="admin@example.com" required autocomplete="username" />
          </div>
          <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Your password" required autocomplete="current-password" />
          </div>
          <button type="submit">Log In</button>
        </form>
        <div class="links">
          <a href="index.php">← Back to Home</a>
        </div>
      </section>
    </div>
  </div>
</body>
</html>

