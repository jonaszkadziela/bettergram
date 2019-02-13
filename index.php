<?php
  require_once 'config.php';
  require_once 'routes.php';
  ob_start();
?>
<!doctype html>
<html lang="pl-PL">
  <head>
    <meta charset="<?php echo CHARACTER_ENCODING ?>">
    <meta property="og:locale" content="pl_PL">
    <meta property="og:site_name" content="BetterGram">
    <meta property="og:title" content="<?php echo get_page_title() ?>">
    <meta property="og:image" content="<?php echo IMAGES_URL ?>brand/bettergram-og-image.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Jonasz Kądziela">
    <meta name="description" content="BetterGram is a simple yet powerful web application to share your photos with others.">
    <meta name="msapplication-TileImage" content="<?php echo IMAGES_URL ?>favicons/ms-icon-144x144.png">
    <meta name="msapplication-TileColor" content="#e06f24">
    <meta name="theme-color" content="#e06f24">
    <?php
      $apple_icons_sizes = [57, 60, 72, 76, 114, 120, 144, 152, 180];
      $favicon_sizes = [16, 32, 96];

      foreach ($apple_icons_sizes as $size)
      {
        echo
          '<link rel="apple-touch-icon" ' .
          'sizes="' . $size . 'x' . $size . '" ' .
          'href="' . IMAGES_URL . 'favicons/apple-icon-' . $size . 'x' . $size . '.png">' . PHP_EOL;
      }
      foreach ($favicon_sizes as $size)
      {
        echo
          '<link rel="icon" type="image/png" ' .
          'sizes="' . $size . 'x' . $size . '" ' .
          'href="' . IMAGES_URL . 'favicons/favicon-' . $size . 'x' . $size . '.png">' . PHP_EOL;
      }
    ?>
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo IMAGES_URL ?>favicons/android-icon-192x192.png">
    <link rel="manifest" href="<?php echo ROOT_URL ?>manifest.json">
    <link rel="stylesheet" href="<?php echo get_asset_path('main.css') ?>">
    <title><?php echo get_page_title() ?></title>
  </head>
  <body>
    <?php
      if (RECAPTCHA_ENABLED || ANALYTICS_ENABLED)
      {
        require 'env.php';
      }
      echo
        '<script>' . PHP_EOL .
          'var ENVIRONMENT = "' . ENVIRONMENT . '";' . PHP_EOL .
          'var ANALYTICS_ENABLED = ' . (ANALYTICS_ENABLED ? 'true' : 'false') . ';' . PHP_EOL .
          (ANALYTICS_ENABLED ? 'var ANALYTICS_TRACKING_ID = "' . $env['analytics']['tracking_id'] . '";' . PHP_EOL : '') .
          'var RECAPTCHA_ENABLED = ' . (RECAPTCHA_ENABLED ? 'true' : 'false') . ';' . PHP_EOL .
          (RECAPTCHA_ENABLED ? 'var RECAPTCHA_SITE_KEY = "' . $env['recaptcha']['site_key'] . '";' . PHP_EOL : '') .
          (RECAPTCHA_ENABLED ? 'var CURRENT_PAGE = "' . str_replace('_', '', $_SESSION['current_page']) . '";' . PHP_EOL : '') .
        '</script>' . PHP_EOL .
        '<script defer src="' . get_asset_path('main.js') . '"></script>' . PHP_EOL;

      if (empty($_GET['error']))
      {
        $file = VIEWS_PATH . 'pages/' . $_SESSION['current_page'] . '.php';
        if (file_exists($file))
        {
          // Prepare redirect_url and referrer_url to be set again
          unset($_SESSION['redirect_url']);
          unset($_SESSION['referrer_url']);

          $_SESSION['referrer_url'] = $_SERVER['REQUEST_URI'];

          include_once $file;
        }
        else
        {
          header('Location: ' . ROOT_URL . '?error=404');
          exit();
        }
      }
      if (isset($_GET['error']))
      {
        include_once VIEWS_PATH . 'pages/error.php';
      }
    ?>
  </body>
</html>
<?php
  ob_end_flush();
?>
