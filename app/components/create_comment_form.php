<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  $errors = array();

  if (empty($photo_id))
  {
    if (isset($_GET['photo_id']))
    {
      $photo_id = $_GET['photo_id'];
    }
    else
    {
      $errors[] = 'Nie zdefiniowano ID zdjęcia!';
    }
  }

  if (count($errors) == 0)
  {
    echo
      '<div class="rounded border bg-light my-1-5 p-1-5">' . PHP_EOL .
        '<h3 class="text-center mb-1-5">Dodaj komentarz</h3>' . PHP_EOL;
    if (isset($_SESSION['user_id']))
    {
      echo
        '<form action="' . BACKEND_URL . 'comment/create.php" method="post">' . PHP_EOL .
          '<input type="hidden" name="photo_id" value="' . $photo_id . '">' . PHP_EOL .
          '<input type="hidden" name="user_id" value="' . $_SESSION['user_id'] . '">' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="comment">Komentarz</label>' . PHP_EOL .
            '<textarea id="comment" class="form-control" name="comment" rows="3" placeholder="Komentarz">' . PHP_EOL;
      if (isset($_SESSION['create_comment_form_comment']))
      {
        echo $_SESSION['create_comment_form_comment'];
        unset($_SESSION['create_comment_form_comment']);
      }
      echo
            '</textarea>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="text-center">' . PHP_EOL .
            '<div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">' . PHP_EOL .
              '<button id="create_comment_form_button" class="btn btn-primary" tabindex="-1" type="submit" disabled>Dodaj komentarz</button>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</form>' . PHP_EOL;
  }
  else
  {
    $_SESSION['target_url'] = get_url() . '#comment';
    echo
      '<div class="text-center">' . PHP_EOL .
        '<h5 class="card-title mb-1">Przed dodaniem komentarza należy się zalogować</h5>' . PHP_EOL .
        '<p class="mb-0">' . PHP_EOL .
          '<span class="card-text text-muted">Jesteś tu po raz pierwszy?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?view=register&redirect=1">Stwórz nowe konto!</a>' . PHP_EOL .
        '</p>' . PHP_EOL .
        '<p class="mb-0">' . PHP_EOL .
          '<span class="card-text text-muted">Posiadasz już konto?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?view=login&redirect=1">Zaloguj się!</a>' . PHP_EOL .
        '</p>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
  echo
      '</div>' . PHP_EOL .
      '<script src="https://cdnjs.cloudflare.com/ajax/libs/xregexp/3.2.0/xregexp-all.min.js"></script>' . PHP_EOL .
      '<script>loadScript("' . ASSETS_URL . 'javascripts/validation.js");</script>' . PHP_EOL .
      '<script>loadScript("' . ASSETS_URL . 'javascripts/validation_create_comment_form.js");</script>' . PHP_EOL;
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Nie można wyświetlić formularza dodawania komentarzy, gdyż:</h5>' . PHP_EOL .
      '<ul class="mb-0">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $_SESSION['alert'] .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $_SESSION['alert'] .= '</ul>' . PHP_EOL;
  }
?>
