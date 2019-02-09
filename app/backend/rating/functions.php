<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'shared/classes.php');

  function get_rating($photo_id, $user_id)
  {
    require(ROOT_PATH . 'env.php');

    $rating = null;

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query("SELECT * FROM photos_ratings WHERE photo_id='$photo_id' AND user_id='$user_id'");

      if (!$result)
      {
        throw new Exception($connection->errno);
      }
      if ($result->num_rows > 0)
      {
        $row = $result->fetch_assoc();
        $rating = new Rating
        (
          $row['rating'],
          $row['photo_id'],
          $row['user_id']
        );
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się wczytać ocen! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $rating;
  }

  function get_ratings($photo_id, $sql_where = null)
  {
    require(ROOT_PATH . 'env.php');

    $ratings_array = array();

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query("SELECT * FROM photos_ratings WHERE photo_id='$photo_id'");
      if ($sql_where != null)
      {
        $result = $connection->query("SELECT * FROM photos_ratings WHERE photo_id='$photo_id' AND ($sql_where)");
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
          $ratings_array[] = new Rating
          (
            $row['rating'],
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
        '<p class="mb-0">Nie udało się wczytać ocen! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $ratings_array;
  }

  function calculate_rating($photo_id, $precision = 1)
  {
    $rating = null;
    $ratings = get_ratings($photo_id);

    if (count($ratings) > 0)
    {
      $ratings_sum = 0;
      for ($i = 0; $i < count($ratings); $i++)
      {
        $ratings_sum += $ratings[$i]->rating;
      }
      $rating_calculated = round($ratings_sum / count($ratings), $precision);
      $rating = array('rating' => $rating_calculated, 'rating_count' => count($ratings));
    }
    return $rating;
  }

  function render_rating($photo_id, $precision = 1)
  {
    $rating = calculate_rating($photo_id, $precision);

    if ($rating != null)
    {
      $result =
        '<i class="fas fa-star"></i>' . PHP_EOL .
        '<span>' .
          $rating['rating'] . '/' . MAX_RATING .
          ' (' . $rating['rating_count'] . ' głos' . polish_suffix($rating['rating_count'], 'm') . ')' .
        '</span>';
    }
    else
    {
      $result = 'Brak ocen';
    }
    return $result . PHP_EOL;
  }
?>
