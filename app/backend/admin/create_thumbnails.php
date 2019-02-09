<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  require_once BACKEND_PATH . 'photo/functions.php';

  $user_permissions = isset($_SESSION['current_user']['permissions']) ? $_SESSION['current_user']['permissions'] : null;

  if (!validate_request('get', [$user_permissions]))
  {
    header('Location: ' . ROOT_URL);
    exit();
  }

  if (empty($_SESSION['create_thumbnails_errors']))
  {
    $_SESSION['create_thumbnails_errors'] = [];
  }

  if (!has_enough_permissions('administrator'))
  {
    $_SESSION['create_thumbnails_errors'][] = 'Twoje konto posiada niewystarczające uprawnienia, aby kontynuować!';
  }

  if (count($_SESSION['create_thumbnails_errors']) == 0)
  {
    $max_processed_photos_at_once = 20;
    $processed_photos_count = 0;
    $albums_path = CONTENT_PATH . 'albums/';
    $albums = glob($albums_path . '*', GLOB_ONLYDIR);

    if (empty($_SESSION['created_thumbnails']))
    {
      $_SESSION['created_thumbnails'] = [];
    }
    if (empty($_SESSION['skipped_photos_count']))
    {
      $_SESSION['skipped_photos_count'] = 0;
    }

    $i = isset($_GET['album']) ? $_GET['album'] : 0;
    for ($i; $i < count($albums); $i++)
    {
      $photos = array_values(array_diff(scandir($albums[$i]), ['.', '..']));

      $j = isset($_GET['photo']) ? $_GET['photo'] : 0;
      unset($_GET['photo']);

      for ($j; $j < count($photos); $j++)
      {
        $processed_photos_count++;

        if ($processed_photos_count > $max_processed_photos_at_once)
        {
          header('Location: ' . get_url(false) . modify_get_parameters(['album' => $i, 'photo' => $j]));
          exit();
        }

        $photo_array = explode('.', $photos[$j]);
        $photo_name = $photo_array[0];
        $photo_ext = end($photo_array);
        $photo_thumbnail_path = $albums[$i] . '/' . $photo_name . '_thumbnail.' . $photo_ext;

        if (!preg_match("/^[\S]+_[\d]+$/m", $photo_name) || isset($_GET['check_existing']) && file_exists($photo_thumbnail_path))
        {
          $_SESSION['skipped_photos_count']++;
          continue;
        }

        $target = $albums[$i] . '/' . $photos[$j];
        $width = isset($_GET['width']) ? $_GET['width'] : PHOTO_THUMBNAIL_SIZE;
        $height = isset($_GET['height']) ? $_GET['height'] : PHOTO_THUMBNAIL_SIZE;

        if (resize_photo($target, $photo_thumbnail_path, $width, $height))
        {
          $_SESSION['created_thumbnails'][] = basename($photo_thumbnail_path);
        }
        else
        {
          $_SESSION['create_thumbnails_errors'][] = 'Nie udało się przetworzyć zdjęcia ' . $photos[$j] . '! Spróbuj jeszcze raz.';
        }
      }
    }

    if (count($_SESSION['create_thumbnails_errors']) == 0)
    {
      $_SESSION['notice'][] =
        '<p class="h5 mb-0">Proces tworzenia miniaturek zdjęć został zakończony pomyślnie!</p>' . PHP_EOL .
        '<p class="m-0">Pominięto: ' . $_SESSION['skipped_photos_count'] . '.</p>' . PHP_EOL .
        (count($_SESSION['created_thumbnails']) > 0 ? '<p class="mt-0-5 mb-0">Utworzono ' . count($_SESSION['created_thumbnails']) . ' ' . polish_suffix('miniatur', count($_SESSION['created_thumbnails']), 'biernik') . ':</p><p class="m-0">' . implode(', ', $_SESSION['created_thumbnails']) . '</p>' : '') . PHP_EOL;
    }
  }

  if (count($_SESSION['create_thumbnails_errors']) > 0)
  {
    $alert =
      '<h5>Wystąpiły następujące błędy:</h5>' . PHP_EOL .
      '<ul class="mb-0 pl-1-25">' . PHP_EOL;
    for ($i = 0; $i < count($_SESSION['create_thumbnails_errors']); $i++)
    {
      $alert .= '<li>' . $_SESSION['create_thumbnails_errors'][$i] . '</li>' . PHP_EOL;
    }
    $alert .= '</ul>' . PHP_EOL;
    $_SESSION['alert'][] = $alert;
  }

  unset($_SESSION['created_thumbnails']);
  unset($_SESSION['skipped_photos_count']);
  unset($_SESSION['create_thumbnails_errors']);

  header('Location: ' . ROOT_URL);
  exit();
?>
