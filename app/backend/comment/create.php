<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');

  $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
  $_SESSION['create_comment_form_comment'] = $comment;

  if (!validate_request('post', array(empty($_POST['comment']), empty($_POST['photo_id']), empty($_POST['user_id']))))
  {
    $url = ROOT_URL . (isset($_POST['photo_id']) ? '?view=photo&photo_id=' . $_POST['photo_id'] : '');
    header('Location: ' . $url);
    exit();
  }

  $photo_id = $_POST['photo_id'];
  $user_id = $_POST['user_id'];
  $errors = array();

  if (!preg_match("/^.{1,500}$/mu", $comment))
  {
    $errors[] = 'Komentarz musi posiadać od 1 do 500 znaków!';
  }
  else if (!preg_match("/^[\p{L}\p{N}\p{P} ]{1,500}$/mu", $comment))
  {
    $errors[] = 'Komentarz nie może zawierać niedozwolonych znaków!';
  }

  if (count($errors) == 0)
  {
    require(ROOT_PATH . 'env.php');

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $date = date('Y-m-d H:i:s');
      $comment = htmlentities($comment, ENT_QUOTES, 'UTF-8');
      $verified = 0;

      if ($connection->query("INSERT INTO photos_comments VALUES ('$comment', '$date', '$verified', '$photo_id', '$user_id')"))
      {
        $_SESSION['alert'] = 'Proces dodawania komentarza zakończony pomyślnie!';
        $_SESSION['alert_class'] = 'alert-info';

        unset($_SESSION['create_comment_form_comment']);

        header('Location: ' . ROOT_URL . '?view=photo&photo_id=' . $photo_id);
        exit();
      }
      else
      {
        throw new Exception($connection->errno);
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się dodać komentarza! Przepraszamy za niedogodności.</p>' . PHP_EOL;
      header('Location: ' . ROOT_URL . '?view=photo&photo_id=' . $photo_id);
      exit();
    }
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Wystąpiły następujące błędy:</h5>' . PHP_EOL .
      '<ul class="mb-0">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $_SESSION['alert'] .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $_SESSION['alert'] .= '</ul>' . PHP_EOL;
    header('Location: ' . ROOT_URL . '?view=photo&photo_id=' . $photo_id);
    exit();
  }
?>
