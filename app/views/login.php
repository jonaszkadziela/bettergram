<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
?>
<div class="d-flex flex-column h-100vh bg-img-1">
  <?php
    include_once(COMPONENTS_PATH . 'navbar.php');
  ?>
  <div class="container d-flex flex-grow-1 flex-column h-100 my-3">
    <div class="row flex-grow-1">
      <div class="col-lg-8 m-auto">
        <?php
          include_once(COMPONENTS_PATH . 'alert.php');
        ?>
        <div class="card p-1 shadow-lg">
          <div class="card-body">
            <h2 class="d-flex card-title underline underline-primary mb-1-75">Zaloguj się</h2>
            <form action="<?php echo BACKEND_URL . 'user/sign_in.php' ?>" method="post">
              <div class="form-group">
                <label for="login">Login</label>
                <input id="login" class="form-control" name="login" type="text" placeholder="Login"<?php
                  if (isset($_SESSION['sign_in_form_login']))
                  {
                    echo 'value="' . $_SESSION['sign_in_form_login'] . '"';
                    unset($_SESSION['sign_in_form_login']);
                  }
                ?>>
              </div>
              <div class="form-group">
                <label for="password">Hasło</label>
                <input id="password" class="form-control" name="password" type="password" placeholder="Hasło">
              </div>
              <div class="text-center">
                <div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">
                  <button id="sign_in_form_button" class="btn btn-lg btn-primary" tabindex="-1" type="submit" disabled>Zaloguj</button>
                </div>
              </div>
            </form>
            <div class="mt-1-5 pt-0-5 text-center">
              <span class="card-text text-muted">Jesteś tu po raz pierwszy?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL ?>?view=register">Stwórz nowe konto!</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    include_once(COMPONENTS_PATH . 'footer.php');
  ?>
</div>
<script>loadScript("<?php echo ASSETS_URL . 'javascripts/validation.js' ?>");</script>
<script>loadScript("<?php echo ASSETS_URL . 'javascripts/validation_sign_in_form.js' ?>");</script>
