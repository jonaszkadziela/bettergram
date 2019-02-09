<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();

  if (isset($photo_id))
  {
    $query =
      'SELECT
        ROUND(AVG(pr.rating), 1) AS rating_avarage,
        COUNT(pr.rating) AS rating_count
      FROM
        photos_ratings AS pr
      JOIN photos AS p
      ON
        pr.photo_id = p.id
      WHERE
        p.id = ?;';
    $result = $db->prepared_select_query($query, [$photo_id]);

    if ($result && count($result) > 0 && $result[0]['rating_count'] > 0)
    {
      $rating = new RatingAverage
      (
        $result[0]['rating_avarage'],
        $result[0]['rating_count'],
        $photo_id
      );
      echo
        '<i class="fas fa-star"></i>' . PHP_EOL .
        '<span>' .
          (float)$rating->average . '/' . MAX_RATING .
          ' (' . $rating->count . ' ' . polish_suffix('głos', $rating->count) . ')' .
        '</span>';
    }
    else
    {
      echo 'Brak ocen';
    }
  }
  else
  {
    echo '<div class="p-0-5 border rounded">Nie udało się wyświetlić oceny zdjęcia</div>';
  }
?>
