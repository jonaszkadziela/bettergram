<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  unset($_SESSION['sign_up_form_login']);
  unset($_SESSION['sign_up_form_email']);
  unset($_SESSION['sign_up_form_password1']);
  unset($_SESSION['sign_up_form_password2']);
  unset($_SESSION['user_signed_up']);

  $target_url = get_url(false) . '?view=gallery';
  if (isset($_SESSION['target_url']))
  {
    $target_url = $_SESSION['target_url'];
    unset($_SESSION['target_url']);
  }
?>
<div class="d-flex flex-column h-100vh bg-img-1">
  <div class="container d-flex flex-grow-1 flex-column h-100 my-3">
    <div class="row flex-grow-1">
      <div class="col-lg-6 m-auto">
        <div class="card p-1 shadow-lg">
          <div class="card-body text-center">
            <h2 class="d-flex card-title underline underline-primary mb-2">Witamy na pokładzie!<i class="far fa-grin-beam ml-0-5"></i></h2>
            <p class="card-text my-1-5">Pomyślnie utworzono nowe konto.</p>
            <a href="<?php echo $target_url ?>" class="btn btn-lg btn-primary">Przejdź do <?php echo $target_url == get_url(false) . '?view=gallery' ? 'galerii' : 'żądanej strony' ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
