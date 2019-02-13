<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  if (isset($photo))
  {
    echo
      '<div class="js-spinner-container card d-block min-h-64px rounded-inherit">' . PHP_EOL .
        '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-inherit">' . PHP_EOL .
          '<i class="fas fa-spinner fa-3x fa-spin"></i>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="js-photo-lightgallery rounded-inherit">' . PHP_EOL .
          '<a class="d-block rounded-inherit" href="' . $photo->get_path() . '" data-sub-html=".caption">' . PHP_EOL .
            '<img class="max-w-100 max-h-800px m-auto rounded-inherit" src="#" data-src="' . $photo->get_path() . '" alt="Zdjęcie #' . $photo->id . '" draggable="false">' . PHP_EOL .
            '<div class="d-flex overlay overlay--translucent justify-content-center align-items-center text-white bg-black">' . PHP_EOL .
              '<i class="fas fa-search fa-3x"></i>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="d-none caption">' . PHP_EOL .
              '<span>' . (strlen($photo->description) > 0 ? $photo->description : 'To zdjęcie nie posiada opisu') . '</span>' . PHP_EOL .
              (isset($album) ? '<h3 class="m-0">' . $album->author . '</h3>' . PHP_EOL : '') .
            '</div>' . PHP_EOL .
          '</a>' . PHP_EOL .
        '</div>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
  else
  {
    echo '<div class="p-0-5 border rounded">Nie udało się wyświetlić zdjęcia</div>';
  }
?>
