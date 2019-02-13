<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $photo_container_class = isset($photo_container_class) ? $photo_container_class : 'card w-180px h-180px shadow rounded-inherit';
  $photo_badge_class = isset($photo_badge_class) ? $photo_badge_class : 'badge badge-pill badge-primary position-absolute position-top-right px-0-5 py-0-25 m--0-5';

  if (isset($photo))
  {
    echo '<div class="js-spinner-container ' . $photo_container_class . '">' . PHP_EOL;
    if (isset($photo_badge))
    {
      echo '<span class="' . $photo_badge_class . '">' . $photo_badge . '</span>';
    }
    echo
        '<div class="js-spinner h-100 d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-inherit">' . PHP_EOL .
          '<i class="fas fa-spinner fa-3x fa-spin"></i>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="h-100 rounded-inherit">' . PHP_EOL .
          '<img class="w-100 h-100 object-fit-cover rounded-inherit" src="#" data-src="' . $photo->get_path('thumbnail') . '" alt="Miniatura zdjęcia #' . $photo->id . '">' . PHP_EOL .
          '<div class="overlay overlay--translucent bg-black"></div>' . PHP_EOL .
        '</div>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
  else
  {
    echo '<div class="p-0-5 border rounded">Nie udało się wyświetlić miniaturki zdjęcia</div>';
  }
?>
