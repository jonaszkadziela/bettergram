<?php
  require_once('protect_components.php');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light py-3 sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <span class="brand-name font-weight-bold">BetterGram</span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_collapse" aria-controls="navbar_collapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar_collapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=gallery">Galeria</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Najlepiej oceniane</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Najnowsze</a>
        </li>
      </ul>
      <div class="d-lg-inline-flex ml-auto">
        <?php
          if (isset($_SESSION['user_signed_in']))
          {
            echo
              '<div class="d-flex align-items-center my-2 my-lg-0">' .
                '<a class="btn btn-outline-primary mr-2" href="#">Załóż album</a>' .
                '<a class="btn btn-outline-info mr-2" href="#">Dodaj zdjęcie</a>' .
              '</div>' .
              '<ul class="navbar-nav">' .
                '<li class="nav-item dropdown">' .
                  '<a id="user_dropdown" class="nav-link text-capitalize dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo isset($_SESSION['user_login']) ? $_SESSION['user_login'] : 'użytkownik';
            echo
                  '</a>' .
                  '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user_dropdown">' .
                    '<a class="dropdown-item" href="#">Moje konto</a>';
            if ($_SESSION['user_permissions'] == 'moderator' || $_SESSION['user_permissions'] == 'administrator')
            {
              echo '<a class="dropdown-item" href="#">Panel administracyjny</a>';
            }
            echo
                    '<div class="dropdown-divider"></div>' .
                    '<a class="dropdown-item" href="index.php?action=sign_out">Wyloguj się</a>' .
                  '</div>' .
                '</li>' .
              '</ul>';
          }
          else
          {
            echo
              '<div class="d-flex align-items-center my-2 my-lg-0">' .
                '<a class="btn btn-outline-primary mr-2" href="index.php?page=login">Logowanie</a>' .
                '<a class="btn btn-outline-info mr-2" href="index.php?page=register">Rejestracja</a>' .
              '</div>';
          }
        ?>
      </div>
    </div>
  </div>
</nav>
