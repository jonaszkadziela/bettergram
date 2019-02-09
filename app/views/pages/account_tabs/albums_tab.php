<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $albums_tab_errors = [];

  if (empty($tab))
  {
    $albums_tab_errors[] = 'Nie zdefiniowano podstrony!';
  }

  if (count($albums_tab_errors) == 0 && $tab == 'albums')
  {
    if (isset($_SESSION['current_user']['id']))
    {
      $album_id = isset($_GET['album_id']) ? $_GET['album_id'] : null;
      $albums = [];

      if (empty($album_id))
      {
        $query =
          'SELECT
            a.id AS album_id,
            a.title AS album_title,
            p.id AS photo_id
          FROM
            albums AS a
          LEFT JOIN photos AS p
          ON
            a.id = p.album_id
          JOIN users AS u
          ON
            a.user_id = u.id
          WHERE
            u.id = ?
          GROUP BY
            a.id;';
        $result = $db->prepared_select_query($query, [$_SESSION['current_user']['id']]);

        for ($i = 0; $i < ($result ? count($result) : 0); $i++)
        {
          $albums[$i] = new Album
          (
            $result[$i]['album_id'],
            $result[$i]['album_title'],
            null,
            $_SESSION['current_user']['id']
          );
          if (is_null($result[$i]['photo_id']))
          {
            continue;
          }
          $albums[$i]->photos[0] = new Photo
          (
            $result[$i]['photo_id'],
            null,
            null,
            null,
            $result[$i]['album_id']
          );
        }
      }
      if (!empty($album_id))
      {
        $query =
          'SELECT
            a.title AS album_title
          FROM
            albums AS a
          JOIN users AS u
          ON
            a.user_id = u.id
          WHERE
            a.id = ? AND u.id = ?;';
        $result = $db->prepared_select_query($query, [$album_id, $_SESSION['current_user']['id']]);

        if ($result && count($result) > 0)
        {
          $album = new Album
          (
            $album_id,
            $result[0]['album_title'],
            null,
            null
          );
        }
        else
        {
          $_SESSION['alert'][] = '<h5>Błąd!</h5> Podany album nie istnieje lub nie masz do niego uprawnień!';
          header('Location: ' . ROOT_URL . modify_get_parameters(['album_id' => null]));
          exit();
        }
      }
    }
    else
    {
      $errors[] = 'Nie udało się ustalić ID użytkownika!';
      header('Location: ' . ROOT_URL);
      exit();
    }

    if (empty($album_id))
    {
      echo '<div class="rounded border bg-light my-1-5 p-1-5">' . PHP_EOL;
      if (count($albums) > 0)
      {
        echo '<h3 class="mb-0-5">Twoje albumy</h3>' . PHP_EOL;
        $albums_link = get_url();
        include VIEWS_PATH . 'albums/albums.php';
      }
      else
      {
        echo
          '<h3 class="mb-1">Brak albumów</h3>' . PHP_EOL .
          '<p class="mb-0-25">Nie posiadasz jeszcze żadnych albumów.</p>' . PHP_EOL .
          '<p>Utwórz swój pierwszy album już dziś!</p>' . PHP_EOL .
          '<a href="' . ROOT_URL . '?page=create_album" class="btn btn-primary">Załóż album</a>' . PHP_EOL;
      }
      echo
        '</div>' . PHP_EOL .
        '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
          '<span class="text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }

    if (!empty($album_id))
    {
      echo
        '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
          '<div class="card-body">' . PHP_EOL;
      // Redirect user to the following URL after deleting an album
      $_SESSION['redirect_url'] = ROOT_URL . '?page=account&tab=albums';
      include VIEWS_PATH . 'albums/update_album_form.php';
      echo
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
          '<span class="text-muted">Nie ten album?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . modify_get_parameters(['album_id' => null]) . '">Wybierz jeszcze raz!</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
  }

  if (count($albums_tab_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić podstrony albumów, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($albums_tab_errors); $i++)
    {
      echo '<li>' . $albums_tab_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
