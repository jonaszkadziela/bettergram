<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $users_tab_errors = [];

  if (empty($tab))
  {
    $users_tab_errors[] = 'Nie zdefiniowano podstrony!';
  }

  if (count($users_tab_errors) == 0 && $tab == 'users')
  {
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    switch ($filter)
    {
      case 'all':
      case 'regular':
      case 'moderators':
      case 'administrators':
      break;

      default:
        header('Location: ' . ROOT_URL . modify_get_parameters(['filter' => 'all']));
        exit();
    }

    if (empty($user_id))
    {
      echo
        '<div class="rounded border bg-light my-1-5 p-1">' . PHP_EOL .
          '<div class="row">' . PHP_EOL .
            '<div class="col-12 col-md-auto d-flex flex-column justify-content-center px-md-0-5 ml-auto">' . PHP_EOL .
              '<label class="m-md-0" for="select_filter">Wyświetl</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="col-12 col-md-6 px-md-0-5 mr-auto">' . PHP_EOL .
              '<select id="select_filter" class="js-select-links custom-select">' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['filter' => 'all']) . '"' . ($filter == 'all' ? ' selected' : '') . '>Wszyscy użytkownicy</option>' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['filter' => 'regular']) . '"' . ($filter == 'regular' ? ' selected' : '') . '>Zwykli użytkownicy</option>' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['filter' => 'moderators']) . '"' . ($filter == 'moderators' ? ' selected' : '') . '>Moderatorzy</option>' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['filter' => 'administrators']) . '"' . ($filter == 'administrators' ? ' selected' : '') . '>Administratorzy</option>' . PHP_EOL .
              '</select>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL;

      $users = [];

      $permissions = '%';
      if ($filter == 'regular')
      {
        $permissions = 'użytkownik';
      }
      else if ($filter == 'moderators')
      {
        $permissions = 'moderator';
      }
      else if ($filter == 'administrators')
      {
        $permissions = 'administrator';
      }
      $query =
        'SELECT
          u.id AS user_id,
          u.login AS user_login,
          u.email AS user_email,
          u.registration_date AS user_registration_date,
          u.permissions AS user_permissions,
          u.active AS user_active
        FROM
          users AS u
        WHERE
          u.permissions LIKE ?;';
      $result = $db->prepared_select_query($query, [$permissions]);

      for ($i = 0; $i < ($result ? count($result) : 0); $i++)
      {
        $users[] = new User
        (
          $result[$i]['user_id'],
          $result[$i]['user_login'],
          $result[$i]['user_email'],
          $result[$i]['user_registration_date'],
          $result[$i]['user_permissions'],
          $result[$i]['user_active']
        );
      }

      echo
        '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
          '<div class="card-body">' . PHP_EOL;
      if (count($users) > 0)
      {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'login';
        switch ($sort)
        {
          case 'login':
          case 'date_asc':
          case 'date_desc':
            usort($users, ['User', 'compare_' . $sort]);
          break;

          default:
            header('Location: ' . ROOT_URL . modify_get_parameters(['sort' => 'login']));
            exit();
        }

        if ($filter == 'all')
        {
          echo '<h3 class="mb-1-5">Wszyscy użytkownicy</h3>' . PHP_EOL;
        }
        else if ($filter == 'regular')
        {
          echo '<h3 class="mb-1-5">Zwykli użytkownicy</h3>' . PHP_EOL;
        }
        else if ($filter == 'moderators')
        {
          echo '<h3 class="mb-1-5">Moderatorzy</h3>' . PHP_EOL;
        }
        else if ($filter == 'administrators')
        {
          echo '<h3 class="mb-1-5">Administratorzy</h3>' . PHP_EOL;
        }
        echo '<div class="mb-1">' . PHP_EOL;
        $sorting_options = ['login', 'date_asc', 'date_desc'];
        include VIEWS_PATH . 'shared/sorting.php';
        echo '</div>' . PHP_EOL;

        $users_link = get_url();
        $users_badge_content = 'inactive';
        include VIEWS_PATH . 'users/users.php';
      }
      else
      {
        echo '<h3 class="m-0">Brak użytkowników</h3>' . PHP_EOL;
      }
      echo
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
          '<span class="text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }

    if (!empty($user_id))
    {
      $query =
        'SELECT
          u.login AS user_login,
          u.email AS user_email,
          u.permissions AS user_permissions,
          u.active AS user_active
        FROM
          users AS u
        WHERE
          u.id = ?;';
      $result = $db->prepared_select_query($query, [$user_id]);

      if ($result && count($result) > 0)
      {
        $user = new User
        (
          $user_id,
          $result[0]['user_login'],
          $result[0]['user_email'],
          null,
          $result[0]['user_permissions'],
          $result[0]['user_active']
        );
      }
      else
      {
        $_SESSION['alert'][] = '<h5>Błąd!</h5> Podany użytkownik nie istnieje!';
        header('Location: ' . ROOT_URL . modify_get_parameters(['user_id' => null]));
        exit();
      }
      echo
        '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
          '<div class="card-body">' . PHP_EOL;

      // Redirect user to the following URL after deleting a user
      $_SESSION['redirect_url'] = ROOT_URL . modify_get_parameters(['user_id' => null]);
      $update_user_form_mode = 'privileged';
      include VIEWS_PATH . 'users/update_user_form.php';

      echo
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
          '<span class="text-muted">Nie ten użytkownik?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . modify_get_parameters(['user_id' => null]) . '">Wybierz jeszcze raz!</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
  }

  if (count($users_tab_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić podstrony użytkowników, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($users_tab_errors); $i++)
    {
      echo '<li>' . $users_tab_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
