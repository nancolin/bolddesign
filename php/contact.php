<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = [
  'error' => null,
  'success' => false,
];

if (!isset($_POST['name']) || !isset($_POST['email']) ||
    !isset($_POST['subject']) || !isset($_POST['message'])) {
  $response['error'] = 'Invalid request, data missing.';
}
elseif (preg_match('/[\r\n]/', $_POST['name'])) {
  $response['error'] = 'Invalid name, cannot contain new line.';
}
elseif (preg_match('/[\r\n]/', $_POST['email'])) {
  $response['error'] = 'Invalid email, cannot contain new line.';
}
elseif (preg_match('/[\r\n]/', $_POST['subject'])) {
  $response['error'] = 'Invalid subject, cannot contain new line.';
}
else {
  try {
    // Load Composer's autoloader
    require 'vendor/autoload.php';
    require '_private/credentials.php';

    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    // Server settings
    // $mail->SMTPDebug = 2;                               // Enable verbose debug output
    // $mail->isSMTP();                                    // Set mailer to use SMTP
    // $mail->Host       = 'mail.gandi.net';               // Specify main and backup SMTP servers
    // $mail->SMTPAuth   = true;                           // Enable SMTP authentication
    // $mail->Username   = 'contact@nancolin.nl';          // SMTP username
    // $mail->Password   = $credentials['email_password']; // SMTP password
    // $mail->SMTPSecure = 'ssl';                          // Enable TLS encryption, `ssl` also accepted
    // $mail->Port       = 465;                            // TCP port to connect to

    // Recipients
    $mail->setFrom('contact@nancolin.nl', $_POST['name']);
    $mail->addAddress('nancolin92@gmail.com');     // Add a recipient
    $mail->addReplyTo($_POST['email'], $_POST['name']);

    // Content
    $mail->isHTML(false); // Set email format to NOT HTML
    $mail->Subject = $_POST['subject'];
    $mail->Body = $_POST['message'];

    $mail->send();
    $response['success'] = true;
  } catch (Exception $e) {
    $response['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
die();
