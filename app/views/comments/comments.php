<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $comments_errors = [];

  if (empty($comments))
  {
    $comments_errors[] = 'Nie zdefiniowano listy komentarzy!';
  }

  if (count($comments_errors) == 0)
  {
    if (count($comments) > COMMENTS_PAGINATION_THRESHOLD)
    {
      $page = 1;
      $page_count = ceil(count($comments) / COMMENTS_PAGINATION_THRESHOLD);
      if (isset($_GET['p']))
      {
        $page = $_GET['p'];
        $offset = ($page - 1) * COMMENTS_PAGINATION_THRESHOLD;
        if ($page > 0 && $offset < count($comments))
        {
          $comments = array_slice($comments, $offset, COMMENTS_PAGINATION_THRESHOLD);
        }
        else
        {
          $_SESSION['alert'][] = 'Żądany fragment listy komentarzy nie istnieje.';
          header('Location: ' . ROOT_URL . modify_get_parameters(['p' => null]));
          exit();
        }
      }
      else
      {
        $comments = array_slice($comments, 0, COMMENTS_PAGINATION_THRESHOLD);
      }
    }

    $comments_link = isset($comments_link) ? $comments_link : null;

    for ($i = 0; $i < count($comments); $i++)
    {
      $comment = $comments[$i];

      if (!empty($comments_link))
      {
        echo '<a class="d-block link--float" href="' . $comments_link . '&comment_id=' . $comment->id . '">' . PHP_EOL;
      }
      include VIEWS_PATH . 'comments/render_comment.php';
      if (!empty($comments_link))
      {
        echo '</a>' . PHP_EOL;
      }
    }

    if (isset($page_count))
    {
      echo '<div class="pt-1-75">' . PHP_EOL;
      include_once VIEWS_PATH . 'shared/pagination.php';
      echo '</div>' . PHP_EOL;
    }
  }

  if (count($comments_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić komentarzy, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($comments_errors); $i++)
    {
      echo '<li>' . $comments_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
