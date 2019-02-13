<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom py-1 sticky-top">
  <div class="container">
    <a class="navbar-brand p-0" href="<?php echo ROOT_URL ?>">
      <img src="<?php echo IMAGES_URL . 'brand/bettergram-logo.svg' ?>" alt="BetterGram" width="50" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_collapse" aria-controls="navbar_collapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar_collapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item<?php echo $_SESSION['current_page'] == 'gallery' ? ' active' : '' ?>">
          <a class="nav-link nav-page" href="<?php echo ROOT_URL ?>?page=gallery">Galeria</a>
        </li>
        <li class="nav-item<?php echo $_SESSION['current_page'] == 'top_photos' ? ' active' : '' ?>">
          <a class="nav-link nav-page" href="<?php echo ROOT_URL ?>?page=top_photos">Najlepiej oceniane</a>
        </li>
        <li class="nav-item<?php echo $_SESSION['current_page'] == 'latest_photos' ? ' active' : '' ?>">
          <a class="nav-link nav-page" href="<?php echo ROOT_URL ?>?page=latest_photos">Najnowsze</a>
        </li>
      </ul>
      <div class="d-lg-inline-flex ml-auto">
        <?php
          if (isset($_SESSION['current_user']['logged_in']))
          {
            echo
              '<div class="d-flex align-items-center my-0-5 my-lg-0">' . PHP_EOL .
                '<a class="btn btn-outline-primary py-0-5 mr-0-5" href="' . ROOT_URL . '?page=create_album">Załóż album</a>' . PHP_EOL .
                '<a class="btn btn-outline-secondary py-0-5 mr-0-5" href="' . ROOT_URL . '?page=create_photo">Dodaj zdjęcie</a>' . PHP_EOL .
              '</div>' . PHP_EOL .
              '<ul class="navbar-nav">' . PHP_EOL .
                '<li class="nav-item dropdown">' . PHP_EOL .
                  '<a id="user_dropdown" class="nav-link text-capitalize dropdown-toggle border rounded p-0-5" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . PHP_EOL .
                    '<img class="d-inline-block w-32px h-32px border rounded-circle my--0-5 mr-0-25" src="' . get_gravatar_url($_SESSION['current_user']['email'], 32) . '" alt="#">' . PHP_EOL .
                    '<span>' . $_SESSION['current_user']['login'] . '</span>' . PHP_EOL .
                  '</a>' . PHP_EOL .
                  '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user_dropdown">' . PHP_EOL .
                    '<a class="dropdown-item' . ($_SESSION['current_page'] == 'account' ? ' active' : '') . '" href="' . ROOT_URL . '?page=account">Moje konto</a>' . PHP_EOL;
            if (has_enough_permissions('moderator'))
            {
              echo '<a class="dropdown-item' . ($_SESSION['current_page'] == 'admin_panel' ? ' active' : '') . '" href="' . ROOT_URL . '?page=admin_panel">Panel administracyjny</a>' . PHP_EOL;
            }
            if (has_enough_permissions('administrator'))
            {
              echo '<a class="dropdown-item" href="' . ROOT_URL . '?action=create_thumbnails&check_existing=1' . '">Utwórz miniaturki</a>' . PHP_EOL;
            }
            echo
                    '<div class="dropdown-divider"></div>' . PHP_EOL .
                    '<a class="dropdown-item" href="' . ROOT_URL . '?action=log_out">Wyloguj się</a>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                '</li>' . PHP_EOL .
              '</ul>' . PHP_EOL;
          }
          else
          {
            echo
              '<div class="d-flex align-items-center my-0-5 my-lg-0">' . PHP_EOL .
                '<a class="btn btn-outline-primary py-0-5 mr-0-5" href="' . ROOT_URL . '?page=login">Logowanie</a>' . PHP_EOL .
                '<a class="btn btn-outline-secondary py-0-5 mr-0-5" href="' . ROOT_URL . '?page=register">Rejestracja</a>' . PHP_EOL .
              '</div>' . PHP_EOL;
          }
        ?>
      </div>
    </div>
  </div>
</nav>
