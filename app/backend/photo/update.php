<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $description = isset($_POST['description']) ? $_POST['description'] : null;
  $verified = isset($_POST['verified']) ? $_POST['verified'] : null;
  $delete_photo = isset($_POST['delete_photo']) ? $_POST['delete_photo'] : null;
  $mode = isset($_POST['mode']) ? $_POST['mode'] : null;
  $photo_id = isset($_POST['photo_id']) ? $_POST['photo_id'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  $mode = filter_var($mode, FILTER_SANITIZE_STRING);

  $_SESSION['update_photo_form']['description'] = sanitize_text($description);

  if (!validate_request('post', [$photo_id, $user_id]))
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
        p.description AS photo_description,
        p.date AS photo_date,
        p.verified AS photo_verified,
        p.album_id AS photo_album_id,
        a.user_id AS album_user_id
      FROM
        photos AS p
      JOIN albums AS a
      ON
        p.album_id = a.id
      WHERE
        p.id = ?;';
    $result = $db->prepared_select_query($query, [$photo_id]);

    if ($result && count($result) > 0)
    {
      if ($user_id != $result[0]['album_user_id'])
      {
        $report_error = true;
        if (!is_null($mode))
        {
          switch ($mode)
          {
            case 'privileged':
              if (has_enough_permissions('moderator'))
              {
                $report_error = false;
              }
            break;
          }
        }
        if ($report_error)
        {
          $errors[] = 'Nie masz uprawnień do tego zdjęcia!';
        }
      }

      if (count($errors) == 0)
      {
        if (!check_recaptcha($recaptcha))
        {
          header('Location: ' . get_referrer_url());
          exit();
        }

        if (filter_var($delete_photo, FILTER_VALIDATE_BOOLEAN))
        {
          $db->prepared_query('DELETE FROM photos WHERE id = ?;', [$photo_id]);
          $db->prepared_query('DELETE FROM photos_comments WHERE photo_id = ?;', [$photo_id]);
          $db->prepared_query('DELETE FROM photos_ratings WHERE photo_id = ?;', [$photo_id]);

          $allowed_photo_extensions = explode(',', ALLOWED_PHOTO_EXTENSIONS);
          $target = CONTENT_PATH . 'albums/album_' . $album_id . '/'. 'photo_' . $photo_id;
          $photos = glob($target . '*');

          for ($i = 0; $i < count($photos); $i++)
          {
            $photo_array = explode('.', $photos[$i]);
            $photo_name = basename($photos[$i]);
            $photo_ext = end($photo_array);

            if (in_array($photo_ext, $allowed_photo_extensions))
            {
              if (!file_exists($photos[$i]) || !unlink($photos[$i]))
              {
                $errors[] = 'Nie udało się usunąć zdjęcia!';
                break;
              }
            }
          }
          if (count($errors) == 0)
          {
            $_SESSION['notice'][] = 'Proces usuwania zdjęcia zakończony pomyślnie!';
            unset($_SESSION['update_photo_form']);

            header('Location: ' . get_redirect_url());
            exit();
          }
        }
        else
        {
          $fields = [];
          if ($description != $result[0]['photo_description'])
          {
            if (!preg_match('/^.{0,255}$/m', $description))
            {
              $errors[] = 'Opis zdjęcia nie może przekraczać 255 znaków!';
            }
            else if (!preg_match('/^(?=.*[^\s]).+$/m', $description))
            {
              $errors[] = 'Opis zdjęcia musi posiadać przynajmniej jeden znak spoza białych znaków!';
            }
            else
            {
              $fields['description'] = $description;
            }
          }
          if ((bool)$verified != (bool)$result[0]['photo_verified'] && $mode == 'privileged')
          {
            if (has_enough_permissions('moderator'))
            {
              $fields['verified'] = filter_var($verified, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            else
            {
              $errors[] = 'Nie masz wystarczających uprawnień, aby zmienić status tego zdjęcia!';
            }
          }

          if (count($errors) == 0)
          {
            if (count($fields) > 0)
            {
              $inner = [];
              $args = [];
              foreach ($fields as $key => $value)
              {
                $inner[] = $key . ' = ?';
                $args[] = $value;
              }
              $query = 'UPDATE photos SET ' . implode($inner, ', ') . ' WHERE id = ?;';
              $args[] = $photo_id;

              $db->prepared_query($query, $args);

              $_SESSION['notice'][] = 'Proces edycji zdjęcia zakończony pomyślnie!';
              unset($_SESSION['update_photo_form']);
            }
            else
            {
              $_SESSION['notice'][] = 'Nie wprowadzono żadnych zmian!';
            }
          }
        }
      }
    }
    else
    {
      $errors[] = 'Nie znaleziono zdjęcia o podanym ID!';
    }
  }
  catch (Exception $e)
  {
    $_SESSION['alert'][] =
      '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
      '<p class="m-0">Nie udało się zedytować zdjęcia! Przepraszamy za niedogodności.</p>' . PHP_EOL;
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
