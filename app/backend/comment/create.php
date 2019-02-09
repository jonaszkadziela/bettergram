<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $comment = isset($_POST['comment']) ? $_POST['comment'] : null;
  $photo_id = isset($_POST['photo_id']) ? $_POST['photo_id'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;

  $_SESSION['create_comment_form']['comment'] = sanitize_text($comment);

  if (!validate_request('post', [$comment, $photo_id, $user_id]))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  $errors = [];

  if (!preg_match('/^.{1,500}$/m', $comment))
  {
    $errors[] = 'Komentarz musi posiadać od 1 do 500 znaków!';
  }
  else if (!preg_match('/^(?=.*[^\s]).+$/m', $comment))
  {
    $errors[] = 'Komentarz musi posiadać przynajmniej jeden znak spoza białych znaków!';
  }

  try
  {
    $db = Database::get_instance();
    $query =
      'SELECT
        a.user_id AS album_user_id,
        p.verified AS photo_verified
      FROM
        albums AS a
      JOIN photos AS p
      ON
        a.id = p.album_id
      WHERE
        p.id = ?;';
    $result = $db->prepared_select_query($query, [$photo_id]);

    if ($result && count($result) > 0)
    {
      if ($result[0]['photo_verified'] != 1 && $result[0]['album_user_id'] != $user_id)
      {
        $errors[] = 'Nie można dodać komentarza, gdyż zdjęcie nie zostało jeszcze zaakceptowane!';
      }
    }
    else
    {
      $errors[] = 'Nie można dodać komentarza, gdyż zdjęcie o takim ID nie istnieje!';
    }

    if (count($errors) == 0)
    {
      $date = (new DateTime())->format('Y-m-d H:i:s');
      $verified = 0;

      $db->prepared_query('INSERT INTO photos_comments(id, comment, date, verified, photo_id, user_id) VALUES(NULL, ?, ?, ?, ?, ?);', [$comment, $date, $verified, $photo_id, $user_id]);

      $_SESSION['notice'][] = 'Proces dodawania komentarza zakończony pomyślnie!';
      unset($_SESSION['create_comment_form']);

      header('Location: ' . get_redirect_url());
      exit();
    }
  }
  catch (Exception $e)
  {
    $_SESSION['alert'][] =
      '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
      '<p class="m-0">Nie udało się dodać komentarza! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    header('Location: ' . get_referrer_url());
    exit();
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
