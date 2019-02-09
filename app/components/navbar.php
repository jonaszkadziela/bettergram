<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom py-1 sticky-top">
  <div class="container">
    <a class="navbar-brand p-0" href="<?php echo ROOT_URL ?>">
      <img src="<?php echo ASSETS_URL . 'images/brand/bettergram-logo.svg' ?>" alt="BetterGram" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_collapse" aria-controls="navbar_collapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar_collapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item<?php echo $_SESSION['current_view'] == 'gallery' ? ' active' : '' ?>">
          <a class="nav-link nav-page underline underline-primary underline-animation pb-0 mb-0-5" href="<?php echo ROOT_URL ?>?view=gallery">Galeria</a>
        </li>
        <li class="nav-item<?php echo $_SESSION['current_view'] == '#' ? ' active' : '' ?>">
          <a class="nav-link nav-page underline underline-primary underline-animation pb-0 mb-0-5" href="#">Najlepiej oceniane</a>
        </li>
        <li class="nav-item<?php echo $_SESSION['current_view'] == '#' ? ' active' : '' ?>">
          <a class="nav-link nav-page underline underline-primary underline-animation pb-0 mb-0-5" href="#">Najnowsze</a>
        </li>
      </ul>
      <div class="d-lg-inline-flex ml-auto">
        <?php
          if (isset($_SESSION['user_signed_in']))
          {
            echo
              '<div class="d-flex align-items-center my-0-5 my-lg-0">' . PHP_EOL .
                '<a class="btn btn-outline-primary py-0-5 mr-0-5" href="' . ROOT_URL . '?view=create_album">Załóż album</a>' . PHP_EOL .
                '<a class="btn btn-outline-secondary py-0-5 mr-0-5" href="' . ROOT_URL . '?view=create_photo">Dodaj zdjęcie</a>' . PHP_EOL .
              '</div>' . PHP_EOL .
              '<ul class="navbar-nav">' . PHP_EOL .
                '<li class="nav-item dropdown">' . PHP_EOL .
                  '<a id="user_dropdown" class="nav-link text-capitalize dropdown-toggle border rounded p-0-5" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . PHP_EOL .
                    '<i class="fas fa-user-circle mr-0-25"></i>' . PHP_EOL .
                    '<span>' . (isset($_SESSION['user_login']) ? $_SESSION['user_login'] : 'użytkownik') . '</span>' . PHP_EOL .
                  '</a>' . PHP_EOL .
                  '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user_dropdown">' . PHP_EOL .
                    '<a class="dropdown-item" href="#">Moje konto</a>' . PHP_EOL;
            if ($_SESSION['user_permissions'] == 'moderator' || $_SESSION['user_permissions'] == 'administrator')
            {
              echo
                    '<a class="dropdown-item" href="#">Panel administracyjny</a>' . PHP_EOL .
                    '<a class="dropdown-item" href="' . BACKEND_URL . 'photo/create_thumbnails.php?check_existing=1' . '">Utwórz miniaturki</a>' . PHP_EOL;
            }
            echo
                    '<div class="dropdown-divider"></div>' . PHP_EOL .
                    '<a class="dropdown-item" href="' . ROOT_URL . '?action=sign_out">Wyloguj się</a>' . PHP_EOL .
                  '</div>' . PHP_EOL .
                '</li>' . PHP_EOL .
              '</ul>' . PHP_EOL;
          }
          else
          {
            echo
              '<div class="d-flex align-items-center my-0-5 my-lg-0">' . PHP_EOL .
                '<a class="btn btn-outline-primary py-0-5 mr-0-5" href="' . ROOT_URL . '?view=login">Logowanie</a>' . PHP_EOL .
                '<a class="btn btn-outline-secondary py-0-5 mr-0-5" href="' . ROOT_URL . '?view=register">Rejestracja</a>' . PHP_EOL .
              '</div>' . PHP_EOL;
          }
        ?>
      </div>
    </div>
  </div>
</nav>
