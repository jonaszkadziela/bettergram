<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();
  $albums = [];
  $query =
    'SELECT
      a.id AS album_id,
      a.title AS album_title,
      a.date AS album_date,
      a.user_id AS album_user_id,
      p.id AS photo_id,
      u.login AS user_login
    FROM
      albums AS a
    JOIN photos AS p
    ON
      a.id = p.album_id
    JOIN users AS u
    ON
      a.user_id = u.id
    WHERE
      p.verified = 1
    GROUP BY
      a.id;';
  $result = $db->prepared_select_query($query);

  if ($result && count($result) > 0)
  {
    for ($i = 0; $i < count($result); $i++)
    {
      $albums[$i] = new Album
      (
        $result[$i]['album_id'],
        $result[$i]['album_title'],
        $result[$i]['album_date'],
        $result[$i]['album_user_id']
      );
      $albums[$i]->author->login = $result[$i]['user_login'];
      $albums[$i]->photos[0] = new Photo
      (
        $result[$i]['photo_id'],
        null,
        null,
        1,
        $result[$i]['album_id']
      );
    }

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
              echo '<h2 class="underline underline-primary mb-1-5">Galeria</h2>' . PHP_EOL;
              if (count($albums) > 0)
              {
                echo '<div class="mt-0-5 mb-1">' . PHP_EOL;
                $sorting_options = ['title', 'author', 'date_asc', 'date_desc'];
                include VIEWS_PATH . 'shared/sorting.php';
                echo '</div>' . PHP_EOL;

                $albums_link_type = 'popover';
                include VIEWS_PATH . 'albums/albums.php';
              }
              else
              {
                echo
                  '<h4 class="m-0">Nie ma jeszcze żadnych albumów do wyświetlenia</h4>' . PHP_EOL .
                  '<small class="d-block text-muted mb-1-5">(wyświetlane są tylko albumy, które posiadają przynajmniej jedno zaakceptowane zdjęcie)</small>' . PHP_EOL .
                  '<p class="mb-1-5">Bądź pierwszym użytkownikiem, który stworzy album i zamieści w nim zdjęcie!</p>' . PHP_EOL;

                if (isset($_SESSION['current_user']['logged_in']))
                {
                  echo
                    '<p class="m-0">' . PHP_EOL .
                      '<span class="text-muted">Nie posiadasz jeszcze albumu?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=create_album">Załóż album!</a>' . PHP_EOL .
                    '</p>' . PHP_EOL .
                    '<p class="m-0">' . PHP_EOL .
                      '<span class="text-muted">Twój album jest pusty?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=create_photo">Dodaj do niego zdjęcie!</a>' . PHP_EOL .
                    '</p>' . PHP_EOL;
                }
                else
                {
                  $_SESSION['target_url'] = get_url(false) . '?page=create_album';
                  echo
                    '<p class="m-0">' . PHP_EOL .
                      '<span class="text-muted">Jesteś tu po raz pierwszy?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=register&redirect=1">Stwórz nowe konto!</a>' . PHP_EOL .
                    '</p>' . PHP_EOL .
                    '<p class="m-0">' . PHP_EOL .
                      '<span class="text-muted">Posiadasz już konto?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?page=login&redirect=1">Zaloguj się!</a>' . PHP_EOL .
                    '</p>' . PHP_EOL;
                }
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
