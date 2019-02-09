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
    $albums = array();

    if (empty($_SESSION['user_id']))
    {
      $errors[] = 'Nie udało się ustalić ID użytkownika!';
    }

    if (count($errors) == 0)
    {
      $albums = get_albums($_SESSION['user_id'], true);
    }
  }
  else
  {
    $album_id = $_GET['album_id'];
    $album = get_album($album_id);

    if ($album != null)
    {
      if ($album->user_id != $_SESSION['user_id'])
      {
        $_SESSION['alert'] = '<strong>Błąd!</strong> Nie masz uprawnień do tego albumu!';
        header('Location: ' . ROOT_URL);
        exit();
      }

      $album->photos = get_photos($album_id);
    }
    else
    {
      $_SESSION['alert'] = '<strong>Błąd!</strong> Podany album nie istnieje!';
      header('Location: ' . ROOT_URL);
      exit();
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
            <?php
              if (empty($album_id))
              {
                if (count($albums) > 0)
                {
                  echo
                    '<h2 class="d-flex card-title underline underline-primary mb-1-75">Wybierz album</h2>' . PHP_EOL .
                    '<div class="row justify-content-center">' . PHP_EOL;
                  for ($i = 0; $i < count($albums); $i++)
                  {
                    echo
                      '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 my-1">' . PHP_EOL .
                        '<a class="d-block link--clean w-180px h-100 m-auto" href="' . ROOT_URL . '?view=create_photo&album_id=' . $albums[$i]->id . '">' . PHP_EOL .
                          '<div class="card h-100 shadow">' . PHP_EOL;
                    if (count($albums[$i]->photos) > 0)
                    {
                      echo
                        '<div class="js-spinner h-180px d-flex justify-content-center align-items-center card-img-overlay bg-dark rounded-top">' . PHP_EOL .
                          '<i class="fas fa-spinner fa-3x fa-spin text-light"></i>' . PHP_EOL .
                        '</div>' . PHP_EOL .
                        '<img class="h-180px object-fit-cover darken card-img-top" src="#" data-src="' . $albums[$i]->photos[0]->get_path('thumbnail') . '" alt="Okładka albumu">' . PHP_EOL;
                    }
                    else
                    {
                      echo
                            '<div class="d-flex h-180px card-img-top darken bg-dark text-light">' . PHP_EOL .
                              '<i class="fas fa-images fa-5x m-auto"></i>' . PHP_EOL .
                            '</div>'. PHP_EOL;
                    }
                    echo
                            '<div class="d-flex flex-column justify-content-center card-body border-top">' . PHP_EOL .
                              '<p class="text-center m-0">' . truncate($albums[$i]->title, 40) . '</p>' . PHP_EOL .
                            '</div>' . PHP_EOL .
                          '</div>' . PHP_EOL .
                        '</a>' . PHP_EOL .
                      '</div>' . PHP_EOL;
                  }
                  echo
                    '</div>' . PHP_EOL .
                    '<div class="mt-1-5 text-center">' . PHP_EOL .
                      '<span class="card-text text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
                    '</div>' . PHP_EOL;
                }
                else
                {
                  echo
                    '<div class="text-center">' . PHP_EOL .
                      '<h2 class="d-flex card-title underline underline-primary mb-1-75">Brak albumów</h2>' . PHP_EOL .
                      '<p class="mb-0-25">Nie posiadasz jeszcze żadnych albumów.</p>' . PHP_EOL .
                      '<p class="mb-1-5">Utwórz swój pierwszy album już dziś!</p>' . PHP_EOL .
                      '<a href="' . ROOT_URL . '?view=create_album" class="btn btn-primary">Załóż album</a>' . PHP_EOL .
                    '</div>' . PHP_EOL;
                }
              }
              else
              {
                echo
                  '<h2 class="d-flex card-title underline underline-primary mb-1-75">Album</h2>' . PHP_EOL .
                  '<h4 class="text-center mb-1-5">"' . $album->title . '"</h4>' . PHP_EOL .
                  '<div class="card text-center my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                    '<div class="card-body">' . PHP_EOL;
                if (count($album->photos) > 0)
                {
                  echo
                    '<h2 class="card-title mb-0-25">Zdjęcia</h2>' . PHP_EOL .
                    '<div class="row justify-content-center">' . PHP_EOL;
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
                    '<h3 class="card-title mb-0-25">Ten album jest pusty</h3>' . PHP_EOL .
                    '<a class="underline underline--narrow underline-primary underline-animation" href="#photo">Dodaj pierwsze zdjęcie do tego albumu już dziś!</a>' . PHP_EOL;
                }
                echo
                    '</div>' . PHP_EOL .
                  '</div>' . PHP_EOL;

                include_once(COMPONENTS_PATH . 'create_photo_form.php');

                echo
                  '<div class="mt-1-5 pt-0-5 text-center">' . PHP_EOL .
                    '<span class="card-text text-muted">Nie ten album?</span>' . PHP_EOL .
                    '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?view=create_photo">Wybierz jeszcze raz!</a>' . PHP_EOL .
                  '</div>' . PHP_EOL;
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    include_once(COMPONENTS_PATH . 'footer.php');
  ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xregexp/3.2.0/xregexp-all.min.js"></script>
<script>loadScript("<?php echo ASSETS_URL . 'javascripts/validation.js' ?>");</script>
<script>loadScript("<?php echo ASSETS_URL . 'javascripts/validation_create_photo_form.js' ?>");</script>
