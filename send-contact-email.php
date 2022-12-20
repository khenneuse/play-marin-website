<?php

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
$to_emails = $ini_array['to_name'];
$to_names = $ini_array['to_name'];
$to_name   = $to_emails[0];
$to_email  = $to_emails[0];
$bcc_names  = isset($ini_array['bcc_name']) ? $ini_array['bcc_name'] : [];
$bcc_emails = isset($ini_array['bcc_email']) ? $ini_array['bcc_email'] : [];

$subject = "$reason: $from";
$message = <<<ET
Name: $from
Email: $from_email
Phone: $phone

$raw_message
ET;

$headers[] = "From: $from <$from_email>";
for ($index = 0; $index < count($to_emails); $index++) {
  $headers[] = "To: $to_names[$index] <$to_emails[$index]>";
}
for ($index = 0; $index < count($bcc_emails); $index++) {
  $headers[] = "bcc: $bcc_names[$index] <$bcc_emails[$index]>";
}

mail($to_email,$subject,$message, implode("\r\n", $headers));

echo "Email successfully sent\n";
header("Location: index.html");

?>
