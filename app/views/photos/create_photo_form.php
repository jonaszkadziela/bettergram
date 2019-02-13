<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $create_photo_form_errors = [];

  if (empty($album_id))
  {
    if (isset($_GET['album_id']))
    {
      $album_id = $_GET['album_id'];
    }
    else
    {
      $create_photo_form_errors[] = 'Nie zdefiniowano ID albumu!';
    }
  }

  if (count($create_photo_form_errors) == 0)
  {
    $allowed_photo_extensions = explode(',', ALLOWED_PHOTO_EXTENSIONS);
    $allowed_photo_extensions_formatted = strtoupper(implode(', ', $allowed_photo_extensions));

    echo
      '<div id="create_photo_form" class="text-center">' . PHP_EOL .
        '<h3 class="mb-1-5">Dodaj zdjęcie do albumu</h3>' . PHP_EOL .
        '<form class="text-left" action="' . BACKEND_URL . 'photo/create.php" method="post" enctype="multipart/form-data">' . PHP_EOL .
          '<input type="hidden" name="album_id" value="' . $album_id . '">' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="photo">Zdjęcie *</label>' . PHP_EOL .
            '<div class="custom-file">' . PHP_EOL .
              '<input id="photo" class="js-file-input custom-file-input" name="photo" type="file" data-placeholder="Wybierz plik...">' . PHP_EOL .
              '<label class="custom-file-label" for="photo">Wybierz plik...</label>' . PHP_EOL .
              '<small class="form-text text-muted">(maksymalna wielkość zdjęcia: ' . UPLOAD_MAX_FILESIZE . 'B, dozwolone rozszerzenia pliku: ' . $allowed_photo_extensions_formatted . ')</small>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="description">Opis zdjęcia</label>' . PHP_EOL .
            '<textarea id="description" class="js-expand-textarea form-control" name="description" rows="3" data-min-rows="3" placeholder="Opis zdjęcia">' . PHP_EOL;
    if (isset($_SESSION['create_photo_form']['description']))
    {
      echo $_SESSION['create_photo_form']['description'];
      unset($_SESSION['create_photo_form']['description']);
    }
    echo
            '</textarea>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="text-center">' . PHP_EOL .
            '<small class="d-block text-muted my-1">* - to pole jest obowiązkowe</small>' . PHP_EOL .
            '<div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">' . PHP_EOL .
              '<button class="btn btn-primary" tabindex="-1" type="submit">Dodaj zdjęcie</button>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</form>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }

  if (count($create_photo_form_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić formularza dodawania zdjęcia, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($create_photo_form_errors); $i++)
    {
      echo '<li>' . $create_photo_form_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
