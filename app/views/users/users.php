<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $users_errors = [];

  if (empty($users))
  {
    $users_errors[] = 'Nie zdefiniowano listy użytkowników!';
  }

  if (count($users_errors) == 0)
  {
    if (count($users) > USERS_PAGINATION_THRESHOLD)
    {
      $page = 1;
      $page_count = ceil(count($users) / USERS_PAGINATION_THRESHOLD);
      if (isset($_GET['p']))
      {
        $page = $_GET['p'];
        $offset = ($page - 1) * USERS_PAGINATION_THRESHOLD;
        if ($page > 0 && $offset < count($users))
        {
          $users = array_slice($users, $offset, USERS_PAGINATION_THRESHOLD);
        }
        else
        {
          $_SESSION['alert'][] = 'Żądany fragment listy użytkowników nie istnieje.';
          header('Location: ' . ROOT_URL . modify_get_parameters(['p' => null]));
          exit();
        }
      }
      else
      {
        $users = array_slice($users, 0, USERS_PAGINATION_THRESHOLD);
      }
    }

    $users_link = isset($users_link) ? $users_link : null;

    echo '<div class="row justify-content-center">' . PHP_EOL;
    for ($i = 0; $i < count($users); $i++)
    {
      $user = $users[$i];
      echo '<div class="col-xs-12 col-md-6">' . PHP_EOL;

      if (!empty($users_link))
      {
        echo '<a class="d-block link--float" href="' . $users_link . '&user_id=' . $user->id . '">' . PHP_EOL;
      }
      if (!is_null($users_badge_content))
      {
        switch ($users_badge_content)
        {
          case 'inactive':
            if ($user->active == 0)
            {
              $user_badge = 'Nieaktywny';
            }
            else
            {
              unset($user_badge);
            }
          break;
        }
      }
      include VIEWS_PATH . 'users/render_user.php';
      if (!empty($users_link))
      {
        echo '</a>' . PHP_EOL;
      }

      echo '</div>' . PHP_EOL;
    }
    echo '</div>' . PHP_EOL;

    if (isset($page_count))
    {
      echo '<div class="pt-1-75">' . PHP_EOL;
      include_once VIEWS_PATH . 'shared/pagination.php';
      echo '</div>' . PHP_EOL;
    }
  }

  if (count($users_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić użytkowników, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($users_errors); $i++)
    {
      echo '<li>' . $users_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
