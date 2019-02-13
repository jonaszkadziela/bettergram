<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $album_badge_class = isset($album_badge_class) ? $album_badge_class : 'badge badge-pill badge-primary position-absolute position-top-right px-0-5 py-0-25 m--0-5';

  if (isset($album))
  {
    echo
      '<div class="card w-180px h-100 shadow">' . PHP_EOL .
        '<div class="h-180px position-relative rounded-top">' . PHP_EOL;
    if (isset($album_badge))
    {
      echo '<span class="' . $album_badge_class . '">' . $album_badge . '</span>';
    }
    if (count($album->photos) > 0)
    {
      $photo = $album->photos[0];
      $photo_container_class = 'w-180px max-w-100 h-180px rounded-inherit';

      include VIEWS_PATH . 'photos/render_photo_thumbnail.php';
    }
    else
    {
      echo
          '<div class="d-flex justify-content-center align-items-center overlay bg-dark text-light rounded-top">' . PHP_EOL .
            '<i class="fas fa-images fa-5x"></i>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="overlay overlay--translucent bg-black"></div>' . PHP_EOL;
    }
    echo
        '</div>' . PHP_EOL .
        '<div class="d-flex flex-column justify-content-center card-body border-top">' . PHP_EOL .
          '<p class="text-center m-0">' . truncate($album->title, 40) . '</p>' . PHP_EOL .
        '</div>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
  else
  {
    echo '<div class="p-0-5 border rounded">Nie udało się wyświetlić albumu</div>';
  }
?>
