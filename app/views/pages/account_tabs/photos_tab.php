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
    if (isset($_SESSION['current_user']['id']))
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
          header('Location: ' . ROOT_URL . modify_get_parameters(['album_id' => null, 'photo_id' => null]));
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
      if (empty($photo_id))
      {
        $photos = [];

        $query =
          'SELECT
            a.title AS album_title,
            p.id AS photo_id
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
            null,
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
          include VIEWS_PATH . 'photos/photos.php';
        }
        else
        {
          echo
            '<h3 class="mb-0-25">Ten album jest pusty</h3>' . PHP_EOL .
            '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=create_photo&album_id=' . $album_id . '#photo">Dodaj pierwsze zdjęcie do tego albumu już dziś!</a>' . PHP_EOL;
        }
        echo
          '</div>' . PHP_EOL .
          '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
            '<span class="text-muted">Nie ten album?</span>' . PHP_EOL .
            '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . modify_get_parameters(['album_id' => null]) . '">Wybierz jeszcze raz!</a>' . PHP_EOL .
          '</div>' . PHP_EOL;
      }

      if (!empty($photo_id))
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
        $_SESSION['redirect_url'] = ROOT_URL . '?page=account&tab=photos&album_id=' . $album_id;
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
