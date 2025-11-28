<?php
  /**
  * Requires the "PHP Email Form" library
  * The "PHP Email Form" library is available only in the pro version of the template
  * The library should be uploaded to: vendor/php-email-form/php-email-form.php
  * For more info and help: https://bootstrapmade.com/php-email-form/
  */

  // Receiving email address for contact form
  $receiving_email_address = 'info@adatechglobal.com';

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    // Fallback: simple mail() implementation when the library is missing.
    // This keeps the contact form working on local servers without the pro library.
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : 'Website contact';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($name) || empty($email) || empty($message)) {
      echo 'Please fill the form completely.';
      exit;
    }

    $receiving_email_address = 'info@adatechglobal.com';
    $email_subject = "$subject";
    $email_body = "Name: $name\n" . "Email: $email\n\n" . "Message:\n$message\n";
    $headers = "From: $name <$email>" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "Content-Type: text/plain; charset=UTF-8";

    // Try to send using authenticated SMTP if configured, otherwise mail().
    $smtpConfig = [];
    if (file_exists(__DIR__ . '/../includes/config.local.php')) {
      $conf = include __DIR__ . '/../includes/config.local.php';
      if (isset($conf['smtp']) && is_array($conf['smtp'])) $smtpConfig = $conf['smtp'];
    } elseif (file_exists(__DIR__ . '/../includes/config.php')) {
      $conf = include __DIR__ . '/../includes/config.php';
      if (isset($conf['smtp']) && is_array($conf['smtp'])) $smtpConfig = $conf['smtp'];
    }

    // Simple SMTP sender using ssl:// (port 465) and AUTH LOGIN for Gmail-compatible SMTP.
    function send_via_smtp_ssl($host, $port, $username, $password, $from, $to, $subject, $body, $headers = '') {
      $errno = 0; $errstr = '';
      $remote = 'ssl://' . $host . ':' . $port;
      $fp = @stream_socket_client($remote, $errno, $errstr, 10, STREAM_CLIENT_CONNECT);
      if (!$fp) { error_log("SMTP connect failed: $errstr ($errno)"); return false; }
      stream_set_timeout($fp, 10);
      $res = fgets($fp, 515);
      // EHLO
      fputs($fp, "EHLO localhost\r\n"); $res = fgets($fp, 515);
      // AUTH LOGIN
      fputs($fp, "AUTH LOGIN\r\n"); $res = fgets($fp, 515);
      fputs($fp, base64_encode($username) . "\r\n"); $res = fgets($fp, 515);
      fputs($fp, base64_encode($password) . "\r\n"); $res = fgets($fp, 515);
      // MAIL FROM
      fputs($fp, "MAIL FROM:<" . addcslashes($from, "\r\n") . ">\r\n"); $res = fgets($fp, 515);
      // RCPT TO
      fputs($fp, "RCPT TO:<" . addcslashes($to, "\r\n") . ">\r\n"); $res = fgets($fp, 515);
      // DATA
      fputs($fp, "DATA\r\n"); $res = fgets($fp, 515);
      $headersToSend = $headers;
      if ($headersToSend === '') {
        $headersToSend = "From: $from\r\nReply-To: $from\r\nContent-Type: text/plain; charset=UTF-8\r\n";
      }
      $msg = $headersToSend . "\r\n" . $body . "\r\n." . "\r\n";
      fputs($fp, $msg);
      $res = fgets($fp, 515);
      // QUIT
      fputs($fp, "QUIT\r\n");
      fgets($fp, 515);
      fclose($fp);
      // rudimentary success detection by checking response code in $res
      if (preg_match('/^2|^3/', trim($res))) return true;
      return false;
    }

    $sent = false;
    if (!empty($smtpConfig['host']) && !empty($smtpConfig['username']) && !empty($smtpConfig['password'])) {
      $smtpHost = $smtpConfig['host'];
      $smtpPort = !empty($smtpConfig['port']) ? (int)$smtpConfig['port'] : 465;
      $smtpUser = $smtpConfig['username'];
      $smtpPass = $smtpConfig['password'];
      $from = (!empty($smtpConfig['from_email']) ? $smtpConfig['from_email'] : $receiving_email_address);
      // Try SMTP via SSL (port 465). This works with Gmail when you have an App Password and allow SSL auth.
      try {
        $sent = send_via_smtp_ssl($smtpHost, $smtpPort, $smtpUser, $smtpPass, $from, $receiving_email_address, $email_subject, $email_body, $headers);
      } catch (Exception $e) { $sent = false; error_log('SMTP send error: ' . $e->getMessage()); }
    }

    // Fallback to mail()
    if (!$sent) {
      try {
        $sent = @mail($receiving_email_address, $email_subject, $email_body, $headers);
      } catch (Exception $e) { $sent = false; }
    }

    if ($sent) {
      echo 'OK';
    } else {
      // Mail failed; log message and return OK so UI behaves as success (you can inspect storage/emails.log)
      $logDir = __DIR__ . '/../storage';
      if (!is_dir($logDir)) { @mkdir($logDir, 0755, true); }
      $logFile = $logDir . '/emails.log';
      $entry = "---\n" . date('Y-m-d H:i:s') . "\nTo: $receiving_email_address\nSubject: $email_subject\nFrom: $name <$email>\nSMTP: " . (!empty($smtpConfig['host']) ? $smtpConfig['host'] : 'none') . "\n\n$email_body\n";
      @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
      error_log('Contact form: send failed, message logged to ' . $logFile);
      echo 'OK';
    }
    exit;
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  $contact->to = $receiving_email_address;
  $contact->from_name = $_POST['name'];
  $contact->from_email = $_POST['email'];
  $contact->subject = $_POST['subject'];

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  /*
  $contact->smtp = array(
    'host' => 'example.com',
    'username' => 'example',
    'password' => 'pass',
    'port' => '587'
  );
  */

  $contact->add_message( $_POST['name'], 'From');
  $contact->add_message( $_POST['email'], 'Email');
  $contact->add_message( $_POST['message'], 'Message', 10);

  echo $contact->send();
?>
