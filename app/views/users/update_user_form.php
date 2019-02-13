<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $update_user_form_errors = [];

  if (empty($user))
  {
    $update_user_form_errors[] = 'Nie zdefiniowano użytkownika!';
  }

  if (count($update_user_form_errors) == 0)
  {
    echo '<div id="update_user_form" class="text-center">' . PHP_EOL;
    if (isset($update_user_form_mode))
    {
      echo
        '<h3 class="m-0">Zmień dane użytkownika</h3>' . PHP_EOL .
        '<h5 class="m-0">"' . $user->login . '"</h5>' . PHP_EOL;
    }
    else
    {
      echo '<h3 class="m-0">Zmień swoje dane</h3>' . PHP_EOL;
    }
    echo
        '<small class="d-block text-muted mb-1">(wypełnij tylko te pola, które chcesz zmodyfikować)</small>' . PHP_EOL .
        '<form class="text-left" action="' . BACKEND_URL . 'user/update.php" method="post">' . PHP_EOL .
          (isset($update_user_form_mode) ? '<input id="mode" type="hidden" name="mode" value="' . $update_user_form_mode . '">' . PHP_EOL : '') .
          '<input type="hidden" name="user_id" value="' . $user->id . '">' . PHP_EOL;
    if ($user->email == $_SESSION['current_user']['email'])
    {
      echo
          '<div class="d-flex flex-column flex-md-row form-group p-0-5 bg-white border rounded">' . PHP_EOL .
            '<div class="js-spinner-container w-64px h-64px position-relative mx-auto mb-0-25 m-md-0">' . PHP_EOL .
              '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-circle">' . PHP_EOL .
                '<i class="fas fa-spinner fa-2x fa-spin"></i>' . PHP_EOL .
              '</div>' . PHP_EOL .
              '<img class="w-100 h-100 border rounded-circle" src="#" data-src="' . get_gravatar_url($user->email) . '" alt="#">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="d-flex flex-column justify-content-center text-center text-md-left p-md-0-5">' . PHP_EOL .
              '<p class="m-0">Chcesz zmienić swoje zdjęcie profilowe?</p>' . PHP_EOL .
              '<a class="underline underline--narrow underline-primary underline-animation align-self-center align-self-md-start" href="https://gravatar.com/" target="_blank" rel="noreferrer">Przejdź do strony Gravatar.com</a>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL;
    }
    else
    {
      echo
          '<div class="form-group mb-md-0">' . PHP_EOL .
            '<div class="js-spinner-container w-64px h-64px position-relative mx-auto">' . PHP_EOL .
              '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-circle">' . PHP_EOL .
                '<i class="fas fa-spinner fa-2x fa-spin"></i>' . PHP_EOL .
              '</div>' . PHP_EOL .
              '<img class="w-100 h-100 border rounded-circle" src="#" data-src="' . get_gravatar_url($user->email) . '" alt="#">' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL;
    }
    echo
          '<div class="form-group">' . PHP_EOL .
            '<label for="email">Email</label>' . PHP_EOL .
            '<input id="email" class="form-control" name="email" type="text" placeholder="Email" value="';
    if (isset($_SESSION['update_user_form']['email']))
    {
      echo $_SESSION['update_user_form']['email'];
      unset($_SESSION['update_user_form']['email']);
    }
    else
    {
      echo $user->email;
    }
    echo
          '">' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="password1">Nowe hasło</label>' . PHP_EOL .
            '<input id="password1" class="form-control" name="password1" type="password" placeholder="Nowe hasło">' . PHP_EOL .
            '<small class="form-text text-muted">(od 6 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra)</small>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="password2">Potwierdź nowe hasło</label>' . PHP_EOL .
            '<input id="password2" class="form-control" name="password2" type="password" placeholder="Potwierdź nowe hasło">' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="form-group">' . PHP_EOL .
            '<label for="select_permissions">Uprawnienia</label>' . PHP_EOL .
            '<select id="select_permissions" class="custom-select" name="permissions"' .
            (isset($update_user_form_mode) && $update_user_form_mode == 'privileged' ? '' : ' disabled') .'>' . PHP_EOL .
              '<option value="użytkownik"' . ($user->permissions == 'użytkownik' ? ' selected' : '') . '>Zwykły użytkownik</option>' . PHP_EOL .
              '<option value="moderator"' . ($user->permissions == 'moderator' ? ' selected' : '') . '>Moderator</option>' . PHP_EOL .
              '<option value="administrator"' . ($user->permissions == 'administrator' ? ' selected' : '') . '>Administrator</option>' . PHP_EOL .
            '</select>' . PHP_EOL .
          '</div>' . PHP_EOL;
    if (isset($update_user_form_mode))
    {
      echo
          '<div class="form-group position-relative p-0-5 bg-white border rounded">' . PHP_EOL .
            '<div class="custom-control custom-checkbox">' . PHP_EOL .
              '<input id="active" class="custom-control-input" name="active" value="true" type="checkbox"' .
              (filter_var($user->active, FILTER_VALIDATE_BOOLEAN) ? ' checked' : '') . (isset($update_user_form_mode) && $update_user_form_mode == 'privileged' ? '' : ' disabled') . '>' . PHP_EOL .
              '<label class="custom-control-label" for="active">Aktywny użytkownik</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL;
    }
    echo
          '<div class="form-group p-0-5 bg-white border rounded">' . PHP_EOL .
            '<div class="custom-control custom-checkbox">' . PHP_EOL .
              '<input id="delete_user" class="js-confirmation-modal custom-control-input" name="delete_user" value="true" type="checkbox" data-target="#delete_user_modal">' . PHP_EOL .
              '<label class="custom-control-label" for="delete_user">' . (isset($update_user_form_mode) ? 'Usuń użytkownika' : 'Usuń swoje konto') . '</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div id="delete_user_modal" class="modal fade" tabindex="-1" role="dialog">' . PHP_EOL .
              '<div class="modal-dialog modal-dialog-centered" role="document">' . PHP_EOL .
                '<div class="modal-content">' . PHP_EOL .
                  '<div class="modal-header">' . PHP_EOL .
                    '<h5 class="modal-title">Czy na pewno chcesz usunąć ' . (isset($update_user_form_mode) ? 'tego użytkownika' : 'swoje konto') . '?</h5>' . PHP_EOL .
                    '<button class="close" type="button" data-dismiss="modal" aria-label="Close">' . PHP_EOL .
                      '<span aria-hidden="true">&times;</span>' . PHP_EOL .
                    '</button>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                  '<div class="modal-body">' . PHP_EOL .
                    '<p>Jeśli zdecydujesz się usunąć ' . (isset($update_user_form_mode) ? 'tego użytkownika' : 'swoje konto') . ', nie będzie możliwości odzyskania danych.</p>' . PHP_EOL .
                    '<p class="m-0">Dobrze przemyśl, czy na pewno chcesz to zrobić!</p>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                  '<div class="modal-footer">' . PHP_EOL .
                    '<button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Anuluj</button>' . PHP_EOL .
                    '<button class="btn btn-outline-danger" type="button" data-dismiss="modal" data-action="accept">Zatwierdź</button>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL .
              '</div>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL;
    if (empty($update_user_form_mode))
    {
      echo
          '<div class="form-group">' . PHP_EOL .
            '<label for="current_password">Aktualne hasło *</label>' . PHP_EOL .
            '<input id="current_password" class="form-control" name="current_password" type="password" placeholder="Aktualne hasło">' . PHP_EOL .
          '</div>' . PHP_EOL;
    }
    echo
          '<div class="text-center">' . PHP_EOL .
            (empty($update_user_form_mode) ? '<small class="d-block text-muted my-1">* - to pole jest obowiązkowe</small>' . PHP_EOL : '') .
            '<div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">' . PHP_EOL .
              '<button class="btn btn-primary" tabindex="-1" type="submit">Zapisz zmiany</button>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</form>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }

  if (count($update_user_form_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić formularza edycji użytkownika, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($update_user_form_errors); $i++)
    {
      echo '<li>' . $update_user_form_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
