<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
  if (!has_enough_permissions('moderator'))
  {
    $_SESSION['notice'][] = 'Twoje konto nie posiada wystarczających uprawnień, aby wyświetlić tę stronę!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  $db = Database::get_instance();

  $tab_default = has_enough_permissions('administrator') ? 'albums' : 'photos';
  $tab = isset($_GET['tab']) ? $_GET['tab'] : $tab_default;
  $report_error = false;
  $notice_content;
  switch ($tab)
  {
    case 'albums':
    case 'users':
      if (!has_enough_permissions('administrator'))
      {
        $report_error = true;
        $notice_content = 'Twoje konto nie posiada wystarczających uprawnień, aby wyświetlić tę podstronę!';
      }
    break;

    case 'photos':
    case 'comments':
    break;

    default:
      $report_error = true;
      $notice_content = 'Nie można wyświetlić żądanej podstrony!';
  }
  if ($report_error)
  {
    $_SESSION['notice'][] = $notice_content;
    header('Location: ' . ROOT_URL . '?page=admin_panel');
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
            <?php
              /**
               * Render page title and top navigation depending on user's permissions
               */
              echo
                '<h2 class="underline underline-primary mb-1-5">Panel administracyjny</h2>' . PHP_EOL .
                '<nav class="nav nav-pills flex-column flex-sm-row">' . PHP_EOL;
              if (has_enough_permissions('administrator'))
              {
                echo
                  '<a class="flex-sm-fill nav-link border rounded-sm-0 rounded-top rounded-left-sm' . ($tab == 'albums' ? ' active' : '') . '" href="' . ROOT_URL . '?page=admin_panel&tab=albums">Albumy</a>' . PHP_EOL .
                  '<a class="flex-sm-fill nav-link border-sm-0 border-x border-y-sm' . ($tab == 'photos' ? ' active' : '') . '" href="' . ROOT_URL . '?page=admin_panel&tab=photos">Zdjęcia</a>' . PHP_EOL .
                  '<a class="flex-sm-fill nav-link border-sm-0 border-left-sm border-x border-top border-y-sm' . ($tab == 'comments' ? ' active' : '') . '" href="' . ROOT_URL . '?page=admin_panel&tab=comments">Komentarze</a>' . PHP_EOL .
                  '<a class="flex-sm-fill nav-link border rounded-sm-0 rounded-bottom rounded-right-sm' . ($tab == 'users' ? ' active' : '') . '" href="' . ROOT_URL . '?page=admin_panel&tab=users">Użytkownicy</a>' . PHP_EOL;
              }
              else if (has_enough_permissions('moderator'))
              {
                echo
                  '<a class="flex-sm-fill nav-link border rounded-sm-0 rounded-top rounded-left-sm' . ($tab == 'photos' ? ' active' : '') . '" href="' . ROOT_URL . '?page=admin_panel&tab=photos">Zdjęcia</a>' . PHP_EOL .
                  '<a class="flex-sm-fill nav-link border-x border-bottom border-left-sm-0 border-top-sm rounded-sm-0 rounded-bottom rounded-right-sm' . ($tab == 'comments' ? ' active' : '') . '" href="' . ROOT_URL . '?page=admin_panel&tab=comments">Komentarze</a>' . PHP_EOL;
              }
              echo '</nav>' . PHP_EOL;

              require_once VIEWS_PATH . 'pages/admin_panel_tabs/albums_tab.php';
              require_once VIEWS_PATH . 'pages/admin_panel_tabs/photos_tab.php';
              require_once VIEWS_PATH . 'pages/admin_panel_tabs/comments_tab.php';
              require_once VIEWS_PATH . 'pages/admin_panel_tabs/users_tab.php';
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
