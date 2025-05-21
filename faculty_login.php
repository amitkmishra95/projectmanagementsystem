<?php session_start(); include "db.php"; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM faculty WHERE email='$email'");
    if ($res->num_rows == 1) {
        $row = $res->fetch_assoc();
        if ($password== $row['password']) {
            $_SESSION['faculty_id'] = $row['faculty_id'];
            $_SESSION['faculty_name'] = $row['name'];
            header("Location: faculty_dashboard.php");
            exit();
        }
    }
    echo "Invalid admin credentials.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no" />
<title>Faculty Login - Student Project Management</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap');
  * {
    box-sizing: border-box;
  }
  body, html {
    margin: 0; padding: 0;
    height: 100%;
    font-family: 'Open Sans', sans-serif;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
  }
  .container {
    background: white;
    width: 340px;
    padding: 40px 30px;
    border-radius: 25px;
    box-shadow: 0 14px 30px rgba(0,0,0,0.2);
    text-align: center;
  }
  h2 {
    margin-bottom: 24px;
    font-weight: 700;
    color: #333;
    font-size: 2rem;
  }
  form {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  label {
    font-size: 0.95rem;
    font-weight: 600;
    color: #555;
    text-align: left;
  }
  input[type="email"], input[type="password"] {
    padding: 14px 16px;
    border-radius: 12px;
    border: 2px solid #00b8ff;
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }
  input[type="email"]:focus, input[type="password"]:focus {
    outline: none;
    border-color: #007ecc;
    box-shadow: 0 0 8px #007ecc88;
  }
  button[type="submit"] {
    margin-top: 10px;
    padding: 14px 0;
    background: #007ecc;
    border: none;
    border-radius: 14px;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  button[type="submit"]:hover {
    background: #005fa3;
  }
  .footer-links {
    margin-top: 20px;
    font-size: 0.9rem;
    display: flex;
    justify-content: space-between;
  }
  .footer-links a {
    color: #007ecc;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
  }
  .footer-links a:hover {
    color: #005fa3;
    text-decoration: underline;
  }
  @media (max-width: 400px) {
    .container {
      width: 90vw;
      padding: 36px 24px;
    }
  }
</style>
</head>
<body>
  <div class="container" aria-label="User Login Form">
    <h2>Faculty Login</h2>
    <form method="post" action="">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Your email" required autocomplete="username" />
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Your password" required autocomplete="current-password" />
      <button type="submit">Log In</button>
    </form>
    <div class="footer-links">
      <!-- <a href="register.html">Register</a> -->
      <a href="index.php">Home</a>
    </div>
  </div>
</body>
</html>
