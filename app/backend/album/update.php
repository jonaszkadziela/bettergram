<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $title = isset($_POST['title']) ? $_POST['title'] : null;
  $delete_album = isset($_POST['delete_album']) ? $_POST['delete_album'] : null;
  $mode = isset($_POST['mode']) ? $_POST['mode'] : null;
  $album_id = isset($_POST['album_id']) ? $_POST['album_id'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  $mode = filter_var($mode, FILTER_SANITIZE_STRING);

  $_SESSION['update_album_form']['title'] = sanitize_text($title, false);

  if (!validate_request('post', [$album_id, $user_id]))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  $errors = [];

  try
  {
    $db = Database::get_instance();
    $result = $db->prepared_select_query('SELECT title, date, user_id FROM albums WHERE id = ?;', [$album_id]);

    if ($result && count($result) > 0)
    {
      if ($user_id != $result[0]['user_id'])
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
          $errors[] = 'Nie masz uprawnień do tego albumu!';
        }
      }

      if (count($errors) == 0)
      {
        if (!check_recaptcha($recaptcha))
        {
          header('Location: ' . get_referrer_url());
          exit();
        }

        if (filter_var($delete_album, FILTER_VALIDATE_BOOLEAN))
        {
          $result2 = $db->prepared_select_query('SELECT GROUP_CONCAT(DISTINCT p.id) AS photo_ids FROM albums AS a JOIN photos AS p ON a.id = p.album_id WHERE a.id = ?;', [$album_id]);

          if ($result2 && count($result2) > 0)
          {
            $db->prepared_query('DELETE FROM albums WHERE id = ?;', [$album_id]);
            $db->prepared_query('DELETE FROM photos WHERE album_id = ?;', [$album_id]);

            if (!is_null($result2[0]['photo_ids']))
            {
              $db->prepared_query('DELETE FROM photos_comments WHERE photo_id IN (' . $result2[0]['photo_ids'] . ');');
              $db->prepared_query('DELETE FROM photos_ratings WHERE photo_id IN (' . $result2[0]['photo_ids'] . ');');

              $album_path = CONTENT_PATH . 'albums/album_' . $album_id;
              if (!rmdir_recursive($album_path))
              {
                $errors[] = 'Nie udało się usunąć zawartości albumu!';
              }
            }

            if (count($errors) == 0)
            {
              $_SESSION['notice'][] = 'Proces usuwania albumu zakończony pomyślnie!';
              unset($_SESSION['update_album_form']);

              header('Location: ' . get_redirect_url());
              exit();
            }
          }
          else
          {
            $errors[] = 'Nie udało się usunąć albumu!';
          }
        }
        else
        {
          $fields = [];
          if ($title != $result[0]['title'])
          {
            if (!preg_match('/^.{3,100}$/m', $title))
            {
              $errors[] = 'Tytuł albumu musi posiadać od 3 do 100 znaków!';
            }
            else if (!preg_match('/^[\S].+[\S]$/m', $title))
            {
              $errors[] = 'Tytuł albumu nie może zaczynać ani kończyć się spacją!';
            }
            else
            {
              $fields['title'] = $title;
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
              $query = 'UPDATE albums SET ' . implode($inner, ', ') . ' WHERE id = ?;';
              $args[] = $album_id;

              $db->prepared_query($query, $args);

              $_SESSION['notice'][] = 'Proces edycji albumu zakończony pomyślnie!';
              unset($_SESSION['update_album_form']);
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
      $errors[] = 'Nie znaleziono albumu o podanym ID!';
    }
  }
  catch (Exception $e)
  {
    $_SESSION['alert'][] =
      '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
      '<p class="m-0">Nie udało się zedytować albumu! Przepraszamy za niedogodności.</p>' . PHP_EOL;
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
