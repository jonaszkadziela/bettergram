<?php
  require_once('protect_views.php');
?>
<div class="d-flex flex-column h-100vh bg-1">
  <div class="container d-flex flex-grow-1 flex-column h-100 my-5">
    <div class="row flex-grow-1">
      <div class="col-md-6 m-auto">
        <div class="card p-3 shadow-lg">
          <div class="card-body text-center">
            <h2 class="card-title font-weight-medium">
            <?php
              if (isset($_SESSION['error_no']))
              {
                echo 'Wystąpił błąd #' . $_SESSION['error_no'] . '!';
              }
              else
              {
                echo 'Wystąpił błąd!';
              }
            ?>
            </h2>
            <p class="card-text mt-4">Przepraszamy za niedogodności. Spróbuj ponownie później.</p>
            <p class="card-text">Jeśli ten błąd powtarza się wielokrotnie, skontaktuj się z administratorem.</p>
            <p class="card-text mb-4">Email: <a href="mailto:<?php echo ADMIN_MAIL; ?>"><?php echo ADMIN_MAIL; ?></a></p>
            <a href="index.php" class="btn btn-primary">Wróć na stronę główną</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
