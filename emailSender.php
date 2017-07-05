<?php

/**
 * Send a Email request using PHP Mail
 * @param string $subject to send
 * @param string $originator to send
 * @param string $body to send
 * @return bool
 */
function send_email($subject, $originator, $body, $reference) {
  // User IP and Location info.
  $locationInfo = false;
  try {
    $locationInfo = json_decode(getLocationInfo(), true);
  } catch (Exception $ex) {
    
  }

  $to = "email_reciver@yourdomain.com";
  $message = "<b>Something went wrong.</b>";
  $message .= "<h1>Name of Web: " . $originator . "</h1>";
  $message .= "<h2>Reference#: " . $reference . "</h2>";
  $message .= "<p>" . $body . "</p>";
  if (isset($_SERVER['HTTP_REFERER'])) {
    $message .= "<p><b>User landed here from:</b>" . "</p>";
    $message .= "<p>" . $_SERVER['HTTP_REFERER'] . "</p>";
  }
  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $message .= "<p><b>HTTP_USER_AGENT:</b></p>";
    $message .= "<p>" . $_SERVER['HTTP_USER_AGENT'] . "</p>";
  }

  // If we recive location info successfully then pass it to the Email.
  if (is_array($locationInfo) && !empty($locationInfo)) {
    foreach ($locationInfo as $key => $value) {
      $message .= "<p><b>" . $key . ":</b> " . $value . "</p>";
    }
  }

  $header = "From:developer_email@yourdomain.com \r\n";
  $header .= "Cc:developer_email_2@ourdomain.com \r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-type: text/html\r\n";

  $email_sent = mail($to, $subject, $message, $header);

  return $email_sent;
}

/**
 * Start of getting User Location information
 * @param string $ip to send
 * @return array
 */
function getLocationInfo() {
  $ipaddress = get_client_ip();
  $details = false;
  if ($ipaddress) {
    $details = file_get_contents('http://freegeoip.net/json/' . $ipaddress);
  }
  return $details;
}

/**
 * To get the client IP address.
 * @return string
 */
function get_client_ip() {
  if (trim(getenv('HTTP_X_REAL_IP')) != false) {
    $ipaddress = getenv('HTTP_X_REAL_IP');
  }
  else if (trim(getenv('HTTP_CLIENT_IP')) != false) {
    $ipaddress = getenv('HTTP_CLIENT_IP');
  }
  else if (trim(getenv('HTTP_X_FORWARDED_FOR')) != false) {
    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  }
  else if (trim(getenv('HTTP_X_FORWARDED')) != false) {
    $ipaddress = getenv('HTTP_X_FORWARDED');
  }
  else if (trim(getenv('HTTP_FORWARDED_FOR')) != false) {
    $ipaddress = getenv('HTTP_FORWARDED_FOR');
  }
  else if (trim(getenv('HTTP_FORWARDED')) != false) {
    $ipaddress = getenv('HTTP_FORWARDED');
  }
  else if (trim(getenv('REMOTE_ADDR')) != false) {
    $ipaddress = getenv('REMOTE_ADDR');
  }
  else {
    $ipaddress = false;
  }
  return $ipaddress;
}

/*
 * $reference A random reference number which we recive in SMS and in Email also.
 * So if there are so many emails and we want to find particular one,
 * then we can search in E-mails using reference we recive in SMS.
 */
$reference = date("is") . rand(1, 500);

/* Email Body */
$body = "Details of Error";
$originator = "location of error";

$body = $body . " Reference#: " . $reference;

$send_an_email = send_email("There is an Error in Portal", $originator, $body, $reference);

if ($send_an_email) {
  echo "Email has been sent Successfully";
}
else {
  echo "Email has NOT been sent Successfully";
}
?>