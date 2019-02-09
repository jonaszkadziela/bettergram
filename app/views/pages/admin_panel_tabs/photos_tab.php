<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $photos_tab_errors = [];

  if (empty($tab))
  {
    $photos_tab_errors[] = 'Nie zdefiniowano podstrony!';
  }

  if (count($photos_tab_errors) == 0 && $tab == 'photos')
  {
    $album_id = isset($_GET['album_id']) ? $_GET['album_id'] : null;
    $photo_id = isset($_GET['photo_id']) ? $_GET['photo_id'] : null;
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
    else if (!empty($album_id))
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

    $show = isset($_GET['show']) ? $_GET['show'] : 'all_albums';
    switch ($show)
    {
      case 'all_albums':
      case 'unverified_photos':
      break;

      default:
        header('Location: ' . ROOT_URL . modify_get_parameters(['show' => 'all_albums']));
        exit();
    }

    if (empty($album_id) && empty($photo_id))
    {
      echo
        '<div class="rounded border bg-light my-1-5 p-1">' . PHP_EOL .
          '<div class="row">' . PHP_EOL .
            '<div class="col-12 col-md-auto d-flex flex-column justify-content-center px-md-0-5 ml-auto">' . PHP_EOL .
              '<label class="m-md-0" for="select_show">Wyświetl</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="col-12 col-md-6 px-md-0-5 mr-auto">' . PHP_EOL .
              '<select id="select_show" class="js-select-links custom-select">' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['show' => 'all_albums']) . '"' . ($show == 'all_albums' ? ' selected' : '') . '>Wszystkie albumy</option>' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['show' => 'unverified_photos']) . '"' . ($show == 'unverified_photos' ? ' selected' : '') . '>Niezaakceptowane zdjęcia</option>' . PHP_EOL .
              '</select>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }

    if ($show == 'all_albums')
    {
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
          echo '<div class="mt-0-5 mb-1">' . PHP_EOL;
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
        if (empty($photo_id))
        {
          $photos = [];

          $query =
            'SELECT
              a.title AS album_title,
              p.id AS photo_id,
              p.verified AS photo_verified
            FROM
              albums AS a
            JOIN photos AS p
            ON
              a.id = p.album_id
            WHERE
              a.id = ?;';
          $result = $db->prepared_select_query($query, [$album_id]);

          for ($i = 0; $i < ($result ? count($result) : 0); $i++)
          {
            $album = new Album
            (
              $album_id,
              $result[0]['album_title'],
              null,
              null
            );
            $photos[] = new Photo
            (
              $result[$i]['photo_id'],
              null,
              null,
              $result[$i]['photo_verified'],
              $album_id
            );
          }

          echo '<div class="rounded border bg-light my-1-5 p-1-5">' . PHP_EOL;
          if (count($photos) > 0)
          {
            echo
              '<h3 class="m-0">Zdjęcia z albumu</h3>' . PHP_EOL .
              '<h5 class="m-0">"' . $album->title . '"</h5>' . PHP_EOL;
            $photos_link = get_url();
            $photos_class = 'mt-1-5';
            $photos_badge_content = 'unverified';
            include VIEWS_PATH . 'photos/photos.php';
          }
          else
          {
            echo '<h3 class="m-0">Ten album jest pusty</h3>' . PHP_EOL;
          }
          echo
            '</div>' . PHP_EOL .
            '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
              '<span class="text-muted">Nie ten album?</span>' . PHP_EOL .
              '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . modify_get_parameters(['album_id' => null]) . '">Wybierz jeszcze raz!</a>' . PHP_EOL .
            '</div>' . PHP_EOL;
        }
        else if (!empty($photo_id))
        {
          $query =
            'SELECT
              p.id AS photo_id,
              p.description AS photo_description,
              p.verified AS photo_verified
            FROM
              albums AS a
            JOIN photos AS p
            ON
              a.id = p.album_id
            WHERE
              a.id = ? AND p.id = ?;';
          $result = $db->prepared_select_query($query, [$album_id, $photo_id]);

          if ($result && count($result) > 0)
          {
            $photo = new Photo
            (
              $result[0]['photo_id'],
              $result[0]['photo_description'],
              null,
              $result[0]['photo_verified'],
              $album_id
            );
          }
          else
          {
            $_SESSION['alert'][] = '<h5>Błąd!</h5> Nie znaleziono żądanego zdjęcia w tym albumie!';
            header('Location: ' . ROOT_URL . modify_get_parameters(['photo_id' => null]));
            exit();
          }
          echo
            '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
              '<div class="card-body">' . PHP_EOL;
          // Redirect user to the following URL after deleting a photo
          $_SESSION['redirect_url'] = ROOT_URL . '?page=admin_panel&tab=photos&album_id=' . $album_id;
          $update_photo_form_mode = 'privileged';
          include VIEWS_PATH . 'photos/update_photo_form.php';
          echo
              '</div>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
              '<span class="text-muted">Nie to zdjęcie?</span>' . PHP_EOL .
              '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . modify_get_parameters(['photo_id' => null]) . '">Wybierz jeszcze raz!</a>' . PHP_EOL .
            '</div>' . PHP_EOL;
        }
      }
    }
    else if ($show == 'unverified_photos')
    {
      if (empty($photo_id))
      {
        $photos = [];

        $query =
          'SELECT
            p.id AS photo_id,
            a.id AS album_id
          FROM
            albums AS a
          JOIN photos AS p
          ON
            a.id = p.album_id
          WHERE
            p.verified = 0;';
        $result = $db->prepared_select_query($query);

        for ($i = 0; $i < ($result ? count($result) : 0); $i++)
        {
          $photos[] = new Photo
          (
            $result[$i]['photo_id'],
            null,
            null,
            null,
            $result[$i]['album_id']
          );
        }

        echo '<div class="rounded border bg-light my-1-5 p-1-5">' . PHP_EOL;
        if (count($photos) > 0)
        {
          $sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';
          switch ($sort)
          {
            case 'date_asc':
            case 'date_desc':
              usort($photos, ['Photo', 'compare_' . $sort]);
            break;

            default:
              header('Location: ' . ROOT_URL . modify_get_parameters(['sort' => 'date_asc']));
              exit();
          }

          echo '<h3 class="mb-1-5">Niezaakceptowane zdjęcia</h3>' . PHP_EOL;
          echo '<div class="mt-0-5 mb-1">' . PHP_EOL;
          $sorting_options = ['date_asc', 'date_desc'];
          include VIEWS_PATH . 'shared/sorting.php';
          echo '</div>' . PHP_EOL;

          $photos_link = get_url();
          $photos_class = 'mt-1-5';
          include VIEWS_PATH . 'photos/photos.php';
        }
        else
        {
          echo
            '<h3 class="m-0">Brak zdjęć do zaakceptowania</h3>' . PHP_EOL;
        }
        echo
          '</div>' . PHP_EOL .
          '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
            '<span class="text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
            '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
          '</div>' . PHP_EOL;
      }
      else if (!empty($photo_id))
      {
        $query =
          'SELECT
            p.id AS photo_id,
            p.description AS photo_description,
            p.verified AS photo_verified,
            p.album_id AS photo_album_id
          FROM
            photos AS p
          WHERE
            p.id = ?;';
        $result = $db->prepared_select_query($query, [$photo_id]);

        if ($result && count($result) > 0)
        {
          $photo = new Photo
          (
            $result[0]['photo_id'],
            $result[0]['photo_description'],
            null,
            $result[0]['photo_verified'],
            $result[0]['photo_album_id']
          );
        }
        else
        {
          $_SESSION['alert'][] = '<h5>Błąd!</h5> Nie znaleziono żądanego zdjęcia!';
          header('Location: ' . ROOT_URL . modify_get_parameters(['photo_id' => null]));
          exit();
        }
        echo
          '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
            '<div class="card-body">' . PHP_EOL;
        // Redirect user to the following URL after deleting a photo
        $_SESSION['redirect_url'] = ROOT_URL . '?page=admin_panel&tab=photos&show=unverified_photos';
        $update_photo_form_mode = 'privileged';
        include VIEWS_PATH . 'photos/update_photo_form.php';
        echo
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
            '<span class="text-muted">Nie to zdjęcie?</span>' . PHP_EOL .
            '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . modify_get_parameters(['photo_id' => null]) . '">Wybierz jeszcze raz!</a>' . PHP_EOL .
          '</div>' . PHP_EOL;
      }
    }
  }

  if (count($photos_tab_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić podstrony zdjęć, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($photos_tab_errors); $i++)
    {
      echo '<li>' . $photos_tab_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
