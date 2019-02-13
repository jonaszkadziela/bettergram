<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $comments_tab_errors = [];

  if (empty($tab))
  {
    $comments_tab_errors[] = 'Nie zdefiniowano podstrony!';
  }

  if (count($comments_tab_errors) == 0 && $tab == 'comments')
  {
    $comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : null;

    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    switch ($filter)
    {
      case 'all':
      case 'unverified':
      break;

      default:
        header('Location: ' . ROOT_URL . modify_get_parameters(['filter' => 'all']));
        exit();
    }

    if (empty($comment_id))
    {
      echo
        '<div class="rounded border bg-light my-1-5 p-1">' . PHP_EOL .
          '<div class="row">' . PHP_EOL .
            '<div class="col-12 col-md-auto d-flex flex-column justify-content-center px-md-0-5 ml-auto">' . PHP_EOL .
              '<label class="m-md-0" for="select_filter">Wyświetl</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="col-12 col-md-6 px-md-0-5 mr-auto">' . PHP_EOL .
              '<select id="select_filter" class="js-select-links custom-select">' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['filter' => 'all']) . '"' . ($filter == 'all' ? ' selected' : '') . '>Wszystkie komentarze</option>' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['filter' => 'unverified']) . '"' . ($filter == 'unverified' ? ' selected' : '') . '>Niezaakceptowane komentarze</option>' . PHP_EOL .
              '</select>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL;

      $comments = [];

      if ($filter == 'all')
      {
        $query =
          'SELECT
            pc.id AS comment_id,
            pc.comment AS comment_comment,
            pc.date AS comment_date,
            pc.verified AS comment_verified,
            u.login AS user_login,
            u.email AS user_email,
            p.id AS photo_id,
            p.album_id AS photo_album_id
          FROM
            photos_comments AS pc
          JOIN photos AS p
          ON
            pc.photo_id = p.id
          JOIN users AS u
          ON
            pc.user_id = u.id;';
        $result = $db->prepared_select_query($query);
      }
      else if ($filter == 'unverified')
      {
        $query =
          'SELECT
            pc.id AS comment_id,
            pc.comment AS comment_comment,
            pc.date AS comment_date,
            pc.verified AS comment_verified,
            u.login AS user_login,
            u.email AS user_email,
            p.id AS photo_id,
            p.album_id AS photo_album_id
          FROM
            photos_comments AS pc
          JOIN photos AS p
          ON
            pc.photo_id = p.id
          JOIN users AS u
          ON
            pc.user_id = u.id
          WHERE
            pc.verified = 0;';
        $result = $db->prepared_select_query($query);
      }

      for ($i = 0; $i < ($result ? count($result) : 0); $i++)
      {
        $comments[] = new Comment
        (
          $result[$i]['comment_id'],
          $result[$i]['comment_comment'],
          $result[$i]['comment_date'],
          $result[$i]['comment_verified'],
          null,
          null
        );
        $comments[$i]->photo = new Photo
        (
          $result[$i]['photo_id'],
          null,
          null,
          null,
          $result[$i]['photo_album_id']
        );
        $comments[$i]->author = new User
        (
          null,
          $result[$i]['user_login'],
          $result[$i]['user_email'],
          null,
          null,
          null
        );
      }

      echo
        '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
          '<div class="card-body">' . PHP_EOL;
      if (count($comments) > 0)
      {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';
        switch ($sort)
        {
          case 'author':
          case 'date_asc':
          case 'date_desc':
            usort($comments, ['Comment', 'compare_' . $sort]);
          break;

          default:
            header('Location: ' . ROOT_URL . modify_get_parameters(['sort' => 'date_asc']));
            exit();
        }

        if ($filter == 'all')
        {
          echo '<h3 class="mb-1-5">Wszystkie komentarze</h3>' . PHP_EOL;
        }
        else if ($filter == 'unverified')
        {
          echo '<h3 class="mb-1-5">Niezaakceptowane komentarze</h3>' . PHP_EOL;
        }

        echo '<div class="mb-1-5">' . PHP_EOL;
        $sorting_options = ['author', 'date_asc', 'date_desc'];
        include VIEWS_PATH . 'shared/sorting.php';
        echo '</div>' . PHP_EOL;

        $comments_link = get_url();
        $comments_thumbnail = true;
        include VIEWS_PATH . 'comments/comments.php';
      }
      else
      {
        if ($filter == 'all')
        {
          echo '<h3 class="m-0">Brak komentarzy</h3>' . PHP_EOL;
        }
        else if ($filter == 'unverified')
        {
          echo '<h3 class="m-0">Brak komentarzy do zaakceptowania</h3>' . PHP_EOL;
        }
      }
      echo
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
          '<span class="text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }

    if (!empty($comment_id))
    {
      $query =
        'SELECT
          pc.id AS comment_id,
          pc.comment AS comment_comment,
          pc.date AS comment_date,
          pc.verified AS comment_verified,
          pc.user_id AS comment_user_id,
          p.id AS photo_id,
          p.album_id AS photo_album_id,
          u.login AS user_login,
          u.email AS user_email
        FROM
          photos_comments AS pc
        JOIN photos AS p
        ON
          pc.photo_id = p.id
        JOIN users AS u
        ON
          pc.user_id = u.id
        WHERE
          pc.id = ?;';
      $result = $db->prepared_select_query($query, [$comment_id]);

      if ($result && count($result) > 0)
      {
        $photo = new Photo
        (
          $result[0]['photo_id'],
          null,
          null,
          null,
          $result[0]['photo_album_id']
        );
        $comment = new Comment
        (
          $result[0]['comment_id'],
          $result[0]['comment_comment'],
          $result[0]['comment_date'],
          $result[0]['comment_verified'],
          null,
          $result[0]['comment_user_id']
        );
        $comment->author = new User
        (
          $result[0]['comment_user_id'],
          $result[0]['user_login'],
          $result[0]['user_email'],
          null,
          null,
          null
        );
      }
      else
      {
        $_SESSION['alert'][] = '<h5>Błąd!</h5> Podany komentarz nie istnieje!';
        header('Location: ' . ROOT_URL . modify_get_parameters(['comment_id' => null]));
        exit();
      }
      echo
        '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
          '<div class="card-body">' . PHP_EOL;

      // Redirect user to the following URL after deleting a comment
      if ($filter == 'all')
      {
        $_SESSION['redirect_url'] = ROOT_URL . '?page=admin_panel&tab=comments&filter=all';
      }
      if ($filter == 'unverified')
      {
        $_SESSION['redirect_url'] = ROOT_URL . '?page=admin_panel&tab=comments&filter=unverified';
      }
      $update_comment_form_mode = 'privileged';
      include VIEWS_PATH . 'comments/update_comment_form.php';

      echo
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
          '<span class="text-muted">Nie ten komentarz?</span>' . PHP_EOL .
          '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . modify_get_parameters(['comment_id' => null]) . '">Wybierz jeszcze raz!</a>' . PHP_EOL .
        '</div>' . PHP_EOL;
    }
  }

  if (count($comments_tab_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić podstrony komentarzy, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($comments_tab_errors); $i++)
    {
      echo '<li>' . $comments_tab_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
