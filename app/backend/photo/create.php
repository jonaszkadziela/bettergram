<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  require_once BACKEND_PATH . 'photo/functions.php';

  $description = isset($_POST['description']) ? $_POST['description'] : null;
  $photo = isset($_FILES['photo']) ? $_FILES['photo'] : null;
  $album_id = isset($_POST['album_id']) ? $_POST['album_id'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  $_SESSION['create_photo_form']['description'] = sanitize_text($description);

  if (!validate_request('post', [$photo, $album_id, $user_id]))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  $errors = [];

  if (!empty($description))
  {
    if (!preg_match('/^.{0,255}$/m', $description))
    {
      $errors[] = 'Opis zdjęcia nie może przekraczać 255 znaków!';
    }
    else if (!preg_match('/^(?=.*[^\s]).+$/m', $description))
    {
      $errors[] = 'Opis zdjęcia musi posiadać przynajmniej jeden znak spoza białych znaków!';
    }
  }

  $allowed_photo_extensions = explode(',', ALLOWED_PHOTO_EXTENSIONS);
  $file_info = new finfo(FILEINFO_MIME_TYPE);
  $photo_ext = str_replace('image/', '', $file_info->file($photo['tmp_name']));

  if ($photo['error'] === UPLOAD_ERR_INI_SIZE || $photo['size'] > $upload_max_filesize)
  {
    $errors[] = 'Przesłany plik jest za duży! Wielkość pliku nie może przekraczać ' . UPLOAD_MAX_FILESIZE . 'B.';
  }
  else if ($photo['error'] === UPLOAD_ERR_NO_FILE)
  {
    $errors[] = 'Nie wybrano pliku do przesłania!';
  }
  else if ($photo['error'] !== UPLOAD_ERR_OK)
  {
    $errors[] = 'Wystąpił błąd podczas przesyłania pliku!';
  }
  else if (!in_array($photo_ext, $allowed_photo_extensions))
  {
    $allowed_photo_extensions_formatted = strtoupper(implode(', ', $allowed_photo_extensions));
    $errors[] = 'Nieprawidłowe rozszerzenie przesłanego pliku lub przesłany plik jest uszkodzony! Dozwolone rozszerzenia to: ' . $allowed_photo_extensions_formatted . '.';
  }

  try
  {
    $db = Database::get_instance();
    $result = $db->prepared_select_query('SELECT user_id FROM albums WHERE id = ?;', [$album_id]);

    if ($result && count($result) > 0)
    {
      if ($user_id != $result[0]['user_id'])
      {
        $errors[] = 'Nie można dodać zdjęcia, gdyż nie masz uprawnień do tego albumu!';
      }
    }
    else
    {
      $errors[] = 'Nie można dodać zdjęcia, gdyż album o takim ID nie istnieje!';
    }

    if (count($errors) == 0)
    {
      if (!check_recaptcha($recaptcha))
      {
        header('Location: ' . get_referrer_url());
        exit();
      }

      $date = (new DateTime())->format('Y-m-d H:i:s');
      $verified = 0;

      $db->prepared_query('INSERT INTO photos(id, description, date, verified, album_id) VALUES(NULL, ?, ?, ?, ?);', [$description, $date, $verified, $album_id]);

      $photo_id = $db->insert_id;
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

          if (resize_photo($target, $target, PHOTO_SIZE, PHOTO_SIZE))
          {
            if (!resize_photo($target, $photo_thumbnail_path, PHOTO_THUMBNAIL_SIZE, PHOTO_THUMBNAIL_SIZE))
            {
              $_SESSION['notice'][] = 'Nie udało się utworzyć miniaturki zdjęcia! W wolnej chwili, poinformuj o tym administrację.';
            }
            $_SESSION['notice'][] = 'Proces dodawania zdjęcia zakończony pomyślnie!';
            unset($_SESSION['create_photo_form']);

            header('Location: ' . get_redirect_url());
            exit();
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
    }

    if (count($errors) > 0)
    {
      $db->prepared_query('DELETE FROM photos WHERE id = ?;', [$photo_id]);

      if (file_exists($target))
      {
        unlink($target);
      }
    }
  }
  catch (Exception $e)
  {
    $_SESSION['alert'][] =
      '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
      '<p class="m-0">Nie udało się dodać zdjęcia! Przepraszamy za niedogodności.</p>' . PHP_EOL;
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
