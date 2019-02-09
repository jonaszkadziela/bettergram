<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'rating/functions.php');

  if (isset($_SESSION['user_id']))
  {
    $user_id = $_SESSION['user_id'];
    $errors = array();

    if (empty($photo_id))
    {
      if (isset($_GET['photo_id']))
      {
        $photo_id = $_GET['photo_id'];
      }
      else
      {
        $errors[] = 'Nie zdefiniowano ID zdjęcia!';
      }
    }

    if (count($errors) == 0)
    {
      $user_rating = get_rating($photo_id, $user_id);

      echo
        '<div id="photo_rating" class="js-rating rating rounded border bg-light my-0-5 p-1">' . PHP_EOL .
          '<h4 class="js-rating-title text-center m-0">' . ($user_rating != null ? 'Twoja ocena' : 'Oceń to zdjęcie') . '</h4>' . PHP_EOL .
          '<form action="' . BACKEND_URL . 'rating/" method="post">' . PHP_EOL .
            '<input type="hidden" name="photo_id" value="' . $photo_id . '">' . PHP_EOL .
            '<input type="hidden" name="user_id" value="' . $user_id . '">' . PHP_EOL;
      for ($i = 0; $i < MAX_RATING; $i++)
      {
        echo
            ($i % 5 == 0 ? '<div class="d-inline-block">' : '') . PHP_EOL .
            '<span class="js-star fa-stack fa-lg w-32px h-32px text-dark rating__star"' . (($user_rating != null && $i < $user_rating->rating) ? ' data-star-selected="true"' : '') . '>' . PHP_EOL .
              '<i class="js-star-fill fas fa-star fa-stack-lg rating__star-inner' . (($user_rating != null && $i < $user_rating->rating) ? ' rating__star--selected' : '') . '"></i>' . PHP_EOL .
              '<i class="js-star-border far fa-star fa-stack-1x rating__star-inner' . (($user_rating != null && $i < $user_rating->rating) ? ' rating__star--selected' : '') . '"></i>' . PHP_EOL .
            '</span>' . PHP_EOL .
            (($i % 5 == 4 || $i == MAX_RATING - 1) ? '</div>' : '') . PHP_EOL;
      }
      echo
          '</form>' . PHP_EOL .
          '<div class="js-rating-response">' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<script>loadScript("' . ASSETS_URL . 'javascripts/rating.js");</script>' . PHP_EOL .
        '<script>loadScript("' . ASSETS_URL . 'javascripts/rating_photo.js");</script>' . PHP_EOL;
    }

    if (count($errors) > 0)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Nie można wyświetlić formularza dodawania oceny, gdyż:</h5>' . PHP_EOL .
        '<ul class="mb-0">' . PHP_EOL;
      for ($i = 0; $i < count($errors); $i++)
      {
        $_SESSION['alert'] .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
      }
      $_SESSION['alert'] .= '</ul>' . PHP_EOL;
    }
  }
?>
