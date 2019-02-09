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
            <h2 class="card-title font-weight-medium text-center">Zaloguj się</h2>
            <form action="app/backend/user/sign_in.php" method="post">
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
                <button id="sign_in_form_button" class="btn btn-lg btn-primary" type="submit" disabled>Zaloguj</button>
              </div>
            </form>
            <div class="mt-3 text-center">
              <span class="card-text text-muted">Jesteś tu po raz pierwszy?</span>
              <a class="card-link" href="index.php?page=register">Stwórz nowe konto!</a>
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
<script src="<?php echo ASSETS_PATH . 'javascripts/validation_sign_in_form.js' ?>"></script>
