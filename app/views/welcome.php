<?php
  require_once('protect_views.php');

  unset($_SESSION['sign_up_form_login']);
  unset($_SESSION['sign_up_form_email']);
  unset($_SESSION['sign_up_form_password1']);
  unset($_SESSION['sign_up_form_password2']);

  unset($_SESSION['user_signed_up']);
?>
<div class="d-flex flex-column h-100vh bg-1">
  <div class="container d-flex flex-grow-1 flex-column h-100 my-5">
    <div class="row flex-grow-1">
      <div class="col-md-6 m-auto">
        <div class="card p-3 shadow-lg">
          <div class="card-body text-center">
            <h2 class="card-title font-weight-medium">Witamy na pokładzie!</h2>
            <p class="card-text my-4">Pomyślnie utworzono nowe konto.</p>
            <a href="index.php?page=gallery" class="btn btn-primary">Przejdź do galerii</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
