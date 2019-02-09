<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  if (isset($_SESSION['alert']))
  {
    if (!is_array($_SESSION['alert']))
    {
      $_SESSION['alert'] = [$_SESSION['alert']];
    }

    foreach ($_SESSION['alert'] as $alert)
    {
      echo
        '<div class="d-flex alert alert-danger alert-dismissible fade show mb-1-5" role="alert">' . PHP_EOL .
          '<i class="fas fa-exclamation-circle fa-lg py-0-5 pr-1"></i>' . PHP_EOL .
          '<div class="d-flex flex-column justify-content-center">' . PHP_EOL .
            $alert . PHP_EOL .
          '</div>' . PHP_EOL .
          '<button class="close" type="button" data-dismiss="alert" aria-label="Close">' . PHP_EOL .
            '<i class="far fa-times-circle" aria-hidden="true"></i>' . PHP_EOL .
          '</button>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
  }
  if (isset($_SESSION['notice']))
  {
    if (!is_array($_SESSION['notice']))
    {
      $_SESSION['notice'] = [$_SESSION['notice']];
    }

    foreach ($_SESSION['notice'] as $notice)
    {
      echo
        '<div class="d-flex alert alert-info alert-dismissible fade show mb-1-5" role="alert">' . PHP_EOL .
          '<i class="fas fa-info-circle fa-lg py-0-5 pr-1"></i>' . PHP_EOL .
          '<div class="d-flex flex-column justify-content-center">' . PHP_EOL .
            $notice . PHP_EOL .
          '</div>' . PHP_EOL .
          '<button class="close" type="button" data-dismiss="alert" aria-label="Close">' . PHP_EOL .
            '<i class="far fa-times-circle" aria-hidden="true"></i>' . PHP_EOL .
          '</button>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
  }

  unset($_SESSION['alert']);
  unset($_SESSION['notice']);
?>
