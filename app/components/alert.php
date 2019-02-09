<?php
  require_once('protect_components.php');

  if (isset($_SESSION['alert']))
  {
    $alert = $_SESSION['alert'];
    $alert_class = isset($_SESSION['alert_class']) ? $_SESSION['alert_class'] : 'alert-danger';

    echo
      '<div class="alert ' . $alert_class . ' alert-dismissible fade show my-4" role="alert">' .
      $alert .
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' .
      '<span aria-hidden="true">&times;</span>' .
      '</button>' .
      '</div>';
  }

  unset($_SESSION['alert']);
  unset($_SESSION['alert_class']);
?>
