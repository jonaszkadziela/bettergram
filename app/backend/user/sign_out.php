<?php
  if ((defined('PHP_SESSION_ACTIVE') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
  {
    session_start();
  }
  if (file_exists('../../../config.php'))
  {
    require_once('../../../config.php');
  }

  $invalid_request = false;
  if (empty($_SESSION['user_signed_in']) || $_SERVER['REQUEST_METHOD'] !== 'GET')
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Proces wylogowania nie powiódł się!';
    $invalid_request = true;
  }
  if ($invalid_request)
  {
    header('Location: ' . ROOT_URL . 'index.php');
    exit();
  }

  session_unset();

  $_SESSION['alert'] = 'Proces wylogowania przebiegł pomyślnie!';
  $_SESSION['alert_class'] = 'alert-info';

  header('Location: ' . ROOT_URL . 'index.php');
?>
