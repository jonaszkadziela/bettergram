<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  if (isset($_SESSION['alert']))
  {
    echo
      '<div class="alert ' . (isset($_SESSION['alert_class']) ? $_SESSION['alert_class'] : 'alert-danger') . ' alert-dismissible fade show my-1-5" role="alert">' . PHP_EOL .
        $_SESSION['alert'] . PHP_EOL .
        '<button class="close" type="button" data-dismiss="alert" aria-label="Close">' . PHP_EOL .
          '<i class="far fa-times-circle" aria-hidden="true"></i>' . PHP_EOL .
        '</button>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }

  unset($_SESSION['alert']);
  unset($_SESSION['alert_class']);
?>
