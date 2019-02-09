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
      <div class="col-md-8 m-auto">
        <?php
          $_SESSION['render_component'] = true;
          include_once('app/components/alert.php');
        ?>
        <div class="card p-3 shadow-lg">
          <div class="card-body">
            <h2 class="card-title font-weight-medium text-center">Zarejestruj się</h2>
            <form action="app/backend/user/sign_up.php" method="post">
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
                <button id="sign_up_form_button" class="btn btn-lg btn-primary" type="submit" disabled>Zarejestruj</button>
              </div>
            </form>
            <div class="mt-3 text-center">
              <span class="card-text text-muted">Posiadasz już konto?</span>
              <a class="card-link" href="index.php?page=login">Zaloguj się!</a>
            </div>
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
<script src="<?php echo ASSETS_PATH . 'javascripts/validation.js' ?>"></script>
<script src="<?php echo ASSETS_PATH . 'javascripts/validation_sign_up_form.js' ?>"></script>
