<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $update_album_form_errors = [];

  if (empty($album))
  {
    $update_album_form_errors[] = 'Nie zdefiniowano albumu!';
  }

  if (count($update_album_form_errors) == 0)
  {
    echo
      '<div id="update_album_form" class="text-center">' . PHP_EOL .
        '<h3 class="m-0">Zedytuj album</h3>' . PHP_EOL .
        '<h5 class="m-0">"' . $album->title . '"</h5>' . PHP_EOL .
        '<small class="d-block text-muted mb-0-5">(wypełnij tylko te pola, które chcesz zmodyfikować)</small>' . PHP_EOL .
        '<form class="text-left" action="' . BACKEND_URL . 'album/update.php" method="post">' . PHP_EOL .
          (isset($update_album_form_mode) ? '<input type="hidden" name="mode" value="' . $update_album_form_mode . '">' . PHP_EOL : '') .
          '<input type="hidden" name="album_id" value="' . $album->id . '">' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="title">Tytuł albumu</label>' . PHP_EOL .
            '<input id="title" class="form-control" name="title" type="text" placeholder="Tytuł albumu" value="';
    if (isset($_SESSION['update_album_form']['title']))
    {
      echo $_SESSION['update_album_form']['title'];
      unset($_SESSION['update_album_form']['title']);
    }
    else
    {
      echo $album->title;
    }
    echo
            '">' . PHP_EOL .
            '<small class="form-text text-muted">(od 3 do 100 znaków, nie może zaczynać ani kończyć się spacją)</small>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="form-group p-0-5 bg-white border rounded">' . PHP_EOL .
            '<div class="custom-control custom-checkbox">' . PHP_EOL .
              '<input id="delete_album" class="js-confirmation-modal custom-control-input" name="delete_album" value="true" type="checkbox" data-target="#delete_album_modal">' . PHP_EOL .
              '<label class="custom-control-label" for="delete_album">Usuń album wraz z jego zawartością</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div id="delete_album_modal" class="modal fade" tabindex="-1" role="dialog">' . PHP_EOL .
              '<div class="modal-dialog modal-dialog-centered" role="document">' . PHP_EOL .
                '<div class="modal-content">' . PHP_EOL .
                  '<div class="modal-header">' . PHP_EOL .
                    '<h5 class="modal-title">Czy na pewno chcesz usunąć ten album?</h5>' . PHP_EOL .
                    '<button class="close" type="button" data-dismiss="modal" aria-label="Close">' . PHP_EOL .
                      '<span aria-hidden="true">&times;</span>' . PHP_EOL .
                    '</button>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                  '<div class="modal-body">' . PHP_EOL .
                    '<p>Jeśli zdecydujesz się usunąć ten album, nie będzie możliwości odzyskania danych.</p>' . PHP_EOL .
                    '<p class="m-0">Dobrze przemyśl, czy na pewno chcesz to zrobić!</p>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                  '<div class="modal-footer">' . PHP_EOL .
                    '<button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Anuluj</button>' . PHP_EOL .
                    '<button class="btn btn-outline-danger" type="button" data-dismiss="modal" data-action="accept">Zatwierdź</button>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL .
              '</div>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="text-center">' . PHP_EOL .
            '<div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">' . PHP_EOL .
              '<button id="submit" class="btn btn-primary" tabindex="-1" type="submit">Zapisz zmiany</button>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</form>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }

  if (count($update_album_form_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić formularza edycji albumu, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($update_album_form_errors); $i++)
    {
      echo '<li>' . $update_album_form_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
