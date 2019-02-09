<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();
  $errors = [];
  $albums = [];

  if (empty($_SESSION['current_user']['id']))
  {
    $errors[] = 'Nie udało się ustalić ID użytkownika!';
  }

  if (count($errors) == 0)
  {
    if (empty($_GET['album_id']))
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
    else
    {
      $album_id = $_GET['album_id'];
      $query =
        'SELECT
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

        for ($i = 0; $i < count($result); $i++)
        {
          if (is_null($result[$i]['photo_id']))
          {
            continue;
          }
          $album->photos[] = new Photo
          (
            $result[$i]['photo_id'],
            null,
            null,
            null,
            $album_id
          );
        }
      }
      else
      {
        $_SESSION['alert'][] = '<h5>Błąd!</h5> Podany album nie istnieje lub nie masz do niego uprawnień!';
        header('Location: ' . ROOT_URL . '?page=create_photo');
        exit();
      }
    }
  }

  if (count($errors) > 0)
  {
    $alert =
      '<h5>Wystąpiły następujące błędy:</h5>' . PHP_EOL .
      '<ul class="mb-0 pl-1-25">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $alert .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $alert .= '</ul>' . PHP_EOL;
    $_SESSION['alert'][] = $alert;
  }
?>
<div class="d-flex flex-column min-vh-100 bg-img-1">
  <?php
    include_once VIEWS_PATH . 'shared/navbar.php';
  ?>
  <div class="container d-flex flex-grow-1 flex-column h-100 my-3">
    <div class="row flex-grow-1">
      <div class="col-lg-12 col-xl-10 m-auto">
        <?php
          include_once VIEWS_PATH . 'shared/flash.php';
        ?>
        <div class="card p-1 shadow-lg">
          <div class="card-body text-center">
            <?php
              if (empty($album_id))
              {
                if (count($albums) > 0)
                {
                  echo
                    '<h2 class="underline underline-primary mb-1-5">Wybierz album</h2>' . PHP_EOL;
                  $albums_link = get_url();
                  include VIEWS_PATH . 'albums/albums.php';
                  echo
                    '<div class="mt-1-5">' . PHP_EOL .
                      '<span class="text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
                    '</div>' . PHP_EOL;
                }
                else
                {
                  echo
                    '<h2 class="underline underline-primary mb-1-5">Brak albumów</h2>' . PHP_EOL .
                    '<p class="mb-0-25">Nie posiadasz jeszcze żadnych albumów.</p>' . PHP_EOL .
                    '<p class="mb-1-5">Utwórz swój pierwszy album już dziś!</p>' . PHP_EOL .
                    '<a href="' . ROOT_URL . '?page=create_album" class="btn btn-primary">Załóż album</a>' . PHP_EOL;
                }
              }
              else
              {
                echo
                  '<h2 class="underline underline-primary mb-1-5">Album</h2>' . PHP_EOL .
                  '<h4 class="mb-1-5">"' . $album->title . '"</h4>' . PHP_EOL .
                  '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                    '<div class="card-body">' . PHP_EOL;
                if (count($album->photos) > 0)
                {
                  echo '<h2 class="mb-0-25">Zdjęcia</h2>' . PHP_EOL;
                  $photos = $album->photos;
                  $photos_class = 'mt-1-5';
                  include VIEWS_PATH . 'photos/photos.php';
                }
                else
                {
                  echo
                    '<h3 class="mb-0-25">Ten album jest pusty</h3>' . PHP_EOL .
                    '<a class="underline underline--narrow underline-primary underline-animation" href="#photo">Dodaj pierwsze zdjęcie do tego albumu już dziś!</a>' . PHP_EOL;
                }
                echo
                    '</div>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                  '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                    '<div class="card-body">' . PHP_EOL;
                include VIEWS_PATH . 'photos/create_photo_form.php';
                echo
                    '</div>' . PHP_EOL .
                  '</div>' . PHP_EOL;

                echo
                  '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
                    '<span class="text-muted">Nie ten album?</span>' . PHP_EOL .
                    '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=create_photo">Wybierz jeszcze raz!</a>' . PHP_EOL .
                  '</div>' . PHP_EOL;
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    include_once VIEWS_PATH . 'shared/footer.php';
  ?>
</div>
