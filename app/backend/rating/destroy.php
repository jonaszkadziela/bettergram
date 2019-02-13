<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  header('Content-Type: application/json');

  $photo_id = isset($_POST['photo_id']) ? $_POST['photo_id'] : null;
  $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  if (!validate_request('post', [$photo_id, $user_id]))
  {
    echo 'Wystąpił błąd podczas usuwania oceny! Spróbuj ponownie później.';
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
        $errors[] = 'Nie można usunąć oceny, gdyż zdjęcie nie zostało jeszcze ocenione!';
      }
    }
    else
    {
      $errors[] = 'Nie można usunąć oceny, gdyż zdjęcie o takim ID nie istnieje!';
    }

    if (count($errors) == 0)
    {
      if (!check_recaptcha($recaptcha, true))
      {
        echo 'Weryfikacja reCAPTCHA zakończona niepowodzeniem! Spróbuj ponownie później.';
        exit();
      }

      $db->prepared_query('DELETE FROM photos_ratings WHERE photo_id = ? AND user_id = ?;', [$photo_id, $user_id]);

      $response =
      [
        'responseText' => 'Pomyślnie usunięto ocenę!',
        'ratingResult' => get_php_output(VIEWS_PATH . 'ratings/render_rating_photo.php', ['photo_id' => $photo_id])
      ];
      echo json_encode($response);
      http_response_code(200);
      exit();
    }
  }
  catch (Exception $e)
  {
    $errors[] = 'Wystąpił błąd #' . $e->getmessage() . ' podczas usuwania oceny! Spróbuj ponownie później.';
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
