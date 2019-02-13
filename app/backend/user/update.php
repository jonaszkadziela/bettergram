<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $email = isset($_POST['email']) ? $_POST['email'] : null;
  $password1 = isset($_POST['password1']) ? $_POST['password1'] : null;
  $password2 = isset($_POST['password2']) ? $_POST['password2'] : null;
  $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : null;
  $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : null;
  $active = isset($_POST['active']) ? $_POST['active'] : null;
  $delete_user = isset($_POST['delete_user']) ? $_POST['delete_user'] : null;
  $mode = isset($_POST['mode']) ? $_POST['mode'] : null;
  $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
  $current_user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  $_SESSION['update_user_form']['email'] = sanitize_text($email);

  $required_params = $mode == 'privileged' ? [$current_user_id] : [$current_password, $current_user_id];

  if (!validate_request('post', $required_params, $recaptcha))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  $errors = [];

  try
  {
    $db = Database::get_instance();
    $query =
      'SELECT
        u.email AS user_email,
        u.password AS user_password,
        u.permissions AS user_permissions,
        u.active AS user_active
      FROM
        users AS u
      WHERE
        u.id = ?;';
    $result = $db->prepared_select_query($query, [$user_id]);

    if ($result && count($result) > 0)
    {
      if ($current_user_id != $user_id)
      {
        $report_error = true;
        if (!is_null($mode))
        {
          switch ($mode)
          {
            case 'privileged':
              if (has_enough_permissions('administrator'))
              {
                $report_error = false;
              }
            break;
          }
        }
        if ($report_error)
        {
          $errors[] = 'Nie masz uprawnień do tego użytkownika!';
        }
      }
      if (is_null($mode))
      {
        if (!password_verify($current_password, $result[0]['user_password']))
        {
          $errors[] = 'Wprowadzone aktualne hasło jest nieprawidłowe!';
        }
      }
    }
    else
    {
      $errors[] = 'Nie znaleziono użytkownika o podanym ID!';
    }

    if (count($errors) == 0)
    {
      if (!check_recaptcha($recaptcha))
      {
        header('Location: ' . get_referrer_url());
        exit();
      }

      if (filter_var($delete_user, FILTER_VALIDATE_BOOLEAN))
      {
        $query =
          'SELECT
            GROUP_CONCAT(DISTINCT p.id) AS photo_ids,
            GROUP_CONCAT(DISTINCT a.id) AS albums_ids
          FROM
            users AS u
          JOIN albums AS a
          ON
            u.id = a.user_id
          JOIN photos AS p
          ON
            a.id = p.album_id
          WHERE
            u.id = ?;';
        $result2 = $db->prepared_select_query($query, [$user_id]);

        if ($result2 && count($result2) > 0)
        {
          $db->prepared_query('DELETE FROM users WHERE id = ?;', [$user_id]);
          $db->prepared_query('DELETE FROM photos_comments WHERE user_id = ?;', [$user_id]);
          $db->prepared_query('DELETE FROM photos_ratings WHERE user_id = ?;', [$user_id]);

          if (!is_null($result2[0]['photo_ids']))
          {
            $db->prepared_query('DELETE FROM photos WHERE id IN (' . $result2[0]['photo_ids'] . ');');
          }

          if (!is_null($result2[0]['albums_ids']))
          {
            $db->prepared_query('DELETE FROM albums WHERE id IN (' . $result2[0]['albums_ids'] . ');');
            $album_ids = explode(',', $result2[0]['albums_ids']);

            foreach ($album_ids as $album_id)
            {
              $album_path = CONTENT_PATH . 'albums/album_' . $album_id;
              if (!rmdir_recursive($album_path))
              {
                $errors[] = 'Nie udało się usunąć zawartości albumu!';
                break;
              }
            }
          }

          if (count($errors) == 0)
          {
            unset($_SESSION['update_user_form']);
            if ($current_user_id == $user_id)
            {
              $_SESSION['notice'][] = 'Proces usuwania twojego konta zakończony pomyślnie!';
              unset($_SESSION['current_user']);

              header('Location: ' . ROOT_URL);
              exit();
            }
            else
            {
              $_SESSION['notice'][] = 'Proces usuwania użytkownika zakończony pomyślnie!';

              header('Location: ' . get_redirect_url());
              exit();
            }
          }
        }
        else
        {
          $errors[] = 'Nie udało się usunąć konta użytkownika!';
        }
      }
      else
      {
        $fields = [];
        if ($email != $result[0]['user_email'])
        {
          $email = strtolower($email);
          $email_filtered = filter_var($email, FILTER_SANITIZE_EMAIL);
          if (!filter_var($email_filtered, FILTER_VALIDATE_EMAIL) || $email_filtered != $email)
          {
            $errors[] = 'Podany adres email jest nieprawidłowy!';
          }
          if (count($errors) == 0)
          {
            $result2 = $db->prepared_select_query('SELECT id FROM users WHERE email = ?;', [$email]);

            if ($result2 && count($result2) > 0)
            {
              $errors[] = 'Istnieje już konto przypisane do tego adresu email!';
            }
            else
            {
              $fields['email'] = $email;
            }
          }
        }
        if (!empty($password1) || !empty($password2))
        {
          if ($password1 != $password2)
          {
            $errors[] = 'Podane nowe hasła nie są identyczne!';
          }
          else if ($password1 == $current_password)
          {
            $errors[] = 'Nowe hasło musi się różnić od aktualnego hasła!';
          }
          else if (!preg_match('/^.{6,20}$/m', $password1))
          {
            $errors[] = 'Nowe hasło musi posiadać od 6 do 20 znaków!';
          }
          else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m', $password1))
          {
            $errors[] = 'Nowe hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!';
          }
          else
          {
            $fields['password'] = password_hash($password1, PASSWORD_DEFAULT);
          }
        }
        if (!is_null($permissions))
        {
          if (has_enough_permissions('administrator'))
          {
            if ($permissions != $result[0]['user_permissions'])
            {
              $fields['permissions'] = $permissions;
            }
          }
          else
          {
            $errors[] = 'Nie masz wystarczających uprawnień, aby zmienić uprawnienia tego użytkownika!';
          }
        }
        if ((bool)$active != (bool)$result[0]['user_active'] && $mode == 'privileged')
        {
          if (has_enough_permissions('administrator'))
          {
            $fields['active'] = filter_var($active, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
          }
          else
          {
            $errors[] = 'Nie masz wystarczających uprawnień, aby zmienić status tego użytkownika!';
          }
        }

        if (count($errors) == 0)
        {
          if (count($fields) > 0)
          {
            foreach ($fields as $key => $value)
            {
              $inner[] = $key . ' = ?';
              $args[] = $value;
            }
            $query = 'UPDATE users SET ' . implode($inner, ', ') . ' WHERE id = ?;';
            $args[] = $user_id;

            $db->prepared_query($query, $args);

            if ($current_user_id == $user_id)
            {
              $_SESSION['current_user']['email'] = isset($fields['email']) ? $fields['email'] : $_SESSION['current_user']['email'];
            }

            $_SESSION['notice'][] = 'Proces zmiany danych użytkownika zakończony pomyślnie!';
            unset($_SESSION['update_user_form']);
          }
          else
          {
            $_SESSION['notice'][] = 'Nie wprowadzono żadnych zmian!';
          }
        }
      }
    }
  }
  catch (Exception $e)
  {
    $_SESSION['alert'][] =
      '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
      '<p class="m-0">Nie udało się zmienić danych użytkownika! Przepraszamy za niedogodności.</p>' . PHP_EOL;
  }

  if (count($errors) > 0)
  {
    $alert =
      '<h5>Zmiany nie zostały zapisane, gdyż:</h5>' . PHP_EOL .
      '<ul class="mb-0 pl-1-25">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $alert .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $alert .= '</ul>' . PHP_EOL;
    $_SESSION['alert'][] = $alert;
  }

  header('Location: ' . get_referrer_url());
  exit();
?>
