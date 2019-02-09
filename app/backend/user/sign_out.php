<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');

  if (!validate_request('get', array(empty($_SESSION['user_signed_in']))))
  {
    header('Location: ' . ROOT_URL);
    exit();
  }

  session_unset();

  $_SESSION['alert'] = 'Proces wylogowania przebiegł pomyślnie!';
  $_SESSION['alert_class'] = 'alert-info';

  header('Location: ' . ROOT_URL);
  exit();
?>
