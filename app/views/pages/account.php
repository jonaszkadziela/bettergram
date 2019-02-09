<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $db = Database::get_instance();

  $tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
  switch ($tab)
  {
    case 'profile':
    case 'albums':
    case 'photos':
    break;

    default:
      $_SESSION['notice'][] = 'Nie można wyświetlić żądanej podstrony!';
      header('Location: ' . ROOT_URL . '?page=account');
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
               * Render page title and top navigation
               */
              echo
                '<h2 class="underline underline-primary mb-1-5">Moje konto</h2>' . PHP_EOL .
                '<nav class="nav nav-pills flex-column flex-sm-row">' . PHP_EOL .
                  '<a class="flex-sm-fill nav-link border rounded-sm-0 rounded-top rounded-left-sm' . ($tab == 'profile' ? ' active' : '') . '" href="' . ROOT_URL . '?page=account&tab=profile">Profil</a>' . PHP_EOL .
                  '<a class="flex-sm-fill nav-link border-sm-0 border-x border-y-sm' . ($tab == 'albums' ? ' active' : '') . '" href="' . ROOT_URL . '?page=account&tab=albums">Albumy</a>' . PHP_EOL .
                  '<a class="flex-sm-fill nav-link border rounded-sm-0 rounded-bottom rounded-right-sm' . ($tab == 'photos' ? ' active' : '') . '" href="' . ROOT_URL . '?page=account&tab=photos">Zdjęcia</a>' . PHP_EOL .
                '</nav>' . PHP_EOL;

              require_once VIEWS_PATH . 'pages/account_tabs/profile_tab.php';
              require_once VIEWS_PATH . 'pages/account_tabs/albums_tab.php';
              require_once VIEWS_PATH . 'pages/account_tabs/photos_tab.php';
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
