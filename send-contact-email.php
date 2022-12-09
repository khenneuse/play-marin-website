<?php

function processText($text) {
    $text = strip_tags($text);
    $text = trim($text);
    $text = htmlspecialchars($text);
    return $text;
}

$from = processText($_POST['name']);
$from_email = processText($_POST['replyto']);
$phone = processText($_POST['phone']);
$reason = processText($_POST['reason']);
$raw_message = processText($_POST['message']);
$raw_message = wordwrap($raw_message, 70);

if (!empty(processText($_POST['_honeypot']))) {
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

$raw_message
ET;

$headers[] = "From: $from <$from_email>";
$headers[] = "To: $to_name <$to_email>";
if (!empty($bcc_name)) {
  $headers[] = "bcc: $bcc_name <$bcc_email>";
}

mail($to,$subject,$message, implode("\r\n", $headers));

echo "Email sent";
?>
