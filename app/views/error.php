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
  <div class="container d-flex flex-grow-1 flex-column h-100 my-3">
    <div class="row flex-grow-1">
      <div class="col-lg-6 m-auto">
        <div class="card p-1 shadow-lg">
          <div class="card-body text-center">
            <h2 class="d-flex card-title underline underline-primary mb-2">
            <?php
              if (isset($_SESSION['error_no']))
              {
                echo 'Wystąpił błąd #' . $_SESSION['error_no'] . '!';
                unset($_SESSION['error_no']);
              }
              else
              {
                echo 'Wystąpił błąd!';
              }
            ?>
            </h2>
            <p class="card-text mt-1-5">Przepraszamy za niedogodności. Spróbuj ponownie później.</p>
            <p class="card-text">Jeśli ten błąd powtarza się wielokrotnie, skontaktuj się z administratorem.</p>
            <p class="card-text mb-1-5"><i class="far fa-envelope"></i> Email: <a class="underline underline--narrow underline-primary underline-animation" href="mailto:<?php echo ADMIN_MAIL ?>"><?php echo ADMIN_MAIL ?></a></p>
            <a href="<?php echo ROOT_URL ?>" class="btn btn-lg btn-primary">Wróć na stronę główną</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
