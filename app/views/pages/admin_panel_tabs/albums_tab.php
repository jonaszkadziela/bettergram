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
    $album_id = isset($_GET['album_id']) ? $_GET['album_id'] : null;
    $albums = [];

    if (empty($album_id))
    {
      $query =
        'SELECT
          a.id AS album_id,
          a.title AS album_title,
          a.user_id AS album_user_id,
          p.id AS photo_id,
          COUNT(CASE WHEN p.verified = 0 THEN 1 ELSE NULL END) AS unverified_photos_count,
          u.login AS user_login
        FROM
          albums AS a
        LEFT JOIN photos AS p
        ON
          a.id = p.album_id
        JOIN users AS u
        ON
          a.user_id = u.id
        GROUP BY
          a.id;';
      $result = $db->prepared_select_query($query);

      for ($i = 0; $i < ($result ? count($result) : 0); $i++)
      {
        $albums[$i] = new Album
        (
          $result[$i]['album_id'],
          $result[$i]['album_title'],
          null,
          $result[$i]['album_user_id']
        );
        $albums[$i]->author->login = $result[$i]['user_login'];
        $albums[$i]->unverified_photos_count = $result[$i]['unverified_photos_count'];
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
          a.title AS album_title,
          a.user_id AS album_user_id
        FROM
          albums AS a
        WHERE
          a.id = ?';
      $result = $db->prepared_select_query($query, [$album_id]);

      if ($result && count($result) > 0)
      {
        $album = new Album
        (
          $album_id,
          $result[0]['album_title'],
          null,
          $result[0]['album_user_id']
        );
      }
      else
      {
        $_SESSION['alert'][] = '<h5>Błąd!</h5> Podany album nie istnieje!';
        header('Location: ' . ROOT_URL . modify_get_parameters(['album_id' => null, 'photo_id' => null]));
        exit();
      }
    }

    if (empty($album_id))
    {
      echo '<div class="rounded border bg-light my-1-5 p-1-5">' . PHP_EOL;
      if (count($albums) > 0)
      {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
        switch ($sort)
        {
          case 'title':
          case 'author':
          case 'date_asc':
          case 'date_desc':
            usort($albums, ['Album', 'compare_' . $sort]);
          break;

          default:
            header('Location: ' . ROOT_URL . modify_get_parameters(['sort' => 'title']));
            exit();
        }

        echo '<h3 class="mb-1-5">Wszystkie albumy</h3>' . PHP_EOL;
        echo '<div class="mb-1">' . PHP_EOL;
        $sorting_options = ['title', 'author', 'date_asc', 'date_desc'];
        include VIEWS_PATH . 'shared/sorting.php';
        echo '</div>' . PHP_EOL;

        $albums_link = get_url();
        $albums_link_type = 'popover';
        $albums_badge_content = 'unverified_photos_count';
        include VIEWS_PATH . 'albums/albums.php';
      }
      else
      {
        echo '<h3 class="m-0">Brak albumów</h3>' . PHP_EOL;
      }
      echo
        '</div>' . PHP_EOL .
        '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
          '<span class="text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
    else if (!empty($album_id))
    {
      echo
        '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
          '<div class="card-body">' . PHP_EOL;
      // Redirect user to the following URL after deleting an album
      $_SESSION['redirect_url'] = ROOT_URL . '?page=admin_panel&tab=albums';
      $update_album_form_mode = 'privileged';
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
