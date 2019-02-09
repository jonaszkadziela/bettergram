<?php
  if ((defined('PHP_SESSION_ACTIVE') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
  {
    session_start();
  }
  if (file_exists('../../config.php'))
  {
    require_once('../../config.php');
  }

  if (empty($_SESSION['render_component']))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL . 'index.php');
    exit();
  }
  unset($_SESSION['render_component']);
?>
