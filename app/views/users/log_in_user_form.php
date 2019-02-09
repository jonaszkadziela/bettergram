<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
?>
<div id="log_in_user_form" class="text-center">
  <h2 class="underline underline-primary mb-1-5">Zaloguj się</h2>
  <form class="text-left" action="<?php echo BACKEND_URL . 'user/log_in.php' ?>" method="post">
    <div class="form-group">
      <label for="login">Login</label>
      <input id="login" class="form-control" name="login" type="text" placeholder="Login" <?php
        if (isset($_SESSION['log_in_user_form']['login']))
        {
          echo 'value="' . $_SESSION['log_in_user_form']['login'] . '"';
          unset($_SESSION['log_in_user_form']['login']);
        }
      ?>>
    </div>
    <div class="form-group">
      <label for="password">Hasło</label>
      <input id="password" class="form-control" name="password" type="password" placeholder="Hasło">
    </div>
    <div class="text-center">
      <div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">
        <button id="submit" class="btn btn-lg btn-primary" tabindex="-1" type="submit">Zaloguj</button>
      </div>
    </div>
  </form>
</div>
