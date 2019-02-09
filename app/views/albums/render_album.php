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
    echo '<div class="card w-180px h-100 shadow">' . PHP_EOL;
    if (isset($album_badge))
    {
      echo '<span class="' . $album_badge_class . '">' . $album_badge . '</span>';
    }
    if (count($album->photos) > 0)
    {
      echo
        '<div class="js-spinner h-180px d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-top">' . PHP_EOL .
          '<i class="fas fa-spinner fa-3x fa-spin"></i>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="h-180px position-relative rounded-top">' . PHP_EOL .
          '<img class="h-180px object-fit-cover card-img-top" src="#" data-src="' . $album->photos[0]->get_path('thumbnail') . '" alt="Okładka albumu #' . $album->id . '">' . PHP_EOL .
          '<div class="overlay overlay-block bg-black"></div>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
    else
    {
      echo
        '<div class="h-180px position-relative rounded-top">' . PHP_EOL .
          '<div class="d-flex justify-content-center align-items-center overlay bg-dark text-light rounded-top">' . PHP_EOL .
            '<i class="fas fa-images fa-5x"></i>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="overlay overlay-block bg-black"></div>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
    echo
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
