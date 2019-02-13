<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $create_comment_form_errors = [];

  if (empty($photo_id))
  {
    if (isset($_GET['photo_id']))
    {
      $photo_id = $_GET['photo_id'];
    }
    else
    {
      $create_comment_form_errors[] = 'Nie zdefiniowano ID zdjęcia!';
    }
  }

  if (count($create_comment_form_errors) == 0)
  {
    echo
      '<div id="create_comment_form" class="text-cetner">' . PHP_EOL .
      '<h3 class="mb-1-5">Dodaj komentarz</h3>' . PHP_EOL;
    if (isset($_SESSION['current_user']['id']))
    {
      echo
        '<form class="text-left" action="' . BACKEND_URL . 'comment/create.php" method="post">' . PHP_EOL .
          '<input type="hidden" name="photo_id" value="' . $photo_id . '">' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="comment">Komentarz</label>' . PHP_EOL .
            '<textarea id="comment" class="js-expand-textarea form-control" name="comment" rows="3" data-min-rows="3" placeholder="Komentarz">' . PHP_EOL;
      if (isset($_SESSION['create_comment_form']['comment']))
      {
        echo $_SESSION['create_comment_form']['comment'];
        unset($_SESSION['create_comment_form']['comment']);
      }
      echo
            '</textarea>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="text-center">' . PHP_EOL .
            '<div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">' . PHP_EOL .
              '<button class="btn btn-primary" tabindex="-1" type="submit">Dodaj komentarz</button>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</form>' . PHP_EOL;
    }
    else
    {
      $_SESSION['target_url'] = get_url() . '#comment';
      echo
        '<h5 class="mb-1-5">Przed dodaniem komentarza należy się zalogować</h5>' . PHP_EOL .
        '<p class="m-0">' . PHP_EOL .
          '<span class="text-muted">Jesteś tu po raz pierwszy?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=register&redirect=1">Stwórz nowe konto!</a>' . PHP_EOL .
        '</p>' . PHP_EOL .
        '<p class="m-0">' . PHP_EOL .
          '<span class="text-muted">Posiadasz już konto?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=login&redirect=1">Zaloguj się!</a>' . PHP_EOL .
        '</p>' . PHP_EOL;
    }
    echo '</div>' . PHP_EOL;
  }

  if (count($create_comment_form_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić formularza dodawania komentarza, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($create_comment_form_errors); $i++)
    {
      echo '<li>' . $create_comment_form_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
