<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'shared/classes.php');

  function get_comments($photo_id, $sql_where = null)
  {
    require(ROOT_PATH . 'env.php');

    $comments_array = array();

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query("SELECT * FROM photos_comments WHERE photo_id='$photo_id'");
      if ($sql_where != null)
      {
        $result = $connection->query("SELECT * FROM photos_comments WHERE photo_id='$photo_id' AND ($sql_where)");
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
          $comments_array[] = new Comment
          (
            $row['comment'],
            $row['date'],
            $row['verified'],
            $row['photo_id'],
            $row['user_id']
          );
        }
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się wczytać komentarzy! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $comments_array;
  }
?>
