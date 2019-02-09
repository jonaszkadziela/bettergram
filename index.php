<?php
  if ((defined('PHP_SESSION_ACTIVE') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
  {
    session_start();
  }

  require_once('config.php');
  require_once('routes.php');
?>
<!doctype html>
<html lang="pl-PL">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link rel="stylesheet" href="assets/stylesheets/main.css">
    <title>BetterGram</title>
  </head>
  <body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="assets/javascripts/main.js"></script>
    <?php
      $_SESSION['render_view'] = true;
      $file = VIEWS_PATH . $page . '.php';
      if (file_exists($file))
      {
        include_once($file);
      }
      else
      {
        $_SESSION['error_no'] = '404';
        include_once(VIEWS_PATH . 'error.php');
      }
    ?>
  </body>
</html>
