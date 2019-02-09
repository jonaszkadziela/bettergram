<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;

  if (!validate_request('get', [$user_id]))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  unset($_SESSION['current_user']);

  $_SESSION['notice'][] = 'Proces wylogowania przebiegł pomyślnie!';

  header('Location: ' . ROOT_URL);
  exit();
?>
