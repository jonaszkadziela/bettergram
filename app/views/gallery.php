<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'shared/classes.php');

  $errors = array();
  $albums = array();
  $sql = "SELECT albums.*, photos.id AS photo_id, users.login AS user_login FROM albums, photos, users WHERE albums.id = photos.album_id AND photos.verified=1 AND albums.user_id = users.id GROUP BY albums.id";
  $result = custom_database_query($sql);

  if (count($result) > 0)
  {
    for ($i = 0; $i < count($result); $i++)
    {
      $albums[$i] = new Album
      (
        $result[$i]['id'],
        $result[$i]['title'],
        $result[$i]['date'],
        $result[$i]['user_id']
      );
      $albums[$i]->author = User::unknown_user();
      $albums[$i]->author->login = $result[$i]['user_login'];
      $albums[$i]->photos[0] = new Photo
      (
        $result[$i]['photo_id'],
        null,
        null,
        1,
        $albums[$i]->id
      );
    }

    $sort = 'title';
    if (isset($_GET['sort']))
    {
      $sort = $_GET['sort'];
    }

    switch ($sort)
    {
      case 'title':
      default:
        usort($albums, array('Album', 'compare_title'));
      break;

      case 'author':
        usort($albums, array('Album', 'compare_author_login'));
      break;

      case 'date_asc':
        usort($albums, array('Album', 'compare_date_asc'));
      break;

      case 'date_desc':
        usort($albums, array('Album', 'compare_date_desc'));
      break;
    }

    if (count($albums) > ALBUM_PAGINATION_THRESHOLD)
    {
      $page = 1;
      $page_count = ceil(count($albums) / ALBUM_PAGINATION_THRESHOLD);
      if (isset($_GET['page']))
      {
        $page = $_GET['page'];
        $offset = ($page - 1) * ALBUM_PAGINATION_THRESHOLD;
        if ($page > 0 && $offset < count($albums))
        {
          $albums = array_slice($albums, $offset, ALBUM_PAGINATION_THRESHOLD);
        }
        else
        {
          $errors[] = 'Żądany fragment listy albumów nie istnieje.';
        }
      }
      else
      {
        $albums = array_slice($albums, 0, ALBUM_PAGINATION_THRESHOLD);
      }
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
    header('Location: ' . ROOT_URL . '?view=gallery');
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
          <div class="card-body text-center">
            <?php
              if (count($albums) > 0)
              {
                echo
                  '<div class="text-center mb-1">' . PHP_EOL .
                    '<h2 class="d-flex card-title underline underline-primary mb-1-75">Galeria</h2>' . PHP_EOL .
                    '<div class="row py-0-25">' . PHP_EOL .
                      '<div class="col-12 col-md-auto d-flex flex-column justify-content-center px-md-0-5 ml-auto">' . PHP_EOL .
                        '<label class="m-md-0" for="select_sort">Sortuj według</label>' . PHP_EOL .
                      '</div>' . PHP_EOL .
                      '<div class="col-12 col-md-6 px-md-0-5 mr-auto">' . PHP_EOL .
                        '<select id="select_sort" class="js-select-links custom-select">' . PHP_EOL .
                          '<option value="' . modify_url_parameters(array('sort' => 'title')) . '"' . ($sort == 'title' ? ' selected' : '') . '>Tytułu albumu</option>' . PHP_EOL .
                          '<option value="' . modify_url_parameters(array('sort' => 'author')) . '"' . ($sort == 'author' ? ' selected' : '') . '>Nazwy autora</option>' . PHP_EOL .
                          '<option value="' . modify_url_parameters(array('sort' => 'date_asc')) . '"' . ($sort == 'date_asc' ? ' selected' : '') . '>Daty (od najnowszych do najstarszych)</option>' . PHP_EOL .
                          '<option value="' . modify_url_parameters(array('sort' => 'date_desc')) . '"' . ($sort == 'date_desc' ? ' selected' : '') . '>Daty (od najstarszych do najnowszych)</option>' . PHP_EOL .
                        '</select>' . PHP_EOL .
                      '</div>' . PHP_EOL .
                    '</div>' . PHP_EOL .
                  '</div>' . PHP_EOL;

                echo '<div class="row justify-content-center">' . PHP_EOL;
                for ($i = 0; $i < count($albums); $i++)
                {
                  echo
                    '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 my-1">' . PHP_EOL .
                      '<a class="d-block link--clean w-180px m-auto" href="' . ROOT_URL . '?view=album&album_id=' . $albums[$i]->id . '" ' .
                      'data-toggle="tooltip" data-html="true" title="' .
                      '&quot;' . $albums[$i]->title . '&quot;<br>' .
                      '<b>' . $albums[$i]->author . '</b><br>' .
                      '(' . date_format(date_create($albums[$i]->date), 'd.m.Y') . ' ' . date_format(date_create($albums[$i]->date), 'G:i') . ')' .
                      '">' . PHP_EOL .
                        '<div class="card shadow">' . PHP_EOL .
                          '<div class="js-spinner d-flex justify-content-center align-items-center card-img-overlay bg-dark rounded">' . PHP_EOL .
                            '<i class="fas fa-spinner fa-3x fa-spin text-light"></i>' . PHP_EOL .
                          '</div>' . PHP_EOL .
                          '<img class="h-180px object-fit-cover darken rounded" src="#" data-src="' . $albums[$i]->photos[0]->get_path('thumbnail') . '" alt="Okładka albumu">' . PHP_EOL .
                        '</div>' . PHP_EOL .
                      '</a>' . PHP_EOL .
                    '</div>' . PHP_EOL;
                }
                echo '</div>' . PHP_EOL;

                if (isset($page_count))
                {
                  include_once(COMPONENTS_PATH . 'pagination.php');
                }
              }
              else
              {
                echo
                  '<h3 class=" m-0">Nie ma jeszcze żadnych albumów do wyświetlenia</h3>' . PHP_EOL .
                  '<small class="d-block text-muted mb-1-5">(wyświetlane są tylko albumy, które posiadają przynajmniej jedno zaakceptowane zdjęcie)</small>' . PHP_EOL .
                  '<p class=" mb-1-5">Bądź pierwszym użytkownikiem, który stworzy album i zamieści w nim zdjęcie!</p>' . PHP_EOL;

                if (isset($_SESSION['user_signed_in']))
                {
                  echo
                    '<p class="mb-0">' . PHP_EOL .
                      '<span class="card-text text-muted">Nie posiadasz jeszcze albumu?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?view=create_album">Załóż album!</a>' . PHP_EOL .
                    '</p>' . PHP_EOL .
                    '<p class="mb-0">' . PHP_EOL .
                      '<span class="card-text text-muted">Twój album jest pusty?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?view=create_photo">Dodaj do niego zdjęcie!</a>' . PHP_EOL .
                    '</p>' . PHP_EOL;
                }
                else
                {
                  $_SESSION['target_url'] = get_url(false) . '?view=create_album';
                  echo
                    '<p class="mb-0">' . PHP_EOL .
                      '<span class="card-text text-muted">Jesteś tu po raz pierwszy?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?view=register&redirect=1">Stwórz nowe konto!</a>' . PHP_EOL .
                    '</p>' . PHP_EOL .
                    '<p class="mb-0">' . PHP_EOL .
                      '<span class="card-text text-muted">Posiadasz już konto?</span>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '?view=login&redirect=1">Zaloguj się!</a>' . PHP_EOL .
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
    include_once(COMPONENTS_PATH . 'footer.php');
  ?>
</div>
