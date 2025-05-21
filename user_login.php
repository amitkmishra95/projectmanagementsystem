<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['verify'] == 0) {
            echo "<script>
                alert('Please Verify Email');
                </script>";
        } elseif ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['erpid'] = $row['erpid'];
            $_SESSION['name'] = $row['name'];
            header("Location: user_dashboard.php");
            exit;
        } else {
            echo "<script>
                alert('Incorrect Password');
                </script>";
        }
    } else {
        echo "<script>
                alert('Email not found');
                </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>User Login - Student Project Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --background: #f8f9ff;
            --text: #2b2d42;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: var(--background);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1000px;
            width: 90%;
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(67, 97, 238, 0.15);
            overflow: hidden;
            animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .illustration-side {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .illustration-img {
            width: 100%;
            max-width: 400px;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .form-side {
            padding: 60px 40px;
            position: relative;
        }

        h2 {
            color: var(--text);
            font-size: 2rem;
            margin-bottom: 2.5rem;
            font-weight: 600;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .input-group {
            position: relative;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        button[type="submit"] {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
        }

        .links a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .links a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                width: 95%;
            }

            .illustration-side {
                display: none;
            }

            .form-side {
                padding: 40px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="illustration-side">
  <img src="login.jpg" alt="Login Illustration" class="illustration-img">

        </div>
        <div class="form-side">
            <h2>Welcome Back</h2>
            <form action="" method="post" autocomplete="off">
                <div class="input-group">
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit">Sign In</button>
            </form>
            <div class="links">
                <a href="register.php">Create an account</a> •
                <a href="index.php">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>

