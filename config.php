<?php
  $document_root_array = explode('/', $_SERVER['DOCUMENT_ROOT']);
  $parent_folders_array = explode('/', str_replace('\\', '/', dirname(__FILE__)));
  $config_folder_name = end($document_root_array) == basename(dirname(__FILE__)) ? '' : implode('/', array_diff($parent_folders_array, $document_root_array));

  $config_root_url = $config_folder_name == '' ? '/' : '/' . $config_folder_name . '/';
  $config_assets_url = $config_root_url . 'app/assets/';
  $config_backend_url = $config_root_url . 'app/backend/';
  $config_content_url = $config_root_url . 'content/';

  $config_root_path = str_replace('\\', '/', dirname(__FILE__)) . '/';
  $config_backend_path = $config_root_path . 'app/backend/';
  $config_components_path = $config_root_path . 'app/components/';
  $config_content_path = $config_root_path . 'content/';
  $config_views_path = $config_root_path . 'app/views/';

  $config_max_execution_time_seconds = 300;
  $config_upload_max_filesize = '5M'; // Make sure that 'upload_max_filesize' in 'php.ini' is set at least to this value
  $config_memory_limit = '1024M';
  $config_allowed_photo_extensions = 'jpg, jpeg, png, gif';

  $config_pagination_length = 7;
  $config_photo_pagination_threshold = 20;
  $config_album_pagination_threshold = 20;
  $config_max_rating = 10;
  $config_admin_mail = 'admin@jonaszkadziela.pl';
  $config_environment = 'development'; // Applicable values: 'development' or 'production'
  $config_time_zone = 'Europe/Warsaw';

  define('ROOT_URL', $config_root_url);
  define('ASSETS_URL', $config_assets_url);
  define('BACKEND_URL', $config_backend_url);
  define('CONTENT_URL', $config_content_url);

  define('ROOT_PATH', $config_root_path);
  define('BACKEND_PATH', $config_backend_path);
  define('COMPONENTS_PATH', $config_components_path);
  define('CONTENT_PATH', $config_content_path);
  define('VIEWS_PATH', $config_views_path);

  define('MAX_EXECUTION_TIME', $config_max_execution_time_seconds);
  define('UPLOAD_MAX_FILESIZE', $config_upload_max_filesize);
  define('MEMORY_LIMIT', $config_memory_limit);
  define('ALLOWED_PHOTO_EXTENSIONS', str_replace(' ', '', $config_allowed_photo_extensions));

  define('PAGINATION_LENGTH', $config_pagination_length);
  define('PHOTO_PAGINATION_THRESHOLD', $config_photo_pagination_threshold);
  define('ALBUM_PAGINATION_THRESHOLD', $config_album_pagination_threshold);
  define('MAX_RATING', $config_max_rating);
  define('ADMIN_MAIL', $config_admin_mail);
  define('ENVIRONMENT', $config_environment);

  require_once(BACKEND_PATH . 'shared/helpers.php');

  if (!is_session_started())
  {
    session_start();
  }
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  date_default_timezone_set($config_time_zone);
  ini_set('max_execution_time', $config_max_execution_time_seconds);
  ini_set('memory_limit', $config_memory_limit);

  $max_upload_size = formatted_size_to_bytes(UPLOAD_MAX_FILESIZE);
  $memory_limit = formatted_size_to_bytes(ini_get('memory_limit'));
  $post_max_size = formatted_size_to_bytes(ini_get('post_max_size'));

  if ($max_upload_size > $memory_limit)
  {
    ini_set('memory_limit', UPLOAD_MAX_FILESIZE);
  }
  if ($max_upload_size > $post_max_size)
  {
    ini_set('post_max_size', UPLOAD_MAX_FILESIZE);
  }

  if (ENVIRONMENT == 'production')
  {
    error_reporting(0);
  }
?>
