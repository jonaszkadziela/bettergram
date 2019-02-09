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
      '<div class="card min-h-64px rounded-bottom-0">' . PHP_EOL .
        '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-top">' . PHP_EOL .
          '<i class="fas fa-spinner fa-3x fa-spin"></i>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<img class="object-fit-contain w-100 max-h-800px flex-shrink-0 m-auto rounded-top" src="#" data-src="' . $photo->get_path() . '" alt="Zdjęcie #' . $photo->id . '">' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
  else
  {
    echo '<div class="p-0-5 border rounded">Nie udało się wyświetlić zdjęcia</div>';
  }
?>
