<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  require_once(BACKEND_PATH . 'rating/functions.php');

  header('Content-Type: application/json');

  if (!validate_request('post', array(empty($_POST['photo_id']), empty($_POST['user_id']))))
  {
    echo 'Wystąpił błąd podczas usuwania oceny! Spróbuj ponownie później.';
    http_response_code(500);
    exit();
  }

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

    if ($connection->query("DELETE FROM photos_ratings WHERE photo_id='$photo_id' AND user_id='$user_id'"))
    {
      $response = array('responseText' => 'Pomyślnie usunięto ocenę!', 'ratingResult' => render_rating($photo_id));
      echo json_encode($response);
      http_response_code(200);
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
    echo 'Wystąpił błąd #' . $e->getMessage() . ' podczas usuwania oceny! Spróbuj ponownie później.';
    http_response_code(500);
    exit();
  }
?>
