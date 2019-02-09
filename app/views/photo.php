<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'user/functions.php');
  require_once(BACKEND_PATH . 'photo/functions.php');
  require_once(BACKEND_PATH . 'album/functions.php');
  require_once(BACKEND_PATH . 'comment/functions.php');
  require_once(BACKEND_PATH . 'rating/functions.php');

  $errors = array();

  if (empty($_GET['photo_id']))
  {
    $errors[] = 'Nie zdefiniowano ID zdjęcia!';
  }

  if (count($errors) == 0)
  {
    $photo_id = $_GET['photo_id'];
    $photo = get_photo($photo_id);

    if ($photo != null)
    {
      $album = get_album($photo->album_id);

      if ($album != null)
      {
        $album->author = get_user($album->user_id);
        $comments = get_comments($photo_id);
        $comments_verified = $comments;

        if ($comments != null)
        {
          for ($i = 0; $i < count($comments); $i++)
          {
            $comments[$i]->author = get_user($comments[$i]->user_id);
            if ((!$comments[$i]->verified && !isset($_SESSION['user_id'])) ||
                (!$comments[$i]->verified && isset($_SESSION['user_id']) && $_SESSION['user_id'] != $comments[$i]->author->id))
            {
              $comments_verified = array_diff($comments_verified, array($comments[$i]));
            }
          }
          $comments = array_values($comments_verified);

          $sort = 'date_asc';
          if (isset($_GET['sort']))
          {
            $sort = $_GET['sort'];
          }

          switch ($sort)
          {
            case 'author':
              usort($comments, array('Comment', 'compare_author_login'));
            break;

            case 'date_asc':
              usort($comments, array('Comment', 'compare_date_asc'));
            break;

            case 'date_desc':
            default:
              usort($comments, array('Comment', 'compare_date_desc'));
          }
        }
      }
      else
      {
        $errors[] = 'Album, do którego należało to zdjęcie został usunięty!';
      }

      if (!$photo->verified)
      {
        if ($album->author->id == $_SESSION['user_id'])
        {
          $_SESSION['alert'] = 'To zdjęcie nie zostało jeszcze zaakceptowane, dlatego jest niewidoczne publicznie.';
          $_SESSION['alert_class'] = 'alert-info';
        }
        else
        {
          $errors[] = 'Żądane zdjęcie nie zostało jeszcze zaakceptowane!';
        }
      }
    }
    else
    {
      $errors[] = 'Żądane zdjęcie nie istnieje!';
    }
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Nie można wyświetlić zdjęcia, gdyż:</h5>' . PHP_EOL .
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
      <div class="col-sm-12 col-md-10 m-auto">
        <?php
          include_once(COMPONENTS_PATH . 'alert.php');
        ?>
        <div class="card p-1 shadow-lg">
          <div class="card-body">
            <h2 class="d-flex card-title underline underline-primary mb-1-75">Zdjęcie z albumu</h2>
            <?php
              echo
                '<h4 class="text-center mb-1-5">"' . $album->title . '"</h4>' . PHP_EOL .
                '<div class="d-flex flex-column justify-content-center">' . PHP_EOL .
                  '<div class="card rounded-bottom-0">' . PHP_EOL .
                    '<div class="js-spinner d-flex justify-content-center align-items-center card-img-overlay bg-dark rounded">' . PHP_EOL .
                      '<i class="fas fa-spinner fa-3x fa-spin text-light"></i>' . PHP_EOL .
                    '</div>' . PHP_EOL .
                    '<img class="object-fit-contain w-100 mh-800px m-auto rounded-top" src="#" data-src="' . $photo->get_path() . '" alt="Zdjęcie">' . PHP_EOL .
                  '</div>' . PHP_EOL .
                  '<div class="card border-top-0 rounded-top-0 bg-light text-center">' . PHP_EOL .
                    '<div class="card-body d-flex flex-column justify-content-center">' . PHP_EOL .
                      '<p class="h5 font-weight-bold">' . (strlen($photo->description) > 0 ? 'Opis zdjęcia' : 'To zdjęcie nie posiada opisu') . '</p>' . PHP_EOL .
                      (strlen($photo->description) > 0 ? '<p class="card-text mb-0-25">' . $photo->description . '</p>' : '') . PHP_EOL .
                      '<p class="card-text font-weight-bold mb-0">' . $album->author . '</p>' . PHP_EOL .
                    '</div>' . PHP_EOL .
                    '<div class="card-footer bg-dark text-light">' . PHP_EOL .
                      '<p class="m-0">Dodano ' . date_format(date_create($photo->date), 'd.m.Y') . ' o godzinie ' . date_format(date_create($photo->date), 'G:i') . '</p>' . PHP_EOL .
                    '</div>' . PHP_EOL .
                    '<div class="card-footer bg-secondary">' . PHP_EOL .
                      '<p class="js-rating-result h4 text-white py-0-5 m-0">' . PHP_EOL .
                        render_rating($photo_id) .
                      '</p>' . PHP_EOL;
              include_once(COMPONENTS_PATH . 'photo_rating_form.php');
              echo
                    '</div>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL;

              echo
                '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                  '<div class="card-body">' . PHP_EOL;
              if (count($comments) > 0)
              {
                echo
                    '<h3 class="text-center mb-1-5">Komentarze</h3>' . PHP_EOL .
                    '<div class="text-center mb-1-5">' . PHP_EOL .
                      '<div class="row">' . PHP_EOL .
                        '<div class="col-12 col-md-auto d-flex flex-column justify-content-center px-md-0-5 ml-auto">' . PHP_EOL .
                          '<label class="m-md-0" for="select_sort">Sortuj według</label>' . PHP_EOL .
                        '</div>' . PHP_EOL .
                        '<div class="col-12 col-md-6 px-md-0-5 mr-auto">' . PHP_EOL .
                          '<select id="select_sort" class="js-select-links custom-select">' . PHP_EOL .
                            '<option value="' . modify_url_parameters(array('sort' => 'author')) . '"' . ($sort == 'author' ? ' selected' : '') . '>Nazwy autora</option>' . PHP_EOL .
                            '<option value="' . modify_url_parameters(array('sort' => 'date_asc')) . '"' . ($sort == 'date_asc' ? ' selected' : '') . '>Daty (od najnowszych do najstarszych)</option>' . PHP_EOL .
                            '<option value="' . modify_url_parameters(array('sort' => 'date_desc')) . '"' . ($sort == 'date_desc' ? ' selected' : '') . '>Daty (od najstarszych do najnowszych)</option>' . PHP_EOL .
                          '</select>' . PHP_EOL .
                        '</div>' . PHP_EOL .
                      '</div>' . PHP_EOL .
                    '</div>' . PHP_EOL;
                for ($i = 0; $i < count($comments); $i++)
                {
                  echo
                    '<div class="media bg-white shadow-sm rounded p-1 mt-1 position-relative">' . PHP_EOL .
                      '<div class="d-none d-md-flex w-64px h-64px border rounded-circle p-1 mr-1">' . PHP_EOL .
                        '<i class="fas fa-user fa-2x m-auto"></i>' . PHP_EOL .
                      '</div>' . PHP_EOL .
                      '<div class="media-body">' . PHP_EOL .
                        '<p class="h5 text-center text-md-left font-weight-bold m-0">' . $comments[$i]->author->login . '</p>' . PHP_EOL .
                        '<p class="text-center text-md-left my-0-5">' . $comments[$i]->comment . '</p>' . PHP_EOL .
                        '<small class="d-block text-center text-md-right text-muted">Dodano ' . date_format(date_create($comments[$i]->date), 'd.m.Y') . ' o godzinie ' . date_format(date_create($comments[$i]->date), 'G:i') . '</small>' . PHP_EOL;
                  if (!$comments[$i]->verified)
                  {
                    echo
                      '<summary class="position-absolute position-top-right m-0-5" data-toggle="tooltip" data-html="true"' .
                      'title="Ten komentarz nie został jeszcze zaakceptowany, dlatego <u>nie jest widoczny</u> publicznie.">' . PHP_EOL .
                        '<i class="fas fa-question-circle fa-lg fa-fw"></i>' . PHP_EOL .
                      '</summary>' . PHP_EOL;
                  }
                  echo
                      '</div>' . PHP_EOL .
                    '</div>' . PHP_EOL;
                }
              }
              else
              {
                $_SESSION['target_url'] = get_url() . '#comment';
                echo
                    '<div class="text-center">' . PHP_EOL .
                      '<h3 class="card-title m-0">To zdjęcie nie posiada jeszcze komentarzy</h3>' . PHP_EOL .
                      '<small class="d-block text-muted mb-1">(wyświetlane są tylko zaakceptowane komentarze)</small>' . PHP_EOL .
                      '<p class="mb-0">Bądź pierwszą osobą, która skomentuje to zdjęcie.</p>' . PHP_EOL .
                      '<a class="underline underline--narrow underline-primary underline-animation" href="' . (isset($_SESSION['user_signed_in']) ? get_url() . '#comment' : get_url(false) . '?view=login&redirect=1') . '">Dodaj komentarz już dziś!</a>' . PHP_EOL .
                    '</div>' . PHP_EOL;
              }
              echo
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL;
              include_once(COMPONENTS_PATH . 'create_comment_form.php');
            ?>
            <div class="mt-1-5 pt-0-5 text-center">
              <span class="card-text text-muted">Nie to zdjęcie?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL ?>?view=album&album_id=<?php echo $photo->album_id ?>">Wybierz inne!</a>
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
