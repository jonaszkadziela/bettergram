<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();
  $errors = [];

  if (empty($_GET['photo_id']))
  {
    $errors[] = 'Nie zdefiniowano ID zdjęcia!';
  }

  if (count($errors) == 0)
  {
    $photo_id = $_GET['photo_id'];
    $user_id = isset($_SESSION['current_user']['id']) ? $_SESSION['current_user']['id'] : null;

    $query =
      'SELECT
        p.id AS photo_id,
        p.description AS photo_description,
        p.date AS photo_date,
        p.verified AS photo_verified,
        a.id AS album_id,
        a.title AS album_title,
        a.user_id AS album_user_id,
        u.login AS user_login
      FROM
        photos AS p
      JOIN albums AS a
      ON
        p.album_id = a.id
      JOIN users AS u
      ON
        a.user_id = u.id
      WHERE
        p.id = ?;';
    $result = $db->prepared_select_query($query, [$photo_id]);

    if ($result && count($result) > 0)
    {
      $album = new Album
      (
        $result[0]['album_id'],
        $result[0]['album_title'],
        null,
        $result[0]['album_user_id']
      );
      $album->author->login = $result[0]['user_login'];
      $photo = new Photo
      (
        $result[0]['photo_id'],
        $result[0]['photo_description'],
        $result[0]['photo_date'],
        $result[0]['photo_verified'],
        $album->id
      );

      $query =
        'SELECT
          pc.id AS comment_id,
          pc.comment AS comment_comment,
          pc.date AS comment_date,
          pc.verified AS comment_verified,
          pc.user_id AS comment_user_id,
          u.login AS user_login
        FROM
          photos_comments AS pc
        JOIN users AS u
        ON
          pc.user_id = u.id
        JOIN photos AS p
        ON
          pc.photo_id = p.id
        WHERE
          p.id = ? AND (pc.verified = 1 OR (pc.verified = 0 AND pc.user_id = ?))';
      $result = $db->prepared_select_query($query, [$photo_id, $user_id]);

      for ($i = 0; $i < ($result ? count($result) : 0); $i++)
      {
        $photo->comments[] = new Comment
        (
          $result[$i]['comment_id'],
          $result[$i]['comment_comment'],
          $result[$i]['comment_date'],
          $result[$i]['comment_verified'],
          $photo_id,
          $result[$i]['comment_user_id']
        );
        end($photo->comments)->author->login = $result[$i]['user_login'];
      }
    }
    else
    {
      $errors[] = 'Żądane zdjęcie nie istnieje!';
    }

    if (count($errors) == 0)
    {
      if (count($photo->comments) > 0)
      {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';
        switch ($sort)
        {
          case 'author':
          case 'date_asc':
          case 'date_desc':
            usort($photo->comments, ['Comment', 'compare_' . $sort]);
          break;

          default:
            header('Location: ' . ROOT_URL . modify_get_parameters(['sort' => 'date_asc']));
            exit();
        }
      }

      if (!$photo->verified)
      {
        if ($album->user_id == $_SESSION['current_user']['id'])
        {
          $_SESSION['notice'][] = 'To zdjęcie nie zostało jeszcze zaakceptowane, dlatego jest niewidoczne publicznie.';
        }
        else
        {
          $errors[] = 'Żądane zdjęcie nie zostało jeszcze zaakceptowane!';
        }
      }
    }
  }

  if (count($errors) > 0)
  {
    $alert =
      '<h5>Nie można wyświetlić zdjęcia, gdyż:</h5>' . PHP_EOL .
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
      <div class="col-sm-12 col-md-10 m-auto">
        <?php
          include_once VIEWS_PATH . 'shared/flash.php';
        ?>
        <div class="card p-1 shadow-lg">
          <div class="card-body text-center">
            <h2 class="underline underline-primary mb-1-5">Zdjęcie z albumu</h2>
            <?php
              echo
                '<h4 class="mb-1-5">"' . $album->title . '"</h4>' . PHP_EOL .
                '<div class="d-flex flex-column justify-content-center">' . PHP_EOL;
              include VIEWS_PATH . 'photos/render_photo.php';
              echo
                  '<div class="card border-top-0 rounded-top-0 bg-light">' . PHP_EOL .
                    '<div class="card-body d-flex flex-column justify-content-center">' . PHP_EOL .
                      '<p class="h5 font-weight-bold">' . (strlen($photo->description) > 0 ? 'Opis zdjęcia' : 'To zdjęcie nie posiada opisu') . '</p>' . PHP_EOL .
                      (strlen($photo->description) > 0 ? '<p class="mb-0-25">' . $photo->description . '</p>' . PHP_EOL : '') .
                      '<p class="font-weight-bold mb-0">' . $album->author . '</p>' . PHP_EOL .
                    '</div>' . PHP_EOL .
                    '<div class="card-footer bg-dark text-light">' . PHP_EOL .
                      '<p class="m-0">Dodano ' . $photo->date->format('d.m.Y') . ' o godzinie ' . $photo->date->format('G:i') . '</p>' . PHP_EOL .
                    '</div>' . PHP_EOL .
                    '<div class="card-footer bg-secondary">' . PHP_EOL .
                      '<p class="js-rating-result h4 text-white py-0-5 m-0">' . PHP_EOL;
              include VIEWS_PATH . 'ratings/render_rating_photo.php';
              echo
                      '</p>' . PHP_EOL;
              include VIEWS_PATH . 'ratings/rating_photo_form.php';
              echo
                    '</div>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL;

              echo
                '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                  '<div class="card-body">' . PHP_EOL;
              if (count($photo->comments) > 0)
              {
                echo
                    '<h3 class="mb-1-5">Komentarze</h3>' . PHP_EOL .
                    '<div class="mb-1-5">' . PHP_EOL;
                $sorting_options = ['author', 'date_asc', 'date_desc'];
                include VIEWS_PATH . 'shared/sorting.php';
                echo '</div>' . PHP_EOL;

                $comments = $photo->comments;
                include VIEWS_PATH . 'comments/comments.php';
              }
              else
              {
                $_SESSION['target_url'] = get_url() . '#comment';
                echo
                    '<h3 class="m-0">To zdjęcie nie posiada jeszcze komentarzy</h3>' . PHP_EOL .
                    '<small class="d-block text-muted mb-1">(wyświetlane są tylko zaakceptowane komentarze)</small>' . PHP_EOL .
                    '<p class="m-0">Bądź pierwszą osobą, która skomentuje to zdjęcie.</p>' . PHP_EOL .
                    '<a class="underline underline--narrow underline-primary underline-animation" href="' . (isset($_SESSION['current_user']['logged_in']) ? get_url() . '#comment' : get_url(false) . '?page=login&redirect=1') . '">Dodaj komentarz już dziś!</a>' . PHP_EOL;
              }
              echo
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL .
                '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
                  '<div class="card-body">' . PHP_EOL;
              include VIEWS_PATH . 'comments/create_comment_form.php';
              echo
                  '</div>' . PHP_EOL .
                '</div>' . PHP_EOL;
            ?>
            <div class="mt-1-5 pt-0-5">
              <span class="text-muted">Nie to zdjęcie?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL ?>?page=album&album_id=<?php echo $photo->album_id ?>">Wybierz inne!</a>
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
