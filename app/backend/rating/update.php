<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  require_once(BACKEND_PATH . 'rating/functions.php');

  header('Content-Type: application/json');

  if (!validate_request('post', array(empty($_POST['rating']), empty($_POST['photo_id']), empty($_POST['user_id']))))
  {
    echo 'Wystąpił błąd podczas zmieniania oceny! Spróbuj ponownie później.';
    http_response_code(500);
    exit();
  }

  $rating = $_POST['rating'];
  $photo_id = $_POST['photo_id'];
  $user_id = $_POST['user_id'];

  require(ROOT_PATH . 'env.php');

  try
  {
    $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

    if ($connection->connect_errno != 0)
    {
      throw new Exception($connection->connect_errno);
    }

    $connection->set_charset('utf8');

    $rating = clamp($rating, 1, MAX_RATING);

    $result = $connection->query("SELECT * FROM photos_ratings WHERE photo_id='$photo_id' AND user_id='$user_id'");
    if (!$result)
    {
      throw new Exception($connection->errno);
    }
    if ($result->num_rows == 1)
    {
      if ($connection->query("UPDATE photos_ratings SET rating='$rating' WHERE photo_id='$photo_id' AND user_id='$user_id'"))
      {
        $response = array('responseText' => 'Pomyślnie zmieniono ocenę!', 'ratingResult' => render_rating($photo_id));
        echo json_encode($response);
        http_response_code(200);
        exit();
      }
      else
      {
        throw new Exception($connection->errno);
      }
    }
    else
    {
      echo 'Wystąpił błąd podczas zmieniania oceny! Spróbuj ponownie później.';
      http_response_code(500);
      exit();
    }
    $result->close();
    $connection->close();
  }
  catch (Exception $e)
  {
    echo 'Wystąpił błąd #' . $e->getMessage() . ' podczas zmieniania oceny! Spróbuj ponownie później.';
    http_response_code(500);
    exit();
  }
?>
