<?php
session_start();
include_once "smtp/class.phpmailer.php";
require('smtp/class.smtp.php');

$conn = mysqli_connect("localhost", "root", "", "authentication_database");

function sendEmail($subject, $from_name, $email, $content, $client='') {
    $mail = new PHPMailer();
    $mail->CharSet = "utf-8";
    $mail->IsSMTP();
    $mail->SMTPAuth = true;

    if ($client == "" || $client == "gmail") {
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'bikashthapa9815648792@gmail.com';
        $mail->Password = 'gpbw kqeq uuvo ooxw'; // Google App Password
        $mail->Port = 587;
        $mail->setFrom('bikashthapa9815648792@gmail.com', $from_name);
    }

    if (is_array($email)) {
        foreach ($email as $recipient) {
            $mail->AddAddress($recipient);
        }
    } else {
        $mail->AddAddress($email);
    }

    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $content;

    if ($mail->send()) {
        return true;
    } else {
        return $mail->ErrorInfo;
    }
}
?>

