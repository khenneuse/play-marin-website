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

function buildEmailArray($to_names, $to_emails) {
  $email_array = array();
  for ($index = 0; $index < count($to_emails); $index++) {
    array_push($email_array,
      array("email" => $to_emails[$index], "name" => $to_names[$index])
    );
  }
  return $email_array;
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
$api_key = $ini_array['api_key'];
$to_names = $ini_array['to_name'];
$to_emails = $ini_array['to_email'];
$bcc_names  = isset($ini_array['bcc_name']) ? $ini_array['bcc_name'] : [];
$bcc_emails = isset($ini_array['bcc_email']) ? $ini_array['bcc_email'] : [];

$to_email_array = buildEmailArray($to_names, $to_emails);
$bcc_email_array = buildEmailArray($bcc_names, $bcc_emails);

$message = <<<ET
Name: $from
Email: $from_email
Phone: $phone

$raw_message
ET;

$data = array(
  "sender" => array(
    "email" => $from_email,
    "name" => $from
  ),
  "to" => $to_email_array,
  "bcc" => $bcc_email_array,
  "subject" => "$reason: $from",
  "textContent" => $message
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.sendinblue.com/v3/smtp/email');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = "Api-Key: $api_key";
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
  echo 'Error:' . curl_error($ch);
} else {
  echo "Email successfully sent\n";
}
curl_close($ch);

header("Location: index.html");

?>
