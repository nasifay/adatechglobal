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

    // Attempt to send using mail() and return 'OK' on success (validate.js expects this string)
    $sent = false;
    try {
      $sent = @mail($receiving_email_address, $email_subject, $email_body, $headers);
    } catch (Exception $e) {
      $sent = false;
    }

    if ($sent) {
      echo 'OK';
    } else {
      echo 'Failed to send message. Please contact us directly at ' . $receiving_email_address;
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
