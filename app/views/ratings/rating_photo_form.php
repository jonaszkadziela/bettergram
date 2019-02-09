<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();

  if (isset($_SESSION['current_user']['id']))
  {
    $photo_rating_form_errors = [];

    if (empty($photo_id))
    {
      if (isset($_GET['photo_id']))
      {
        $photo_id = $_GET['photo_id'];
      }
      else
      {
        $photo_rating_form_errors[] = 'Nie zdefiniowano ID zdjęcia!';
      }
    }

    if (count($photo_rating_form_errors) == 0)
    {
      $rating = null;
      $query =
        'SELECT
          pr.rating AS rating_rating
        FROM
          photos_ratings AS pr
        JOIN photos AS p
        ON
          pr.photo_id = p.id
        JOIN users AS u
        ON
          pr.user_id = u.id
        WHERE
          p.id = ? AND u.id = ?;';
      $result = $db->prepared_select_query($query, [$photo_id, $_SESSION['current_user']['id']]);

      if ($result && count($result) > 0)
      {
        $rating = new Rating
        (
          $result[0]['rating_rating'],
          $photo_id,
          $_SESSION['current_user']['id']
        );
      }

      echo
        '<div id="photo_rating" class="js-rating rating rounded border bg-light my-0-5 p-1">' . PHP_EOL .
          '<h4 class="js-rating-title text-center m-0">' . ($rating != null ? 'Twoja ocena' : 'Oceń to zdjęcie') . '</h4>' . PHP_EOL .
          '<form action="' . BACKEND_URL . 'rating/" method="post">' . PHP_EOL .
            '<input type="hidden" name="photo_id" value="' . $photo_id . '">' . PHP_EOL;
      for ($i = 0; $i < MAX_RATING; $i++)
      {
        echo
            ($i % 5 == 0 ? '<div class="d-inline-block">' . PHP_EOL : '') .
            '<span class="js-star fa-stack fa-lg w-32px h-32px text-dark rating__star"' . (($rating != null && $i < $rating->rating) ? ' data-star-selected="true"' : '') . '>' . PHP_EOL .
              '<i class="js-star-fill fas fa-star fa-stack-lg rating__star-inner' . (($rating != null && $i < $rating->rating) ? ' rating__star--selected' : '') . '"></i>' . PHP_EOL .
              '<i class="js-star-border far fa-star fa-stack-1x rating__star-inner' . (($rating != null && $i < $rating->rating) ? ' rating__star--selected' : '') . '"></i>' . PHP_EOL .
            '</span>' . PHP_EOL .
            (($i % 5 == 4 || $i == MAX_RATING - 1) ? '</div>' . PHP_EOL : '');
      }
      echo
          '</form>' . PHP_EOL .
          '<div class="js-rating-response">' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }

    if (count($photo_rating_form_errors) > 0)
    {
      echo
        '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
          '<h5>Nie można wyświetlić formularza dodawania oceny, gdyż:</h5>' . PHP_EOL .
          '<ul class="list-unstyled m-0">' . PHP_EOL;
      for ($i = 0; $i < count($photo_rating_form_errors); $i++)
      {
        echo '<li>' . $photo_rating_form_errors[$i] . '</li>' . PHP_EOL;
      }
      echo
          '</ul>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
  }
?>
