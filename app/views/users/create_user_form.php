<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
  // Redirect user to the following URL after creating a new account
  $_SESSION['redirect_url'] = ROOT_URL . '?page=welcome&redirect=1';
?>
<div id="create_user_form" class="text-center">
  <h2 class="underline underline-primary mb-1-5">Zarejestruj się</h2>
  <form class="text-left" action="<?php echo BACKEND_URL . 'user/create.php' ?>" method="post">
    <div class="form-group">
      <label for="login">Login</label>
      <input id="login" class="form-control" name="login" type="text" placeholder="Login" <?php
        if (isset($_SESSION['create_user_form']['login']))
        {
          echo 'value="' . $_SESSION['create_user_form']['login'] . '"';
          unset($_SESSION['create_user_form']['login']);
        }
      ?>>
      <small class="form-text text-muted">(od 6 do 20 znaków, tylko litery i cyfry)</small>
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input id="email" class="form-control" name="email" type="text" placeholder="Email" <?php
        if (isset($_SESSION['create_user_form']['email']))
        {
          echo 'value="' . $_SESSION['create_user_form']['email'] . '"';
          unset($_SESSION['create_user_form']['email']);
        }
      ?>>
    </div>
    <div class="form-group">
      <label for="password1">Hasło</label>
      <input id="password1" class="form-control" name="password1" type="password" placeholder="Hasło" <?php
        if (isset($_SESSION['create_user_form']['password1']))
        {
          echo 'value="' . $_SESSION['create_user_form']['password1'] . '"';
          unset($_SESSION['create_user_form']['password1']);
        }
      ?>>
      <small class="form-text text-muted">(od 6 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra)</small>
    </div>
    <div class="form-group">
      <label for="password2">Potwierdź hasło</label>
      <input id="password2" class="form-control" name="password2" type="password" placeholder="Potwierdź hasło" <?php
        if (isset($_SESSION['create_user_form']['password2']))
        {
          echo 'value="' . $_SESSION['create_user_form']['password2'] . '"';
          unset($_SESSION['create_user_form']['password2']);
        }
      ?>>
    </div>
    <div class="text-center">
      <div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">
        <button class="btn btn-lg btn-primary" tabindex="-1" type="submit">Zarejestruj</button>
      </div>
    </div>
  </form>
</div>
