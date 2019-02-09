<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
?>
<footer class="footer bg-light border-top">
  <div class="container p-1-5">
    <p class="text-center mb-0">
      Zaprojektowane i zaprogramowane z <i class="fas fa-heart text-danger"></i> przez <a class="text-danger underline underline--narrow underline-danger underline-animation" href="https://www.jonaszkadziela.pl/" target="_blank" rel="noreferrer">Jonasza Kądzielę</a>
    </p>
    <?php
      if (ENVIRONMENT !== 'production')
      {
        echo '<p class="text-muted text-center mb-0">Jonasz Kądziela Klasa 4TA</p>' . PHP_EOL;
      }
    ?>
  </div>
  <div class="d-flex bg-dark p-1">
    <a class="m-auto" href="<?php echo ROOT_URL ?>">
      <img src="<?php echo IMAGES_URL . 'brand/bettergram-logotyp.svg' ?>" alt="BetterGram" width="200" height="40">
    </a>
  </div>
</footer>
