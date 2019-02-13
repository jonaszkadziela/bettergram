<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $albums_errors = [];

  if (empty($albums))
  {
    $albums_errors[] = 'Nie zdefiniowano listy albumów!';
  }

  if (count($albums_errors) == 0)
  {
    if (count($albums) > ALBUMS_PAGINATION_THRESHOLD)
    {
      $page = 1;
      $page_count = ceil(count($albums) / ALBUMS_PAGINATION_THRESHOLD);
      if (isset($_GET['p']))
      {
        $page = $_GET['p'];
        $offset = ($page - 1) * ALBUMS_PAGINATION_THRESHOLD;
        if ($page > 0 && $offset < count($albums))
        {
          $albums = array_slice($albums, $offset, ALBUMS_PAGINATION_THRESHOLD);
        }
        else
        {
          $_SESSION['alert'][] = 'Żądany fragment listy albumów nie istnieje.';
          header('Location: ' . ROOT_URL . modify_get_parameters(['p' => null]));
          exit();
        }
      }
      else
      {
        $albums = array_slice($albums, 0, ALBUMS_PAGINATION_THRESHOLD);
      }
    }

    $albums_link = isset($albums_link) ? $albums_link : ROOT_URL . '?page=album';
    $albums_link_type = isset($albums_link_type) ? $albums_link_type : 'regular';
    $albums_badge_content = isset($albums_badge_content) ? $albums_badge_content : null;

    echo '<div class="row justify-content-center">' . PHP_EOL;
    for ($i = 0; $i < count($albums); $i++)
    {
      $album = $albums[$i];
      echo '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 my-1">' . PHP_EOL;

      switch ($albums_link_type)
      {
        case 'popover':
          echo
            '<a class="d-block link--clean w-180px h-100 m-auto" href="' . $albums_link . '&album_id=' . $album->id . '" ' .
            'data-toggle="tooltip" data-html="true" data-trigger="manual" title="' .
            sanitize_text($album->title) . '<br>' .
            '<b>' . $album->author . '</b><br>' .
            '(' . $album->date->format('d.m.Y') . ' ' . $album->date->format('G:i') . ')' .
            '">' . PHP_EOL;
        break;

        case 'regular':
        default:
          echo '<a class="d-block link--clean w-180px h-100 m-auto" href="' . $albums_link . '&album_id=' . $album->id . '">' . PHP_EOL;
      }
      switch ($albums_badge_content)
      {
        case 'unverified_photos_count':
          if ($album->unverified_photos_count > 0)
          {
            $album_badge = 'Do akceptacji: ' . $albums[$i]->unverified_photos_count;
          }
          else
          {
            unset($album_badge);
          }
        break;
      }

      include VIEWS_PATH . 'albums/render_album.php';

      echo
          '</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
    echo '</div>' . PHP_EOL;

    if (isset($page_count))
    {
      echo '<div class="pt-1">' . PHP_EOL;
      include_once VIEWS_PATH . 'shared/pagination.php';
      echo '</div>' . PHP_EOL;
    }
  }

  if (count($albums_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić albumów, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($albums_errors); $i++)
    {
      echo '<li>' . $albums_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
