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
    echo '<div class="media position-relative flex-column flex-md-row align-items-center bg-white shadow-sm rounded p-1">' . PHP_EOL;
    if (isset($user_badge))
    {
      echo '<span class="' . $user_badge_class . '">' . $user_badge . '</span>';
    }
    echo
        '<div class="js-spinner-container w-64px h-64px position-relative flex-shrink-0 mb-0-5 m-md-0 mr-md-1">' . PHP_EOL .
          '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-circle">' . PHP_EOL .
            '<i class="fas fa-spinner fa-2x fa-spin"></i>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<img class="w-100 h-100 border rounded-circle" src="#" data-src="' . get_gravatar_url($user->email) . '" alt="#">' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="media-body flex-fill w-100">' . PHP_EOL .
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
