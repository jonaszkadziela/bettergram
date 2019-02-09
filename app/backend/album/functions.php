<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'shared/classes.php');
  require_once(BACKEND_PATH . 'photo/functions.php');

  function get_album($album_id, $include_photos = false)
  {
    require(ROOT_PATH . 'env.php');

    $album = null;

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query("SELECT * FROM albums WHERE id='$album_id'");
      if (!$result)
      {
        throw new Exception($connection->errno);
      }
      if ($result->num_rows > 0)
      {
        $row = $result->fetch_assoc();
        $album = new Album
        (
          $row['id'],
          $row['title'],
          $row['date'],
          $row['user_id']
        );
        if ($include_photos)
        {
          $album->photos = get_photos($album->id);
        }
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się wczytać albumu! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $album;
  }

  function get_albums($user_id = null, $include_photos = false)
  {
    require(ROOT_PATH . 'env.php');

    $albums_array = array();

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query("SELECT * FROM albums");
      if ($user_id != null)
      {
        $result = $connection->query("SELECT * FROM albums WHERE user_id='$user_id'");
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
          $albums_array[] = new Album
          (
            $row['id'],
            $row['title'],
            $row['date'],
            $row['user_id']
          );
          if ($include_photos)
          {
            $albums_array[$i]->photos = get_photos($albums_array[$i]->id);
          }
        }
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się wczytać albumów! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $albums_array;
  }
?>
