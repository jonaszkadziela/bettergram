<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');

  $title = isset($_POST['title']) ? $_POST['title'] : '';
  $_SESSION['create_album_form_title'] = $title;

  if (!validate_request('post', array(empty($_POST['title']), empty($_POST['user_id']))))
  {
    header('Location: ' . ROOT_URL . '?view=create_album');
    exit();
  }

  $user_id = $_POST['user_id'];
  $errors = array();

  if (!preg_match("/^.{3,100}$/mu", $title))
  {
    $errors[] = 'Tytuł albumu musi posiadać od 3 do 100 znaków!';
  }
  else if (!preg_match("/^[^\s].+[^\s]$/mu", $title))
  {
    $errors[] = 'Tytuł albumu nie może zaczynać ani kończyć się spacją!';
  }
  else if (!preg_match("/^[\p{L}\p{N}\p{P} ]{3,100}$/mu", $title))
  {
    $errors[] = 'Tytuł albumu nie może zawierać niedozwolonych znaków!';
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

      $title = htmlentities($title, ENT_QUOTES, 'UTF-8');
      $date = date('Y-m-d H:i:s');

      if ($connection->query("INSERT INTO albums VALUES (NULL, '$title', '$date', '$user_id')"))
      {
        $album_id = $connection->insert_id;
        $folder_name = 'album_' . $album_id;
        $target = CONTENT_PATH . '/albums/' . $folder_name;

        if (!is_dir($target))
        {
          if (mkdir($target, 0700, true))
          {
            unset($_SESSION['create_album_form_title']);

            header('Location: ' . ROOT_URL . '?view=create_photo');
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
          if (!$connection->query("DELETE FROM albums WHERE id='$album_id'"))
          {
            throw new Exception($connection->errno);
          }
        }
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
        '<p class="mb-0">Nie udało się stworzyć albumu! Przepraszamy za niedogodności.</p>' . PHP_EOL;
      header('Location: ' . ROOT_URL . '?view=create_album');
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
    header('Location: ' . ROOT_URL . '?view=create_album');
    exit();
  }
?>
