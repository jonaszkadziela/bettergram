<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();
  $photos = [];
  $query =
    'SELECT
      p.id AS photo_id,
      p.album_id AS photo_album_id
    FROM
      photos AS p
    WHERE
      p.verified = 1
    ORDER BY
      p.date
    DESC
    LIMIT 20;';
  $result = $db->prepared_select_query($query);

  for ($i = 0; $i < ($result ? count($result) : 0); $i++)
  {
    $photos[$i] = new Photo
    (
      $result[$i]['photo_id'],
      null,
      null,
      null,
      $result[$i]['photo_album_id']
    );
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
              echo '<h2 class="underline underline-primary mb-1-5">Najnowsze</h2>' . PHP_EOL;
              if (count($photos) > 0)
              {
                echo '<p class="text-muted pt-0-5 mb-1">Zestawienie 20 najnowszych zdjęć</p>' . PHP_EOL;
                include VIEWS_PATH . 'photos/photos.php';
              }
              else
              {
                echo
                  '<h4 class="m-0">Nie ma jeszcze żadnych zdjęć do wyświetlenia</h4>' . PHP_EOL .
                  '<small class="d-block text-muted mb-1-5">(wyświetlane są tylko zaakceptowane zdjęcia)</small>' . PHP_EOL .
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
