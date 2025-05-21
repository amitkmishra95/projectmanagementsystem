<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function send_email($email, $vcode) {
    $mail = new PHPMailer(true);
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
