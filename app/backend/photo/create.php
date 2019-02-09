<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  require_once(BACKEND_PATH . 'photo/functions.php');

  $description = isset($_POST['description']) ? $_POST['description'] : '';
  $_SESSION['create_photo_form_description'] = $description;

  if (!validate_request('post', array(empty($_POST['album_id']), empty($_FILES['photo']))))
  {
    header('Location: ' . ROOT_URL . '?view=create_photo');
    exit();
  }

  $album_id = $_POST['album_id'];
  $errors = array();

  if (!preg_match("/^.{0,255}$/mu", $description))
  {
    $errors[] = 'Opis zdjęcia nie może przekraczać 255 znaków!';
  }
  else if (!preg_match("/^[\p{L}\p{N}\p{P} ]{0,255}$/mu", $description))
  {
    $errors[] = 'Opis zdjęcia nie może zawierać niedozwolonych znaków!';
  }

  $allowed_photo_extensions = explode(',', ALLOWED_PHOTO_EXTENSIONS);
  $upload_max_filesize = formatted_size_to_bytes(UPLOAD_MAX_FILESIZE);

  $photo = $_FILES['photo'];
  $photo_ext = explode('.', $photo['name']);
  $photo_ext = strtolower(end($photo_ext));

  if ($photo['error'] !== 0)
  {
    $errors[] = 'Wystąpił błąd podczas przesyłania pliku!';
  }
  else if (!in_array($photo_ext, $allowed_photo_extensions))
  {
    $allowed_photo_extensions_formatted = strtoupper(implode(', ', $allowed_photo_extensions));
    $errors[] = 'Nieprawidłowe rozszerzenie przesłanego pliku! Dozwolone rozszerzenia to: ' . $allowed_photo_extensions_formatted . '.';
  }
  else if ($photo['size'] > $upload_max_filesize)
  {
    $errors[] = 'Przesłany plik jest za duży! Wielkość pliku nie może przekraczać ' . UPLOAD_MAX_FILESIZE . 'B.';
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

      $description = htmlentities($description, ENT_QUOTES, 'UTF-8');
      $date = date('Y-m-d H:i:s');
      $verified = 0;

      if ($connection->query("INSERT INTO photos VALUES (NULL, '$description', '$date', '$verified', '$album_id')"))
      {
        $photo_id = $connection->insert_id;
        $album_path = CONTENT_PATH . 'albums/album_' . $album_id;
        $target = $album_path . '/photo_' . $photo_id . '.' . $photo_ext;
        $photo_thumbnail_path = $album_path . '/photo_' . $photo_id . '_thumbnail.' . $photo_ext;

        if (!is_dir($album_path))
        {
          mkdir($album_path, 0700, true);
        }
        if (is_dir($album_path))
        {
          if (!file_exists($target))
          {
            move_uploaded_file($photo['tmp_name'], $target);

            if (resize_photo($target, $target, 1200, 1200))
            {
              if (resize_photo($target, $photo_thumbnail_path, 400, 400))
              {
                $_SESSION['alert'] = 'Proces dodawania zdjęcia zakończony pomyślnie!';
                $_SESSION['alert_class'] = 'alert-info';

                unset($_SESSION['create_photo_form_description']);

                header('Location: ' . ROOT_URL . '?view=create_photo&album_id=' . $album_id);
                exit();
              }
              else
              {
                $errors[] = 'Nie udało się przetworzyć zdjęcia! Spróbuj jeszcze raz.';
              }
            }
            else
            {
              $errors[] = 'Nie udało się przetworzyć zdjęcia! Spróbuj jeszcze raz.';
            }
          }
          else
          {
            $errors[] = 'Istnieje już zdjęcie o takim ID! Spróbuj jeszcze raz.';
          }
        }
        else
        {
          $errors[] = 'Nie ma albumu o takim ID!';
        }

        if (count($errors) > 0)
        {
          if ($connection->query("DELETE FROM photos WHERE id='$photo_id'"))
          {
            if (file_exists($target))
            {
              unlink($target);
            }
          }
          else
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
        '<p class="mb-0">Nie udało się dodać zdjęcia! Przepraszamy za niedogodności.</p>' . PHP_EOL;
      header('Location: ' . ROOT_URL . '?view=create_photo&album_id=' . $album_id);
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
    header('Location: ' . ROOT_URL . '?view=create_photo&album_id=' . $album_id);
    exit();
  }
?>
