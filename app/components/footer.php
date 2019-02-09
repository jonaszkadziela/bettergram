<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
?>
<footer class="footer bg-light border-top">
  <div class="container p-1-5">
    <p class="text-center mb-0">
      Zaprojektowane i zaprogramowane z <i class="fas fa-heart text-danger"></i> przez <a class="text-danger underline underline--narrow underline-danger underline-animation" href="https://www.jonaszkadziela.pl/" target="_blank">Jonasza Kądzielę</a>
    </p>
    <?php
      if (ENVIRONMENT != 'production')
      {
        echo '<p class="text-muted text-center mb-0">Jonasz Kądziela Klasa 4TA</p>' . PHP_EOL;
      }
    ?>
  </div>
  <div class="d-flex bg-dark p-1">
    <a class="m-auto" href="<?php echo ROOT_URL ?>">
      <img src="<?php echo ASSETS_URL . 'images/brand/bettergram-logotyp.svg' ?>" alt="BetterGram" height="40">
    </a>
  </div>
</footer>
