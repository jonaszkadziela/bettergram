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
        '<small class="d-block text-muted mb-1">(wypełnij tylko te pola, które chcesz zmodyfikować)</small>' . PHP_EOL .
        '<form class="text-left" action="' . BACKEND_URL . 'album/update.php" method="post">' . PHP_EOL .
          (isset($update_album_form_mode) ? '<input type="hidden" name="mode" value="' . $update_album_form_mode . '">' . PHP_EOL : '') .
          '<input type="hidden" name="album_id" value="' . $album->id . '">' . PHP_EOL;
    if (!empty((string)$album->author->email))
    {
      echo
          '<div class="d-flex flex-column flex-md-row form-group p-0-5 bg-white border rounded">' . PHP_EOL .
            '<div class="js-spinner-container w-64px h-64px position-relative mx-auto mb-0-25 m-md-0">' . PHP_EOL .
              '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-circle">' . PHP_EOL .
                '<i class="fas fa-spinner fa-2x fa-spin"></i>' . PHP_EOL .
              '</div>' . PHP_EOL .
              '<img class="w-100 h-100 border rounded-circle" src="#" data-src="' . get_gravatar_url($album->author->email) . '" alt="#">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="d-flex flex-column justify-content-center text-center text-md-left p-md-0-5">' . PHP_EOL .
              '<p class="m-0">Ten album został stworzony przez ' . $album->author->login . '</p>' . PHP_EOL;
      if (has_enough_permissions('administrator'))
      {
        echo
              '<a class="underline underline--narrow underline-primary underline-animation align-self-center align-self-md-start" href="' .
              ROOT_URL . '?page=admin_panel&tab=users&user_id=' . $album->author->id . '">Zedytuj konto ' . $album->author->login . '</a>' . PHP_EOL;
      }
      echo
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL;
    }
    echo
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
              '<button class="btn btn-primary" tabindex="-1" type="submit">Zapisz zmiany</button>' . PHP_EOL .
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
