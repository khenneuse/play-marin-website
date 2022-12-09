<?php

$from = $_POST['name'];
$from_email = $_POST['_replyto'];
$phone = $_POST['phone'];
$reason = $_POST['reason'];
$raw_message = wordwrap($_POST['message'], 70);

if (!empty($_POST['_honeypot'])) {
  exit("Email forwarded");
}

$ini_array = parse_ini_file("contact.ini");
$to_name = $ini_array['to_name'];
$to_email = $ini_array['to_email'];
$bcc_name = $ini_array['bcc_name'];
$bcc_email = $ini_array['bcc_email'];
$subject = "$reason: $from";
$message = <<<ET
Name: $from
Email: $from_email
Phone: $phone

$rawMessage
ET;

$headers[] = "From: $from <$from_email>";
$headers[] = "To: $to_name <$to_email>";
if (!empty($bcc_name)) {
  $headers[] = "bcc: $bcc_name <$bcc_email>";
}

mail($to,$subject,$message, implode("\r\n", $headers));

echo "Email sent";
?>
