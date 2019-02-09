<?php
  require_once str_replace('\\', '/', __DIR__) . '/../config.php';

  echo 'Wersjonowanie plików' . PHP_EOL;

  $stylesheets_path = STYLESHEETS_PATH . 'dist/';
  $javascripts_path = JAVASCRIPTS_PATH . 'dist/';

  $errors = [];

  $stylesheets = glob($stylesheets_path . '*');
  $javascripts = glob($javascripts_path . '*');
  $files_paths = array_merge($stylesheets, $javascripts);

  foreach ($files_paths as $file_path)
  {
    $file = basename($file_path);
    $file_array = pathinfo($file_path);
    $new_name = str_replace('.min', '', $file_array['filename']) . ASSETS_VERSION_SUFFIX . '.' . $file_array['extension'];

    switch ($file_array['extension'])
    {
      case 'css':
        $new_path = $stylesheets_path . $new_name;
      break;

      case 'js':
        $new_path = $javascripts_path . $new_name;
      break;

      default:
        $errors[] = 'Niepoprawne rozszerzenie pliku: ' . $file;
        continue 2;
      break;
    }

    if (preg_match("/^[\S]+_\d{4}-\d{2}-\d{2}\.min\." . $file_array['extension'] . "$/m", $file))
    {
      continue;
    }

    if (!rename($file_path, $new_path))
    {
      $errors[] = 'Nie udało się zmienić nazwy pliku: ' . $file;
    }
  }

  echo 'Zakończono proces wersjonowania plików!' . PHP_EOL;

  if (count($errors) > 0)
  {
    echo 'Wystąpiły następujące błędy:' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      echo $errors[$i] . PHP_EOL;
    }
    exit();
  }
?>
