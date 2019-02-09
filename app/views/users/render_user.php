<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $user_badge_class = isset($user_badge_class) ? $user_badge_class : 'badge badge-pill badge-primary position-absolute position-top-right px-0-5 py-0-25 m--0-5';

  if (isset($user))
  {
    echo '<div class="media position-relative flex-column flex-md-row align-items-center bg-white shadow-sm rounded p-1 mt-1">' . PHP_EOL;
    if (isset($user_badge))
    {
      echo '<span class="' . $user_badge_class . '">' . $user_badge . '</span>';
    }
    echo
        '<div class="w-64px h-64px border rounded-circle p-1 mb-0-5 m-md-0 mr-md-1">' . PHP_EOL .
          '<i class="fas fa-user fa-2x m-auto"></i>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="media-body">' . PHP_EOL .
          '<p class="text-center text-md-left font-weight-bold m-0">' . $user->login . '</p>' . PHP_EOL .
          '<small class="d-block text-center text-md-left text-muted">Zarejestrowano ' . $user->registration_date->format('d.m.Y') . '</small>' . PHP_EOL .
        '</div>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
  else
  {
    echo '<div class="p-0-5 m-0-5 border rounded">Nie udało się wyświetlić użytkownika</div>';
  }
?>
