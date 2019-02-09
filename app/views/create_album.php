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
  <?php
    include_once(COMPONENTS_PATH . 'navbar.php');
  ?>
  <div class="container d-flex flex-grow-1 flex-column h-100 my-3">
    <div class="row flex-grow-1">
      <div class="col-lg-8 m-auto">
        <?php
          include_once(COMPONENTS_PATH . 'alert.php');
        ?>
        <div class="card p-1 shadow-lg">
          <div class="card-body">
            <h2 class="d-flex card-title underline underline-primary mb-1-75">Stwórz nowy album</h2>
            <form action="<?php echo BACKEND_URL . 'album/create.php' ?>" method="post">
              <div class="form-group">
                <label for="title">Tytuł albumu</label>
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id'] ?>">
                <input id="title" class="form-control" name="title" type="text" placeholder="Tytuł albumu"<?php
                  if (isset($_SESSION['create_album_form_title']))
                  {
                    echo 'value="' . $_SESSION['create_album_form_title'] . '"';
                    unset($_SESSION['create_album_form_title']);
                  }
                ?>>
              </div>
              <div class="text-center">
                <div class="d-inline-block btn-tooltip btn-tooltip-primary" tabindex="0" data-toggle="tooltip" title="Formularz jest niepoprawnie wypełniony!">
                  <button id="create_album_form_button" class="btn btn-lg btn-primary" tabindex="-1" type="submit" disabled>Załóż album</button>
                </div>
              </div>
            </form>
            <div class="mt-1-5 pt-0-5 text-center">
              <span class="card-text text-muted">Jesteś tu przypadkowo?</span>
              <a class="underline underline--narrow underline-primary underline-animation" href="<?php echo ROOT_URL ?>">Wróć na stronę główną!</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    include_once(COMPONENTS_PATH . 'footer.php');
  ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xregexp/3.2.0/xregexp-all.min.js"></script>
<script>loadScript("<?php echo ASSETS_URL . 'javascripts/validation.js' ?>");</script>
<script>loadScript("<?php echo ASSETS_URL . 'javascripts/validation_create_album_form.js' ?>");</script>
