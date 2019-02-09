<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
?>
<div id="create_album_form" class="text-center">
  <h2 class="underline underline-primary mb-1-5">Stwórz nowy album</h2>
  <form class="text-left" action="<?php echo BACKEND_URL . 'album/create.php' ?>" method="post">
    <div class="form-group">
      <label for="title">Tytuł albumu</label>
      <input id="title" class="form-control" name="title" type="text" placeholder="Tytuł albumu" <?php
        if (isset($_SESSION['create_album_form']['title']))
        {
          echo 'value="' . $_SESSION['create_album_form']['title'] . '"';
          unset($_SESSION['create_album_form']['title']);
        }
      ?>>
      <small class="form-text text-muted">(od 3 do 100 znaków, nie może zaczynać ani kończyć się spacją)</small>
    </div>
    <div class="text-center">
      <div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">
        <button id="submit" class="btn btn-primary" tabindex="-1" type="submit">Załóż album</button>
      </div>
    </div>
  </form>
</div>
