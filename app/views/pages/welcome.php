<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
  if (empty($_SESSION['current_user']['registered']))
  {
    $_SESSION['notice'][] = 'Nie można wyświetlić żądanej strony!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  unset($_SESSION['current_user']['registered']);

  $target_url = ROOT_URL . '?page=gallery';
  if (isset($_SESSION['target_url']))
  {
    $target_url = $_SESSION['target_url'];
    unset($_SESSION['target_url']);
  }
?>
<div class="d-flex flex-column min-vh-100 bg-img-1">
  <div class="container d-flex flex-grow-1 flex-column h-100 my-3">
    <div class="row flex-grow-1">
      <div class="col-lg-6 m-auto">
        <div class="card p-1 shadow-lg">
          <div class="card-body text-center">
            <h2 class="underline underline-primary mb-1-5">Witamy na pokładzie!<i class="far fa-grin-beam ml-0-5"></i></h2>
            <p class="mb-1-5">Pomyślnie utworzono nowe konto.</p>
            <a href="<?php echo $target_url ?>" class="btn btn-lg btn-primary">Przejdź do <?php echo $target_url == ROOT_URL . '?page=gallery' ? 'galerii' : 'żądanej strony' ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
