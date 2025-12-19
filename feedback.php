<?php
// Public feedback submission page
// Simple form that posts to forms/feedback.php
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Leave Feedback</title>
  <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body class="p-4">
  <div class="container">
    <h1>Give Feedback</h1>
    <p>If you'd like to share feedback about our services, please fill this form.</p>
    <?php if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    if (!empty($_SESSION['feedback_success'])) { echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['feedback_success']).'</div>'; unset($_SESSION['feedback_success']); }
    if (!empty($_SESSION['feedback_error'])) { echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['feedback_error']).'</div>'; unset($_SESSION['feedback_error']); }
    ?>
    <form action="forms/feedback.php" method="post">
      <div class="mb-2"><input class="form-control" name="name" placeholder="Your name" required></div>
      <div class="mb-2"><input class="form-control" name="email" placeholder="Your email (optional)"></div>
      <div class="mb-2"><input class="form-control" name="company" placeholder="Company (optional)"></div>
      <div class="mb-2"><textarea class="form-control" name="message" rows="6" placeholder="Your feedback" required></textarea></div>
      <div><button class="btn btn-primary">Send Feedback</button></div>
    </form>
  </div>
</body>
</html>
