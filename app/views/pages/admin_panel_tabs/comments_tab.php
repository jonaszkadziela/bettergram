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

    $show = isset($_GET['show']) ? $_GET['show'] : 'all';
    switch ($show)
    {
      case 'all':
      case 'unverified':
      break;

      default:
        header('Location: ' . ROOT_URL . modify_get_parameters(['show' => 'all']));
        exit();
    }

    if (empty($comment_id))
    {
      echo
        '<div class="rounded border bg-light my-1-5 p-1">' . PHP_EOL .
          '<div class="row">' . PHP_EOL .
            '<div class="col-12 col-md-auto d-flex flex-column justify-content-center px-md-0-5 ml-auto">' . PHP_EOL .
              '<label class="m-md-0" for="select_show">Wyświetl</label>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<div class="col-12 col-md-6 px-md-0-5 mr-auto">' . PHP_EOL .
              '<select id="select_show" class="js-select-links custom-select">' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['show' => 'all']) . '"' . ($show == 'all' ? ' selected' : '') . '>Wszystkie komentarze</option>' . PHP_EOL .
                '<option value="' . ROOT_URL . modify_get_parameters(['show' => 'unverified']) . '"' . ($show == 'unverified' ? ' selected' : '') . '>Niezaakceptowane komentarze</option>' . PHP_EOL .
              '</select>' . PHP_EOL .
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL;

      $comments = [];

      if ($show == 'all')
      {
        $query =
          'SELECT
            pc.id AS comment_id,
            pc.comment AS comment_comment,
            pc.date AS comment_date,
            pc.verified AS comment_verified,
            pc.photo_id AS comment_photo_id,
            pc.user_id AS comment_user_id,
            u.login AS user_login
          FROM
            photos_comments AS pc
          JOIN users AS u
          ON
            pc.user_id = u.id;';
        $result = $db->prepared_select_query($query);
      }
      else if ($show == 'unverified')
      {
        $query =
          'SELECT
            pc.id AS comment_id,
            pc.comment AS comment_comment,
            pc.date AS comment_date,
            pc.verified AS comment_verified,
            u.login AS user_login
          FROM
            photos_comments AS pc
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
        $comments[$i]->author->login = $result[$i]['user_login'];
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

        if ($show == 'all')
        {
          echo '<h3 class="mb-1-5">Wszystkie komentarze</h3>' . PHP_EOL;
        }
        else if ($show == 'unverified')
        {
          echo '<h3 class="mb-1-5">Niezaakceptowane komentarze</h3>' . PHP_EOL;
        }

        echo '<div class="mb-1-5">' . PHP_EOL;
        $sorting_options = ['author', 'date_asc', 'date_desc'];
        include VIEWS_PATH . 'shared/sorting.php';
        echo '</div>' . PHP_EOL;

        $comments_link = get_url();
        include VIEWS_PATH . 'comments/comments.php';
      }
      else
      {
        if ($show == 'all')
        {
          echo '<h3 class="m-0">Brak komentarzy</h3>' . PHP_EOL;
        }
        else if ($show == 'unverified')
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
          pc.verified AS comment_verified
        FROM
          photos_comments AS pc
        WHERE
          pc.id = ?;';
      $result = $db->prepared_select_query($query, [$comment_id]);

      if ($result && count($result) > 0)
      {
        $comment = new Comment
        (
          $result[0]['comment_id'],
          $result[0]['comment_comment'],
          $result[0]['comment_date'],
          $result[0]['comment_verified'],
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
      if ($show == 'all')
      {
        $_SESSION['redirect_url'] = ROOT_URL . '?page=admin_panel&tab=comments&show=all';
      }
      if ($show == 'unverified')
      {
        $_SESSION['redirect_url'] = ROOT_URL . '?page=admin_panel&tab=comments&show=unverified';
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
