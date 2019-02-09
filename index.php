<?php
  require_once('config.php');
  require_once('routes.php');
?>
<!doctype html>
<html lang="pl-PL">
  <head>
    <meta charset="utf-8">
    <meta property="og:locale" content="pl_PL">
    <meta property="og:site_name" content="BetterGram">
    <meta property="og:title" content="<?php echo get_page_title()?>">
    <meta property="og:image" content="<?php echo ASSETS_URL ?>images/brand/bettergram-og-image.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Jonasz Kądziela">
    <meta name="msapplication-TileImage" content="<?php echo ASSETS_URL ?>images/favicons/ms-icon-144x144.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <?php
      $apple_icons_sizes = [57, 60, 72, 76, 114, 120, 144, 152, 180];
      $favicon_sizes = [16, 32, 96];

      foreach ($apple_icons_sizes as $size)
      {
        echo
          '<link rel="apple-touch-icon" ' .
          'sizes="' . $size . 'x' . $size . '" ' .
          'href="' . ASSETS_URL . 'images/favicons/apple-icon-' . $size . 'x' . $size . '.png">' . PHP_EOL;
      }
      foreach ($favicon_sizes as $size)
      {
        echo
          '<link rel="icon" type="image/png" ' .
          'sizes="' . $size . 'x' . $size . '" ' .
          'href="' . ASSETS_URL . 'images/favicons/favicon-' . $size . 'x' . $size . '.png">' . PHP_EOL;
      }
    ?>
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo ASSETS_URL ?>images/favicons/android-icon-192x192.png">
    <link rel="manifest" href="<?php echo ASSETS_URL ?>images/favicons/manifest.json">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL ?>stylesheets/css/main.min.css">
    <title><?php echo get_page_title()?></title>
  </head>
  <body>
    <script>
      const ENVIRONMENT = "<?php echo ENVIRONMENT ?>";
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_URL ?>javascripts/main.js"></script>
    <?php
      $file = VIEWS_PATH . $view . '.php';
      if (file_exists($file))
      {
        include_once($file);
      }
      else
      {
        $_SESSION['error_no'] = '404';
        include_once(VIEWS_PATH . 'error.php');
      }
    ?>
  </body>
</html>
