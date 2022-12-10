<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/src/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/src/PHPMailer.php';

$CONTACT_INI = 'contact.ini';

function checkPostVariable($variable_name) {
  if (empty($_POST[$variable_name])) {
    exit($variable_name.' is required');
  }
}

function processText($text) {
  $text = strip_tags($text);
  $text = stripslashes($text);
  $text = trim($text);
  $text = htmlspecialchars($text);
  return $text;
}

if (!empty($_POST['_honeypot'])) {
  exit("Email forwarded");
}

checkPostVariable('name');
checkPostVariable('replyto');
checkPostVariable('reason');

$replyto = processText($_POST["replyto"]);
if (!filter_var($replyto, FILTER_VALIDATE_EMAIL)) {
  exit("Invalid email format");
}

$from        = processText($_POST['name']);
$from_email  = $replyto;
$reason      = processText($_POST['reason']);
$phone       = isset($_POST['phone']) ? processText($_POST['phone']) : '';
$raw_message = isset($_POST['message']) ? processText($_POST['message']) : '';
$raw_message = wordwrap($raw_message, 70);

$ini_array = parse_ini_file($CONTACT_INI);
$to_name   = $ini_array['to_name'];
$to_email  = $ini_array['to_email'];
$bcc_name  = isset($ini_array['bcc_name']) ? $ini_array['bcc_name'] : '';
$bcc_email = isset($ini_array['bcc_email']) ? $ini_array['bcc_email'] : '';

$mail = new PHPMailer(true);

try {
  $mail->IsSMTP();
  $mail->SMTPAuth   = false;
  $mail->Port       = 25;
  $mail->Host       = "localhost";
  $mail->Username   = $ini_array['mail_username'];
  $mail->Password   = $ini_array['mail_password'];

  $mail->IsSendmail();

  $subject = "$reason: $from";
  $message = <<<ET
  Name: $from
  Email: $from_email
  Phone: $phone

  $raw_message
ET;

  $mail->setFrom($from_email, $from);
  $mail->AddAddress($to_email, $to_name);
  if (!empty($bcc_name)) {
    $mail->addBCC($bcc_email, $bcc_name);
  }

  $mail->Subject = $subject;
  $mail->Body    = $message;
  $mail->send();

  echo "Email successfully sent\n";
  header("Location: index.html");
  exit();

} catch (Exception $e) {
  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
