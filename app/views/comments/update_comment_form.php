<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $update_comment_form_errors = [];

  if (empty($photo))
  {
    $update_comment_form_errors[] = 'Nie zdefiniowano zdjęcia!';
  }
  if (empty($comment))
  {
    $update_comment_form_errors[] = 'Nie zdefiniowano komentarza!';
  }

  if (count($update_comment_form_errors) == 0)
  {
    echo
      '<div id="update_comment_form" class="text-center">' . PHP_EOL .
        '<h3 class="m-0">Zedytuj komentarz</h3>' . PHP_EOL .
        '<small class="d-block text-muted mb-1-5">(wypełnij tylko te pola, które chcesz zmodyfikować)</small>' . PHP_EOL .
        '<a class="d-block link--clean w-180px mx-auto mb-1-5 mb-md-0" href="' . ROOT_URL . '?page=photo&photo_id=' . $photo->id . '">' . PHP_EOL .
          '<div class="rounded">' . PHP_EOL;
    include VIEWS_PATH . 'photos/render_photo_thumbnail.php';
    echo
          '</div>' . PHP_EOL .
        '</a>' . PHP_EOL .
        '<form class="text-left" action="' . BACKEND_URL . 'comment/update.php" method="post">' . PHP_EOL .
          (isset($update_comment_form_mode) ? '<input type="hidden" name="mode" value="' . $update_comment_form_mode . '">' . PHP_EOL : '') .
          '<input type="hidden" name="comment_id" value="' . $comment->id . '">' . PHP_EOL;
    if (!empty((string)$comment->author->email))
    {
      echo
          '<div class="d-flex flex-column flex-md-row form-group p-0-5 mt-1-5 bg-white border rounded">' . PHP_EOL .
            '<div class="js-spinner-container w-64px h-64px position-relative mx-auto mb-0-25 m-md-0">' . PHP_EOL .
              '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-circle">' . PHP_EOL .
                '<i class="fas fa-spinner fa-2x fa-spin"></i>' . PHP_EOL .
              '</div>' . PHP_EOL .
              '<img class="w-100 h-100 border rounded-circle" src="#" data-src="' . get_gravatar_url($comment->author->email) . '" alt="#">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="d-flex flex-column justify-content-center text-center text-md-left p-md-0-5">' . PHP_EOL .
              '<p class="m-0">Ten komentarz został stworzony przez ' . $comment->author->login . '</p>' . PHP_EOL;
      if (has_enough_permissions('administrator'))
      {
        echo
              '<a class="underline underline--narrow underline-primary underline-animation align-self-center align-self-md-start" href="' .
              ROOT_URL . '?page=admin_panel&tab=users&user_id=' . $comment->author->id . '">Zedytuj konto ' . $comment->author->login . '</a>' . PHP_EOL;
      }
      echo
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL;
    }
    echo
          '<div class="form-group">' . PHP_EOL .
            '<label for="comment">Komentarz</label>' . PHP_EOL .
            '<textarea id="comment" class="js-expand-textarea form-control" name="comment" rows="3" data-min-rows="3" placeholder="Komentarz"' .
            (isset($update_comment_form_mode) && $update_comment_form_mode == 'privileged' && has_enough_permissions('administrator') ? '' : ' readonly') . '>' . PHP_EOL;
    if (isset($_SESSION['update_comment_form']['comment']))
    {
      echo $_SESSION['update_comment_form']['comment'];
      unset($_SESSION['update_comment_form']['comment']);
    }
    else
    {
      echo $comment->comment;
    }
    echo
            '</textarea>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="form-group position-relative p-0-5 bg-white border rounded">' . PHP_EOL .
            '<div class="custom-control custom-checkbox">' . PHP_EOL .
              '<input id="verified" class="custom-control-input" name="verified" value="true" type="checkbox"' .
              (filter_var($comment->verified, FILTER_VALIDATE_BOOLEAN) ? ' checked' : '') . (isset($update_comment_form_mode) && $update_comment_form_mode == 'privileged' ? '' : ' disabled') . '>' . PHP_EOL .
              '<label class="custom-control-label" for="verified">Zaakceptowany komentarz</label>' . PHP_EOL .
            '</div>' . PHP_EOL;
    if (!$comment->verified)
    {
      echo
        '<button class="js-prevent-default btn btn--clean position-absolute position-top-right m-0-25 p-0-25" type="button" data-toggle="tooltip" data-html="true" data-trigger="manual" ' .
        'title="Ten komentarz nie został jeszcze zaakceptowany, dlatego <u>nie jest widoczny</u> publicznie.">' . PHP_EOL .
          '<i class="fas fa-question-circle fa-lg fa-fw"></i>' . PHP_EOL .
        '</button>' . PHP_EOL;
    }
    echo
          '</div>' . PHP_EOL .
          '<div class="form-group p-0-5 bg-white border rounded">' . PHP_EOL .
            '<div class="custom-control custom-checkbox">' . PHP_EOL .
              '<input id="delete_comment" class="js-confirmation-modal custom-control-input" name="delete_comment" value="true" type="checkbox" data-target="#delete_comment_modal">' . PHP_EOL .
              '<label class="custom-control-label" for="delete_comment">Usuń komentarz</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div id="delete_comment_modal" class="modal fade" tabindex="-1" role="dialog">' . PHP_EOL .
              '<div class="modal-dialog modal-dialog-centered" role="document">' . PHP_EOL .
                '<div class="modal-content">' . PHP_EOL .
                  '<div class="modal-header">' . PHP_EOL .
                    '<h5 class="modal-title">Czy na pewno chcesz usunąć ten komentarz?</h5>' . PHP_EOL .
                    '<button class="close" type="button" data-dismiss="modal" aria-label="Close">' . PHP_EOL .
                      '<span aria-hidden="true">&times;</span>' . PHP_EOL .
                    '</button>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                  '<div class="modal-body">' . PHP_EOL .
                    '<p>Jeśli zdecydujesz się usunąć ten komentarz, nie będzie możliwości odzyskania danych.</p>' . PHP_EOL .
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

  if (count($update_comment_form_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić formularza edycji komentarza, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($update_comment_form_errors); $i++)
    {
      echo '<li>' . $update_comment_form_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
