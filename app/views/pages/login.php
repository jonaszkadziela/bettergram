<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
?>
<div class="d-flex flex-column min-vh-100 bg-img-1">
  <?php
    include_once VIEWS_PATH . 'shared/navbar.php';
  ?>
  <div class="container d-flex flex-fill flex-column my-3">
    <div class="row flex-fill">
      <div class="col-lg-8 m-auto">
        <?php
          include_once VIEWS_PATH . 'shared/flash.php';
        ?>
        <div class="card p-1 shadow-lg">
          <div class="card-body text-center">
            <?php
              // Redirect user to the following URL after logging in
              $_SESSION['redirect_url'] = ROOT_URL . '?page=gallery';
              include VIEWS_PATH . 'users/log_in_user_form.php';
            ?>
            <div class="mt-1-5 pt-0-5">
              <span class="text-muted">Jesteś tu po raz pierwszy?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL . modify_get_parameters(['page' => 'register']) ?>">Stwórz nowe konto!</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    include_once VIEWS_PATH . 'shared/footer.php';
  ?>
</div>
