



<?php 


include("db.php");
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_email($email, $password , $Faculty_Name){

    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'asd9708402721@gmail.com';
    $mail->Password   = 'szdf ildo sxmq qkqn';
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom("asd9708402721@gmail.com", "Admin");
    $mail->addAddress($email,$Faculty_Name);

    //Content
    $mail->isHTML(true);
    $mail->Subject = "Password";
    $mail->Body    = "Your password for $email is <b> $password</b>";
         
       

    $mail->send();
        return true;
    
 //catch Exception($se){
  //  return false;
 //}
}

             
include("db.php");
if(isset($_POST['forgot'])){
 // $email = mysqli_real_escape_string($connection,$_POST['email']);
 // $password = mysqli_real_escape_string($con,$_POST['password']);

 $query = "Select * from users where email = '$_POST[email]' ";
    
    $query_run = mysqli_query($conn,$query);
    if (mysqli_num_rows($query_run)){
        
      //  $query_run2 = mysqli_fetch_assoc( $query_run );

       // if($query_run2['is_verified']==1){
            while( $row=mysqli_fetch_assoc( $query_run )) {
                $_SESSION['name'] = $row['name'];
                
                
                $_SESSION['email'] = $row['email'];
                $_SESSION['password'] = $row['password'];
                if(send_email($_SESSION['email'],$_SESSION['password'], $_SESSION['name'])){
                echo"<script type='text/javascript'>
        alert('Your password has been sent to your registered email');
        window.location.href='user_login.php';
             </script>";
                }
                else{
                    echo"<script type='text/javascript'>
        alert('Please try again.');
        window.location.href='forgot.php';
             </script>";

                }
    
             }
             

            }
            else{
                echo"<script type='text/javascript'>
    alert('Email not registered , Please try again.');
    window.location.href='forgot.php';
         </script>";

      
        

        }

    
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ODF_FORGOT</title>
    <script src="includes/jquery_latest.js"></script>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script> 
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <!-- <center><div class="row"></center>
        <div class="col-md-3 m-auto" id="login_home_page">
            <center><h3 style="background-color:#5A8F7B; padding:5px; width:15vw;">User Login</h3></center>
            <form action="" method="post">
                <div class='form-group'>
                   <center> <input type='email' name="Email" class="form-control"  placeholder="Enter your email id" style="margin: bottom 3px;; width:28vh; height:4vh" required></center><br/>
                </div>
                <div class='form-group'>
                    <center><input type='password' name="Password" class="form-control"  placeholder="Enter your password" style="margin: bottom 4px;; width:28vh ;height:4vh" required><br/></center>
                </div>
                <div class="form-group">
                   <center><input type='submit' name="userLogin" value='Login' class='btn btn-warning' style=" width:13vh; height:3vh"></center>
                </div>
</form> -->
<!-- <a href="index.html" class="btn btn-danger" style="margin-top: 2vh;">Go to the home page</a>
    </div>
</div> -->

<div class="container">
        <div class="box form-box">
              
            <header>Forgot password</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="Email" id="email" autocomplete="off" required>
                </div>

                
                <div class="field">
                    
                    <input type="submit" class="btn" name="forgot" value="forgot" required>
                </div>
                
            </form>
        </div>
    
      </div>
</body>
</html>