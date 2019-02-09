<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $profile_tab_errors = [];

  if (empty($tab))
  {
    $profile_tab_errors[] = 'Nie zdefiniowano podstrony!';
  }

  if (count($profile_tab_errors) == 0 && $tab == 'profile')
  {
    echo
      '<div class="card my-1-5 p-0-5 bg-light border">' . PHP_EOL .
        '<div class="card-body">' . PHP_EOL;
    $user = new User
    (
      $_SESSION['current_user']['id'],
      $_SESSION['current_user']['login'],
      $_SESSION['current_user']['email'],
      null,
      $_SESSION['current_user']['permissions'],
      $_SESSION['current_user']['active']
    );
    include VIEWS_PATH . 'users/update_user_form.php';
    echo
        '</div>' . PHP_EOL .
      '</div>' . PHP_EOL .
      '<div class="mt-1-5 pt-0-5">' . PHP_EOL .
        '<span class="text-muted">Jesteś tu przypadkowo?</span>' . PHP_EOL .
        '<a class="underline underline--narrow underline-primary underline-animation" href="' . ROOT_URL . '">Wróć na stronę główną!</a>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }

  if (count($profile_tab_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić podstrony profilu, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($profile_tab_errors); $i++)
    {
      echo '<li>' . $profile_tab_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
