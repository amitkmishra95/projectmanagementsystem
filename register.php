<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// require 'vendor/autoload.php';

function send_email($email, $vcode) {
    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");
    $mail = new PHPMailer(true);
//    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // or your SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'asd9708402721@gmail.com';
        $mail->Password = 'szdf ildo sxmq qkqn';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('asd9708402721@gmail.com', 'PMS System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Verify your Email';
        $mail->Body = "Click the link to verify your email: <a href='http://localhost/verify.php?vcode=$vcode'>Verify</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>

<?php
include "db.php";
// include "sendmail.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $erpid = $_POST['erpid'];
    $semester = $_POST['semester'];
    $rollno = $_POST['rollno'];
    $batch_start = $_POST['batch_start'];
    $batch_end = $_POST['batch_end'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $section = $_POST['section'];
    $branch = $_POST['branch'];
    $vcode = bin2hex(random_bytes(16));
    $verify = 0;

    // Password confirmation
    $query = "SELECT * from users where email = '$_POST[email]'";
    $result = mysqli_query($conn, $query);
    if ($result){
        if(mysqli_num_rows($result) > 0){
            $result_fetch = mysqli_fetch_assoc($result);
            if($result_fetch['email'] === $email){
                echo "<script>
                alert('Email already exists')
                </script>";
            }
        }
        else{
            $vcode = bin2hex(random_bytes(16));
            if ($password === $cpassword){
            
                $query  = "INSERT INTO users (name, erpid, rollno, semester , batch_start, batch_end, email, contact, password, vcode, verify, section, branch) values('$name','$erpid','$rollno',,'$semester','$batch_start','$batch_end','$email','$contact' ,'$password','$vcode' ,'$verify','$section','$branch')";
                $result = mysqli_query($conn, $query);
                if ($result && send_email($_POST['email'],$v_code,$_POST['fname'])){ //  
                    echo"<script type='text/javascript'>alert('Email sent Successfully!');
                    window.location.href='user_login.php';
                        </script>";
                }
                else{
                    echo"<script type='text/javascript'>alert('Email  not sent !');
                    window.location.href='index.php';
                        </script>";

                }
            }
            else{
                echo "<script>
                alert('password and confirm password do not match')
                </script>";
            }
    }
    

    }


  }
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=no" />
<title>Register - Student Project Management</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap');
  * {
    box-sizing: border-box;
  }
  body, html {
    margin: 0; padding: 0;
    height: 100%;
    font-family: 'Quicksand', sans-serif;
    background: linear-gradient(135deg, #fddb92 0%, #d1fdff 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    color: #213547;
  }
  .main-wrapper {
    display: flex;
    flex-direction: row;
    background: rgba(255,255,255,0.85);
    box-shadow: 0 12px 30px rgba(33,85,119,0.08);
    border-radius: 28px;
    overflow: hidden;
    max-width: 900px;
    width: 95vw;
    margin: 24px 0;
  }
  .image-side {
    flex: 1.2;
    background: linear-gradient(120deg, #d1fdff 60%, #fddb92 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 36px 24px;
    min-width: 280px;
  }
  .image-side img {
    width: 100%;
    max-width: 330px;
    border-radius: 18px;
    box-shadow: 0 6px 24px #cbe7fa77;
    object-fit: cover;
    transition: transform 0.4s;
  }
  .image-side img:hover {
    transform: scale(1.04) rotate(-2deg);
  }
  .container {
    flex: 1.8;
    padding: 48px 32px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-width: 320px;
    max-width: 480px;
  }
  h2 {
    margin-bottom: 28px;
    font-weight: 700;
    font-size: 2.2rem;
    letter-spacing: 1px;
    color: #215575;
  }
  form {
    display: flex;
    flex-direction: column;
    gap: 14px;
    max-height: 580px;
    overflow-y: auto;
    padding-right: 6px;
    animation: fadeInUp 0.8s;
  }
  @keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(40px);}
    100% { opacity: 1; transform: translateY(0);}
  }
  label {
    font-weight: 600;
    font-size: 1rem;
    text-align: left;
    color: #4a687c;
  }
  input[type="text"],
  input[type="email"],
  input[type="password"],
  input[type="number"],
  input[type="tel"],
  select {
    padding: 14px 14px;
    border-radius: 20px;
    border: 2px solid #95cde6;
    font-size: 1rem;
    transition: border-color 0.3s, background-color 0.3s;
    background: #ffffffcc;
    color: #213547;
    width: 100%;
  }
  input[type="text"]:focus,
  input[type="email"]:focus,
  input[type="password"]:focus,
  input[type="number"]:focus,
  input[type="tel"]:focus,
  select:focus {
    outline: none;
    border-color: #49a6e9;
    box-shadow: 0 0 12px #5bb8ffcc;
  }
  input[readonly] {
    background: #f0f0f0;
    border-color: #ccc;
    color: #666;
    cursor: default;
  }
  button[type="submit"] {
    margin-top: 15px;
    padding: 16px 0;
    background: linear-gradient(90deg, #4a90e2 60%, #49e9c3 100%);
    border: none;
    border-radius: 25px;
    color: white;
    font-weight: 700;
    font-size: 1.3rem;
    cursor: pointer;
    box-shadow: 0 7px 20px #3a70bf44;
    transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
  }
  button[type="submit"]:hover {
    background: linear-gradient(90deg, #6aaeec 60%, #4ae9b3 100%);
    box-shadow: 0 12px 30px #6aaeec66;
    transform: translateY(-2px) scale(1.03);
  }
  .links {
    margin-top: 26px;
    font-size: 0.98rem;
  }
  .links a {
    color: #337ab7;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.25s;
  }
  .links a:hover {
    color: #5aabe3;
  }
  /* Custom scrollbar for form */
  form::-webkit-scrollbar {
    width: 6px;
  }
  form::-webkit-scrollbar-thumb {
    background-color: #95cde6cc;
    border-radius: 10px;
  }
  form::-webkit-scrollbar-track {
    background: transparent;
  }
  @media (max-width: 900px) {
    .main-wrapper {
      flex-direction: column;
      max-width: 98vw;
    }
    .image-side {
      padding: 24px 12px 8px 12px;
      min-width: unset;
      justify-content: flex-start;
    }
    .container {
      padding: 36px 16px 32px 16px;
      min-width: unset;
      max-width: unset;
    }
  }
  @media (max-width: 500px) {
    .main-wrapper {
      border-radius: 0;
      margin: 0;
    }
    .image-side img {
      max-width: 95vw;
      border-radius: 12px;
    }
    .container {
      padding: 24px 6vw 18px 6vw;
    }
  }
</style>
</head>
<body>
  <div class="main-wrapper">
    <div class="image-side">
      <img src="https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=600&q=80" alt="Project Management Illustration" />
    </div>
    <div class="container" aria-label="User Registration Form">
      <h2>Register</h2>
      <form action="register.php" method="POST"> 
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Your full name" required />

        <label for="erpid">ERPID</label>
        <input type="text" id="erpid" name="erpid" placeholder="Your ERPID" required />

        <label for="semester">ERPID</label>
        <input type="text" id="semester" name="semester" placeholder="Your SEMESTER" required />

        <label for="rollno">Roll No</label>
        <input type="number" id="rollno" name="rollno" placeholder="Your Roll Number" min="1" required />

        <label for="batch_start">Batch Start Year</label>
        <input type="number" id="batch_start" name="batch_start" placeholder="e.g. 2020" min="2000" max="2030" required />

        <label for="batch_end">Batch End Year</label>
        <input type="number" id="batch_end" name="batch_end" placeholder="e.g. 2024" min="2004" max="2034" required />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Your email address" required />

        <label for="contact">Contact Number</label>
        <input type="tel" id="contact" name="contact" placeholder="10-digit number" pattern="[0-9]{10}" maxlength="10" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required />

        <label for="cpassword">Confirm Password</label>
        <input type="password" id="cpassword" name="cpassword" placeholder="Confirm your password" required />

        <label for="section">Section</label>
        <input type="text" id="section" name="section" placeholder="Your class section" required />

        <label for="branch">Branch</label>
        <input type="text" id="branch" name="branch" placeholder="Your branch of study" required />

        <button type="submit">Register</button>
      </form>
      <div class="links">
        <a href="user_login.php">Already registered? Login here</a><br />
        <a href="index.php">Back to Home</a>
      </div>
    </div>
  </div>
</body>
</html>
