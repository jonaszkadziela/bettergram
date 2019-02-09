<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $photos_errors = [];

  if (empty($photos))
  {
    $photos_errors[] = 'Nie zdefiniowano listy zdjęć!';
  }

  if (count($photos_errors) == 0)
  {
    if (count($photos) > PHOTOS_PAGINATION_THRESHOLD)
    {
      $page = 1;
      $page_count = ceil(count($photos) / PHOTOS_PAGINATION_THRESHOLD);
      if (isset($_GET['p']))
      {
        $page = $_GET['p'];
        $offset = ($page - 1) * PHOTOS_PAGINATION_THRESHOLD;
        if ($page > 0 && $offset < count($photos))
        {
          $photos = array_slice($photos, $offset, PHOTOS_PAGINATION_THRESHOLD);
        }
        else
        {
          $_SESSION['alert'][] = 'Żądany fragment listy zdjęć nie istnieje.';
          header('Location: ' . ROOT_URL . modify_get_parameters(['p' => null]));
          exit();
        }
      }
      else
      {
        $photos = array_slice($photos, 0, PHOTOS_PAGINATION_THRESHOLD);
      }
    }

    $photos_link = isset($photos_link) ? $photos_link : ROOT_URL . '?page=photo';
    $photos_class = isset($photos_class) ? $photos_class : 'my-1';
    $photos_badge_content = isset($photos_badge_content) ? $photos_badge_content : null;

    echo '<div class="row justify-content-center">' . PHP_EOL;
    for ($i = 0; $i < count($photos); $i++)
    {
      $photo = $photos[$i];
      echo
        '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $photos_class . '">' . PHP_EOL .
          '<a class="d-block link--clean w-180px m-auto" href="' . $photos_link . '&photo_id=' . $photo->id . '">' . PHP_EOL;
      if (!is_null($photos_badge_content))
      {
        switch ($photos_badge_content)
        {
          case 'unverified':
            if ($photo->verified == 0)
            {
              $photo_badge = 'Niezaakceptowane';
            }
            else
            {
              unset($photo_badge);
            }
          break;
        }
      }
      include VIEWS_PATH . 'photos/render_photo_thumbnail.php';
      echo
          '</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
    echo '</div>' . PHP_EOL;

    if (isset($page_count))
    {
      echo '<div class="pt-1-75">' . PHP_EOL;
      include_once VIEWS_PATH . 'shared/pagination.php';
      echo '</div>' . PHP_EOL;
    }
  }

  if (count($photos_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić zdjęć, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($photos_errors); $i++)
    {
      echo '<li>' . $photos_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
