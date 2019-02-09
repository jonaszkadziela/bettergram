<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();
  $errors = [];

  if (empty($_GET['album_id']))
  {
    $errors[] = 'Nie zdefiniowano ID albumu!';
  }

  if (count($errors) == 0)
  {
    $album_id = $_GET['album_id'];
    $photos = [];
    $query =
      'SELECT
        a.title AS album_title,
        p.id AS photo_id,
        p.date AS photo_date
      FROM
        albums AS a
      JOIN photos AS p
      ON
        a.id = p.album_id
      WHERE
        a.id = ? AND p.verified = 1;';
    $result = $db->prepared_select_query($query, [$album_id]);

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
        $album->photos[] = new Photo
        (
          $result[$i]['photo_id'],
          null,
          $result[$i]['photo_date'],
          null,
          $album_id
        );
      }
      usort($album->photos, ['Photo', 'compare_date_asc']);
    }
    else
    {
      $errors[] = 'Podany album nie istnieje lub jest pusty!';
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
    header('Location: ' . ROOT_URL . '?page=gallery');
    exit();
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
            <h2 class="underline underline-primary mb-1-5">Album</h2>
            <?php
              echo
                '<h4 class="mb-1-5">"' . $album->title . '"</h4>' . PHP_EOL .
                '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                  '<div class="card-body">' . PHP_EOL .
                  '<h2 class="mb-0-25">Zdjęcia</h2>' . PHP_EOL;
              $photos = $album->photos;
              $photos_class = 'mt-1-5';
              include VIEWS_PATH . 'photos/photos.php';
              echo
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL;
            ?>
            <div class="mt-1-5 pt-0-5">
              <span class="text-muted">Nie ten album?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL ?>?page=gallery">Wybierz inny!</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    include_once VIEWS_PATH . 'shared/footer.php';
  ?>
</div>
