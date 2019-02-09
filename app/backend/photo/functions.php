<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'shared/classes.php');

  function get_photo($photo_id = null, $album_id = null, $sql_where = null)
  {
    require(ROOT_PATH . 'env.php');

    $photo = null;

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      if ($photo_id != null)
      {
        $result = $connection->query("SELECT * FROM photos WHERE id='$photo_id'");
        if ($sql_where != null)
        {
          $result = $connection->query("SELECT * FROM photos WHERE id='$photo_id' AND ($sql_where)");
        }
      }
      if ($album_id != null)
      {
        $result = $connection->query("SELECT * FROM photos WHERE album_id='$album_id' LIMIT 1");
        if ($sql_where != null)
        {
          $result = $connection->query("SELECT * FROM photos WHERE album_id='$album_id' AND ($sql_where) LIMIT 1");
        }
      }

      if (!$result)
      {
        throw new Exception($connection->errno);
      }
      if ($result->num_rows > 0)
      {
        $row = $result->fetch_assoc();
        $photo = new Photo
        (
          $row['id'],
          $row['description'],
          $row['date'],
          $row['verified'],
          $row['album_id']
        );
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się wczytać zdjęć! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $photo;
  }

  function get_photos($album_id, $sql_where = null)
  {
    require(ROOT_PATH . 'env.php');

    $photos_array = array();

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query("SELECT * FROM photos WHERE album_id='$album_id'");
      if ($sql_where != null)
      {
        $result = $connection->query("SELECT * FROM photos WHERE album_id='$album_id' AND ($sql_where)");
      }

      if (!$result)
      {
        throw new Exception($connection->errno);
      }
      if ($result->num_rows > 0)
      {
        for ($i = 0; $i < $result->num_rows; $i++)
        {
          $row = $result->fetch_assoc();
          $photos_array[] = new Photo
          (
            $row['id'],
            $row['description'],
            $row['date'],
            $row['verified'],
            $row['album_id']
          );
        }
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się wczytać zdjęć! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $photos_array;
  }

  function resize_photo($photo_path, $target_path, $target_width, $target_height, $upscale_smaller = false)
  {
    if (!file_exists($photo_path))
    {
      return false;
    }

    $photo_path_array = explode('/', str_replace('\\', '/', $photo_path));
    $photo_full_name = end($photo_path_array);
    $path = implode('/', array_diff($photo_path_array, array($photo_full_name)));

    $photo_full_name_array = explode('.', $photo_full_name);
    $photo_name = strtolower($photo_full_name_array[0]);
    $photo_extension = strtolower(end($photo_full_name_array));

    switch ($photo_extension)
    {
      case 'jpg':
      case 'jpeg':
        $old_photo = imagecreatefromjpeg($photo_path);
      break;

      case 'png':
        $old_photo = imagecreatefrompng($photo_path);
      break;

      case 'gif':
        $old_photo = imagecreatefromgif($photo_path);
      break;

      default:
        return false;
    }

    if (!$old_photo)
    {
      return false;
    }

    $old_width = imagesx($old_photo);
    $old_height = imagesy($old_photo);

    if (!$upscale_smaller)
    {
      if ($old_width < $target_width)
      {
        $target_width = $old_width;
      }
      if ($old_height < $target_height)
      {
        $target_height = $old_height;
      }
    }
    if ($old_width > $old_height)
    {
      $new_width = $target_width;
      $new_height = $old_height / $old_width * $target_width;
    }
    else if ($old_width < $old_height)
    {
      $new_width = $old_width / $old_height * $target_height;
      $new_height = $target_height;
    }
    else
    {
      $new_width = $target_width;
      $new_height = $target_height;
    }

    $new_photo = imagecreatetruecolor($new_width, $new_height);

    if (!$new_photo)
    {
      return false;
    }

    $white = imagecolorallocate($new_photo, 255, 255, 255);
    imagefill($new_photo, 0, 0, $white);
    imagecopyresampled($new_photo, $old_photo, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

    switch ($photo_extension)
    {
      case 'jpg':
      case 'jpeg':
        $result = imagejpeg($new_photo, $target_path, 80);
      break;

      case 'png':
        $result = imagepng($new_photo, $target_path, 8);
      break;

      case 'gif':
        $result = imagegif($new_photo, $target_path);
      break;

      default:
        return false;
    }

    imagedestroy($new_photo);
    imagedestroy($old_photo);

    return $result;
  }
?>
