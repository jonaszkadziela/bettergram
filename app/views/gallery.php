<?php
  require_once('protect_views.php');
?>
<div class="d-flex flex-column h-100vh bg-1">
  <?php
    $_SESSION['render_component'] = true;
    include_once('app/components/navbar.php');
  ?>
  <div class="container d-flex flex-grow-1 flex-column h-100 my-5">
    <div class="row flex-grow-1">
      <div class="col-md-6 m-auto">
        <?php
          $_SESSION['render_component'] = true;
          include_once('app/components/alert.php');
        ?>
        <div class="card p-3 shadow-lg">
          <div class="card-body text-center">
            <h2 class="card-title font-weight-medium mb-4">Galeria</h2>
            <?php
              if (isset($_SESSION['user_signed_in']))
              {
                echo
                  '<ul class="list-group">' .
                    '<li class="list-group-item bg-light">' .
                      '<h4 class="mb-0">Twoje aktualne dane</h4>' .
                    '</li>' .
                    '<li class="list-group-item">' .
                      '<h5>Login</h5>' .
                      '<p class="mb-0">';
                  echo isset($_SESSION['user_login']) ? $_SESSION['user_login'] : 'brak danych';
                  echo
                      '</p>' .
                    '</li>' .
                    '<li class="list-group-item">' .
                      '<h5>Adres email</h5>' .
                      '<p class="mb-0">';
                  echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'brak danych';
                  echo
                      '</p>' .
                    '</li>' .
                    '<li class="list-group-item">' .
                      '<h5>Data założenia konta</h5>' .
                      '<p class="mb-0">';
                  echo isset($_SESSION['user_sign_up_date']) ? $_SESSION['user_sign_up_date'] : 'brak danych';
                  echo
                      '</p>' .
                    '</li>' .
                    '<li class="list-group-item">' .
                      '<h5>Uprawnienia</h5>' .
                      '<p class="mb-0">';
                  echo isset($_SESSION['user_permissions']) ? $_SESSION['user_permissions'] : 'brak danych';
                  echo
                      '</p>' .
                    '</li>' .
                    '<li class="list-group-item">' .
                      '<h5>Czy konto jest aktywne?</h5>' .
                      '<p class="mb-0">';
                  echo isset($_SESSION['user_active']) ? ($_SESSION['user_active'] == 1 ? 'tak' : 'nie') : 'brak danych';
                  echo
                      '</p>' .
                    '</li>' .
                  '</ul>';
              }
              else
              {
                echo
                  '<h5 class="font-weight-medium mb-3">Nie ma jeszcze żadnych albumów do wyświetlenia.</h5>' .
                  '<p class="mb-3">Bądź pierwszym użytkownikiem, który zamieści zdjęcie i stworzy album.</p>' .
                  '<p class="mb-0">' .
                    '<span class="card-text text-muted mr-1">Jesteś tu po raz pierwszy?</span>' .
                    '<a class="card-link" href="index.php?page=register">Stwórz nowe konto!</a>' .
                  '</p>' .
                  '<p class="mb-0">' .
                    '<span class="card-text text-muted mr-1">Posiadasz już konto?</span>' .
                    '<a class="card-link" href="index.php?page=login">Zaloguj się!</a>' .
                  '</p>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    $_SESSION['render_component'] = true;
    include_once('app/components/footer.php');
  ?>
</div>
