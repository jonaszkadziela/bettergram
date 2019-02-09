<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $comment = isset($_POST['comment']) ? $_POST['comment'] : null;
  $verified = isset($_POST['verified']) ? $_POST['verified'] : null;
  $delete_comment = isset($_POST['delete_comment']) ? $_POST['delete_comment'] : null;
  $mode = isset($_POST['mode']) ? $_POST['mode'] : null;
  $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;

  $mode = filter_var($mode, FILTER_SANITIZE_STRING);

  $_SESSION['update_comment_form']['comment'] = sanitize_text($comment, false);

  if (!validate_request('post', [$comment_id, $user_id]))
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
        pc.id AS comment_id,
        pc.comment AS comment_comment,
        pc.date AS comment_date,
        pc.verified AS comment_verified,
        pc.photo_id AS comment_photo_id,
        pc.user_id AS comment_user_id
      FROM
        photos_comments AS pc
      WHERE
        pc.id = ?;';
    $result = $db->prepared_select_query($query, [$comment_id]);

    if ($result && count($result) > 0)
    {
      if ($user_id != $result[0]['comment_user_id'])
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
          $errors[] = 'Nie masz uprawnień do tego komentarza!';
        }
      }

      if (count($errors) == 0)
      {
        if (filter_var($delete_comment, FILTER_VALIDATE_BOOLEAN))
        {
          $db->prepared_query('DELETE FROM photos_comments WHERE id = ?;', [$comment_id]);

          $_SESSION['notice'][] = 'Proces usuwania komentarza zakończony pomyślnie!';
          unset($_SESSION['update_comment_form']);

          header('Location: ' . get_redirect_url());
          exit();
        }
        else
        {
          $fields = [];
          if ($comment != trim($result[0]['comment_comment']))
          {
            if (!is_null($mode) && !has_enough_permissions('administrator'))
            {
              unset($_SESSION['update_comment_form']['comment']);
              $errors[] = 'Nie masz wystarczających uprawnień, aby zedytować treść tego komentarza!';
            }
            if (count($errors) == 0)
            {
              if (!preg_match('/^.{1,500}$/m', $comment))
              {
                $errors[] = 'Komentarz musi posiadać od 1 do 500 znaków!';
              }
              else if (!preg_match('/^(?=.*[^\s]).+$/m', $comment))
              {
                $errors[] = 'Komentarz musi posiadać przynajmniej jeden znak spoza białych znaków!';
              }
              else
              {
                $fields['comment'] = $comment;
              }
            }
          }
          if ((bool)$verified != (bool)$result[0]['comment_verified'] && $mode == 'privileged')
          {
            if (has_enough_permissions('moderator'))
            {
              $fields['verified'] = filter_var($verified, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            else
            {
              $errors[] = 'Nie masz wystarczających uprawnień, aby zmienić status tego komentarza!';
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
              $query = 'UPDATE photos_comments SET ' . implode($inner, ', ') . ' WHERE id = ?;';
              $args[] = $comment_id;

              $db->prepared_query($query, $args);

              $_SESSION['notice'][] = 'Proces edycji komentarza zakończony pomyślnie!';
              unset($_SESSION['update_comment_form']);
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
      $errors[] = 'Nie znaleziono komentarza o podanym ID!';
    }
  }
  catch (Exception $e)
  {
    $_SESSION['alert'][] =
      '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
      '<p class="m-0">Nie udało się zedytować komentarza! Przepraszamy za niedogodności.</p>' . PHP_EOL;
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
