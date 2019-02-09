<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  $errors = array();

  if (empty($album_id))
  {
    if (isset($_GET['album_id']))
    {
      $album_id = $_GET['album_id'];
    }
    else
    {
      $errors[] = 'Nie zdefiniowano ID albumu!';
    }
  }

  if (count($errors) == 0)
  {
    $allowed_photo_extensions = explode(',', ALLOWED_PHOTO_EXTENSIONS);
    $allowed_photo_extensions_formatted = strtoupper(implode(', ', $allowed_photo_extensions));

    echo
      '<div class="rounded border bg-light my-1-5 p-1-5">' . PHP_EOL .
        '<h3 class="text-center mb-1-5">Dodaj zdjęcie do albumu</h3>' . PHP_EOL .
        '<form action="' . BACKEND_URL . 'photo/create.php" method="post" enctype="multipart/form-data">' . PHP_EOL .
          '<input type="hidden" name="album_id" value="' . $album_id . '">' . PHP_EOL .
          '<label for="photo">Zdjęcie</label>' . PHP_EOL .
          '<div class="custom-file mb-1">' . PHP_EOL .
            '<input id="photo" class="js-file-input custom-file-input" name="photo" type="file" data-placeholder="Wybierz plik...">' . PHP_EOL .
            '<label class="custom-file-label" for="photo">Wybierz plik...</label>' . PHP_EOL .
            '<small class="form-text text-muted">(maksymalna wielkość zdjęcia: ' . UPLOAD_MAX_FILESIZE . 'B, dozwolone rozszerzenia pliku: ' . $allowed_photo_extensions_formatted . ')</small>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="form-group mt-0-5">' . PHP_EOL .
            '<label for="description">Opis zdjęcia</label>' . PHP_EOL .
            '<textarea id="description" class="form-control" name="description" rows="3" placeholder="Opis zdjęcia">' . PHP_EOL;
    if (isset($_SESSION['create_photo_form_description']))
    {
      echo $_SESSION['create_photo_form_description'];
      unset($_SESSION['create_photo_form_description']);
    }
    echo
            '</textarea>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="text-center">' . PHP_EOL .
            '<div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">' . PHP_EOL .
              '<button id="create_photo_form_button" class="btn btn-primary" tabindex="-1" type="submit" disabled>Dodaj zdjęcie</button>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</form>' . PHP_EOL .
      '</div>' . PHP_EOL .
      '<script src="https://cdnjs.cloudflare.com/ajax/libs/xregexp/3.2.0/xregexp-all.min.js"></script>' . PHP_EOL .
      '<script>loadScript("' . ASSETS_URL . 'javascripts/validation.js");</script>' . PHP_EOL .
      '<script>loadScript("' . ASSETS_URL . 'javascripts/validation_create_photo_form.js");</script>' . PHP_EOL;
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Nie można wyświetlić formularza dodawania zdjęć, gdyż:</h5>' . PHP_EOL .
      '<ul class="mb-0">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $_SESSION['alert'] .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $_SESSION['alert'] .= '</ul>' . PHP_EOL;
  }
?>
