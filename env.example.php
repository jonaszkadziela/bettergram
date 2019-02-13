<?php
  require_once 'config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $env =
  [
    'db' =>
    [
      'host' => '',
      'user' => '',
      'password' => '',
      'name' => 'bettergram',
      'port' => 3306,
      'socket' => false
    ],
    'analytics' =>
    [
      'tracking_id' => ''
    ],
    'recaptcha' =>
    [
      'site_key' => '',
      'secret_key' => ''
    ]
  ];
?>
