<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  header('Content-Type: application/json');

  $rating = isset($_POST['rating']) ? $_POST['rating'] : null;
  $photo_id = isset($_POST['photo_id']) ? $_POST['photo_id'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;

  $rating = filter_var($rating, FILTER_SANITIZE_NUMBER_INT);

  if (!validate_request('post', [$rating, $photo_id, $user_id]))
  {
    echo 'Wystąpił błąd podczas zmieniania oceny! Spróbuj ponownie później.';
    http_response_code(500);
    exit();
  }

  $errors = [];

  try
  {
    $db = Database::get_instance();
    $query =
      'SELECT
          pr.rating AS photo_rating
        FROM
          photos AS p
        LEFT JOIN photos_ratings AS pr
        ON
          p.id = pr.photo_id AND pr.user_id = ?
        WHERE
          p.id = ?;';
    $result = $db->prepared_select_query($query, [$user_id, $photo_id]);

    if ($result && count($result) > 0)
    {
      if (is_null($result[0]['photo_rating']))
      {
        $errors[] = 'Nie można zmienić oceny, gdyż zdjęcie nie zostało jeszcze ocenione!';
      }
    }
    else
    {
      $errors[] = 'Nie można zmienić oceny, gdyż zdjęcie o takim ID nie istnieje!';
    }

    if (count($errors) == 0)
    {
      $rating = clamp($rating, 1, MAX_RATING);
      $db->prepared_query('UPDATE photos_ratings SET rating = ? WHERE photo_id = ? AND user_id = ?;', [$rating, $photo_id, $user_id]);

      $response =
      [
        'responseText' => 'Pomyślnie zmieniono ocenę!',
        'ratingResult' => get_php_output(VIEWS_PATH . 'ratings/render_rating_photo.php', ['photo_id' => $photo_id])
      ];
      echo json_encode($response);
      http_response_code(200);
      exit();
    }
  }
  catch (Exception $e)
  {
    $errors[] = 'Wystąpił błąd #' . $e->getmessage() . ' podczas zmieniania oceny! Spróbuj ponownie później.';
  }

  if (count($errors) > 0)
  {
    echo '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      echo '<li>' . $errors[$i] . '</li>';
    }
    echo '</ul>' . PHP_EOL;
    http_response_code(500);
    exit();
  }
?>
