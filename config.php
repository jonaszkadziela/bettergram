<?php
  $document_root_array = explode('/', $_SERVER['DOCUMENT_ROOT']);
  $parent_folders_array = explode('/', str_replace('\\', '/', __DIR__));
  $config_folder_name = end($document_root_array) == basename(__DIR__) ? '' : implode('/', array_diff($parent_folders_array, $document_root_array));

  $config_root_url = $config_folder_name == '' ? '/' : '/' . $config_folder_name . '/';
  $config_assets_url = $config_root_url . 'app/assets/';
  $config_images_url = $config_assets_url . 'images/';
  $config_javascripts_url = $config_assets_url . 'javascripts/';
  $config_stylesheets_url = $config_assets_url . 'stylesheets/';
  $config_backend_url = $config_root_url . 'app/backend/';
  $config_content_url = $config_root_url . 'content/';

  $config_root_path = str_replace('\\', '/', __DIR__) . '/';
  $config_assets_path = $config_root_path . 'app/assets/';
  $config_images_path = $config_assets_path . 'images/';
  $config_javascripts_path = $config_assets_path . 'javascripts/';
  $config_stylesheets_path = $config_assets_path . 'stylesheets/';
  $config_backend_path = $config_root_path . 'app/backend/';
  $config_content_path = $config_root_path . 'content/';
  $config_views_path = $config_root_path . 'app/views/';

  $config_assets_version = '2019-02-03';
  $config_assets_suffix = '_' . $config_assets_version . '.min';
  $config_max_execution_time_seconds = 300;
  $config_upload_max_filesize = '5M'; // Make sure that 'upload_max_filesize' in '.htaccess' is set at least to this value
  $config_memory_limit = '1024M';
  $config_allowed_photo_extensions = 'jpg, jpeg, png, gif';

  $config_pagination_length = 7;
  $config_photos_pagination_threshold = 20;
  $config_albums_pagination_threshold = 20;
  $config_comments_pagination_threshold = 10;
  $config_users_pagination_threshold = 20;
  $config_max_rating = 10;
  $config_photo_size = 1200; // Photos uploaded by users will be scaled down so that their longer side matches this value
  $config_photo_thumbnail_size = 360; // Thumbnails of photos will be scaled down so that their longer side matches this value
  $config_character_encoding = 'utf-8';
  $config_admin_mail = 'admin@jonaszkadziela.pl';
  $config_environment = 'development'; // Applicable values: 'development' or 'production'
  $config_time_zone = 'Europe/Warsaw';

  define('ROOT_URL', $config_root_url);
  define('ASSETS_URL', $config_assets_url);
  define('IMAGES_URL', $config_images_url);
  define('JAVASCRIPTS_URL', $config_javascripts_url);
  define('STYLESHEETS_URL', $config_stylesheets_url);
  define('BACKEND_URL', $config_backend_url);
  define('CONTENT_URL', $config_content_url);

  define('ROOT_PATH', $config_root_path);
  define('ASSETS_PATH', $config_assets_path);
  define('IMAGES_PATH', $config_images_path);
  define('JAVASCRIPTS_PATH', $config_javascripts_path);
  define('STYLESHEETS_PATH', $config_stylesheets_path);
  define('BACKEND_PATH', $config_backend_path);
  define('CONTENT_PATH', $config_content_path);
  define('VIEWS_PATH', $config_views_path);

  define('ASSETS_VERSION', $config_assets_version);
  define('ASSETS_VERSION_SUFFIX', $config_assets_suffix);
  define('MAX_EXECUTION_TIME', $config_max_execution_time_seconds);
  define('UPLOAD_MAX_FILESIZE', $config_upload_max_filesize);
  define('MEMORY_LIMIT', $config_memory_limit);
  define('ALLOWED_PHOTO_EXTENSIONS', str_replace(' ', '', $config_allowed_photo_extensions));

  define('PAGINATION_LENGTH', $config_pagination_length);
  define('PHOTOS_PAGINATION_THRESHOLD', $config_photos_pagination_threshold);
  define('ALBUMS_PAGINATION_THRESHOLD', $config_albums_pagination_threshold);
  define('COMMENTS_PAGINATION_THRESHOLD', $config_comments_pagination_threshold);
  define('USERS_PAGINATION_THRESHOLD', $config_users_pagination_threshold);
  define('MAX_RATING', $config_max_rating);
  define('PHOTO_SIZE', $config_photo_size);
  define('PHOTO_THUMBNAIL_SIZE', $config_photo_thumbnail_size);
  define('CHARACTER_ENCODING', $config_character_encoding);
  define('ADMIN_MAIL', $config_admin_mail);
  define('ENVIRONMENT', $config_environment);
  define('TIME_ZONE', $config_time_zone);

  require_once BACKEND_PATH . 'shared/classes.php';
  require_once BACKEND_PATH . 'shared/helpers.php';

  if (!is_session_started())
  {
    session_start();
  }
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  date_default_timezone_set(TIME_ZONE);
  ini_set('max_execution_time', MAX_EXECUTION_TIME);
  ini_set('memory_limit', MEMORY_LIMIT);

  $upload_max_filesize = formatted_size_to_bytes(UPLOAD_MAX_FILESIZE);
  $memory_limit = formatted_size_to_bytes(ini_get('memory_limit'));

  if ($upload_max_filesize > $memory_limit)
  {
    ini_set('memory_limit', UPLOAD_MAX_FILESIZE);
  }

  if (ENVIRONMENT === 'production')
  {
    error_reporting(0);
  }
?>
