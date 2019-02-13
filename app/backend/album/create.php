<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $title = isset($_POST['title']) ? $_POST['title'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  $_SESSION['create_album_form']['title'] = sanitize_text($title, false);

  if (!validate_request('post', [$title, $user_id]))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  $errors = [];

  if (!preg_match('/^.{3,100}$/m', $title))
  {
    $errors[] = 'Tytuł albumu musi posiadać od 3 do 100 znaków!';
  }
  else if (!preg_match('/^[\S].+[\S]$/m', $title))
  {
    $errors[] = 'Tytuł albumu nie może zaczynać ani kończyć się spacją!';
  }

  if (count($errors) == 0)
  {
    if (!check_recaptcha($recaptcha))
    {
      header('Location: ' . get_referrer_url());
      exit();
    }

    try
    {
      $date = (new DateTime())->format('Y-m-d H:i:s');

      $db = Database::get_instance();
      $db->prepared_query('INSERT INTO albums(id, title, date, user_id) VALUES(NULL, ?, ?, ?);', [$title, $date, $user_id]);

      $album_id = $db->insert_id;
      $folder_name = 'album_' . $album_id;
      $target = CONTENT_PATH . 'albums/' . $folder_name;

      if (!is_dir($target))
      {
        if (mkdir($target, 0700, true))
        {
          unset($_SESSION['create_album_form']);

          header('Location: ' . get_redirect_url());
          exit();
        }
        else
        {
          $errors[] = 'Nie udało się utworzyć albumu!';
        }
      }
      else
      {
        $errors[] = 'Istnieje już album o takim ID! Spróbuj jeszcze raz.';
      }

      if (count($errors) > 0)
      {
        $db->prepared_query('DELETE FROM albums WHERE id = ?;', [$album_id]);
      }
    }
    catch (Exception $e)
    {
      $_SESSION['alert'][] =
        '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
        '<p class="m-0">Nie udało się stworzyć albumu! Przepraszamy za niedogodności.</p>' . PHP_EOL;
      header('Location: ' . get_referrer_url());
      exit();
    }
  }

  if (count($errors) > 0)
  {
    $alert =
      '<h5>Wystąpiły następujące błędy:</h5>' . PHP_EOL .
      '<ul class="mb-0 pl-1-25">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $alert .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $alert .= '</ul>' . PHP_EOL;
    $_SESSION['alert'][] = $alert;
    header('Location: ' . get_referrer_url());
    exit();
  }
?>
