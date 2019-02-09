<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'photo/functions.php');
  require_once(BACKEND_PATH . 'album/functions.php');

  $errors = array();

  if (empty($_GET['album_id']))
  {
    $errors[] = 'Nie zdefiniowano ID albumu!';
  }

  if (count($errors) == 0)
  {
    $album_id = $_GET['album_id'];
    $album = get_album($album_id);

    if ($album != null)
    {
      $album->photos = get_photos($album_id, 'verified=1');
      usort($album->photos, array('Photo', 'compare_date_asc'));

      if (count($album->photos) > PHOTO_PAGINATION_THRESHOLD)
      {
        $page = 1;
        $page_count = ceil(count($album->photos) / PHOTO_PAGINATION_THRESHOLD);
        if (isset($_GET['page']))
        {
          $page = $_GET['page'];
          $offset = ($page - 1) * PHOTO_PAGINATION_THRESHOLD;
          if ($page > 0 && $offset < count($album->photos))
          {
            $album->photos = array_slice($album->photos, $offset, PHOTO_PAGINATION_THRESHOLD);
          }
          else
          {
            $errors[] = 'Żądany fragment listy zdjęć nie istnieje.';
          }
        }
        else
        {
          $album->photos = array_slice($album->photos, 0, PHOTO_PAGINATION_THRESHOLD);
        }
      }
    }
    else
    {
      unset($album_id);
      $errors[] = 'Podany album nie istnieje!';
    }
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Wystąpiły następujące błędy:</h5>' . PHP_EOL .
      '<ul class="mb-0">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $_SESSION['alert'] .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $_SESSION['alert'] .= '</ul>' . PHP_EOL;
    $url = ROOT_URL . (empty($album_id) ? '?view=gallery' : '?view=album&album_id=' . $album_id);
    header('Location: ' . $url);
    exit();
  }
?>
<div class="d-flex flex-column h-100vh bg-img-1">
  <?php
    include_once(COMPONENTS_PATH . 'navbar.php');
  ?>
  <div class="container d-flex flex-grow-1 flex-column h-100 my-3">
    <div class="row flex-grow-1">
      <div class="col-lg-12 col-xl-10 m-auto">
        <?php
          include_once(COMPONENTS_PATH . 'alert.php');
        ?>
        <div class="card p-1 shadow-lg">
          <div class="card-body">
            <h2 class="d-flex card-title underline underline-primary mb-1-75">Album</h2>
            <?php
              echo
                '<h4 class="text-center mb-1-5">"' . $album->title . '"</h4>' . PHP_EOL .
                '<div class="card text-center my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                  '<div class="card-body">' . PHP_EOL;
              if (count($album->photos) > 0)
              {
                echo
                    '<h2 class="card-title mb-0-25">Zdjęcia</h2>' . PHP_EOL .
                    '<div class="row justify-content-center">';
                for ($i = 0; $i < count($album->photos); $i++)
                {
                  echo
                      '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 mt-1-5">' . PHP_EOL .
                        '<a class="d-block link--clean w-180px m-auto" href="' . ROOT_URL . '?view=photo&photo_id=' . $album->photos[$i]->id . '">' . PHP_EOL .
                          '<div class="card shadow">' . PHP_EOL .
                            '<div class="js-spinner d-flex justify-content-center align-items-center card-img-overlay bg-dark rounded">' . PHP_EOL .
                              '<i class="fas fa-spinner fa-3x fa-spin text-light"></i>' . PHP_EOL .
                            '</div>' . PHP_EOL .
                            '<img class="h-180px object-fit-cover darken rounded" src="#" data-src="' . $album->photos[$i]->get_path('thumbnail') . '" alt="Zdjęcie">' . PHP_EOL .
                          '</div>' . PHP_EOL .
                        '</a>' . PHP_EOL .
                      '</div>' . PHP_EOL;
                }
                echo '</div>' . PHP_EOL;
              }
              else
              {
                echo
                    '<h3 class="card-title m-0">Ten album jest pusty</h3>' . PHP_EOL .
                    '<small class="d-block text-muted mb-1">(wyświetlane są tylko zaakceptowane zdjęcia)</small>' . PHP_EOL .
                    '<p class="mb-0">Aktualnie ten album nie posiada żadnych zdjęć. Spróbuj ponownie później!</p>' . PHP_EOL;
              }
              if (isset($page_count))
              {
                echo '<div class="py-0-25">' . PHP_EOL;
                include_once(COMPONENTS_PATH . 'pagination.php');
                echo '</div>' . PHP_EOL;
              }
              echo
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL;
            ?>
            <div class="mt-1-5 pt-0-5 text-center">
              <span class="card-text text-muted">Nie ten album?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL ?>?view=gallery">Wybierz inny!</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    include_once(COMPONENTS_PATH . 'footer.php');
  ?>
</div>
