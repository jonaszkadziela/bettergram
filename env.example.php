<?php
  require_once('config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  $database = [
    'host' => '',
    'user' => '',
    'password' => '',
    'name' => ''
  ];
?>
