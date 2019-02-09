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
            <h2 class="d-flex card-title underline underline-primary mb-1-75">Zarejestruj się</h2>
            <form action="<?php echo BACKEND_URL . 'user/create.php' ?>" method="post">
              <div class="form-group">
                <label for="login">Login</label>
                <input id="login" class="form-control" name="login" type="text" placeholder="Login"<?php
                  if (isset($_SESSION['sign_up_form_login']))
                  {
                    echo 'value="' . $_SESSION['sign_up_form_login'] . '"';
                    unset($_SESSION['sign_up_form_login']);
                  }
                ?>>
                <small class="form-text text-muted">(od 6 do 20 znaków, tylko litery i cyfry)</small>
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input id="email" class="form-control" name="email" type="text" placeholder="Email"<?php
                  if (isset($_SESSION['sign_up_form_email']))
                  {
                    echo 'value="' . $_SESSION['sign_up_form_email'] . '"';
                    unset($_SESSION['sign_up_form_email']);
                  }
                ?>>
              </div>
              <div class="form-group">
                <label for="password1">Hasło</label>
                <input id="password1" class="form-control" name="password1" type="password" placeholder="Hasło"<?php
                  if (isset($_SESSION['sign_up_form_password1']))
                  {
                    echo 'value="' . $_SESSION['sign_up_form_password1'] . '"';
                    unset($_SESSION['sign_up_form_password1']);
                  }
                ?>>
                <small class="form-text text-muted">(od 6 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra)</small>
              </div>
              <div class="form-group">
                <label for="password2">Potwierdź hasło</label>
                <input id="password2" class="form-control" name="password2" type="password" placeholder="Potwierdź hasło"<?php
                  if (isset($_SESSION['sign_up_form_password2']))
                  {
                    echo 'value="' . $_SESSION['sign_up_form_password2'] . '"';
                    unset($_SESSION['sign_up_form_password2']);
                  }
                ?>>
              </div>
              <div class="text-center">
                <div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">
                  <button id="sign_up_form_button" class="btn btn-lg btn-primary" tabindex="-1" type="submit" disabled>Zarejestruj</button>
                </div>
              </div>
            </form>
            <div class="mt-1-5 pt-0-5 text-center">
              <span class="card-text text-muted">Posiadasz już konto?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL ?>?view=login">Zaloguj się!</a>
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
<script>loadScript("<?php echo ASSETS_URL . 'javascripts/validation_create_user_form.js' ?>");</script>
