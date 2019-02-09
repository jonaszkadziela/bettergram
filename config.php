<?php
  $document_root_array = explode('/', $_SERVER['DOCUMENT_ROOT']);
  $parent_folders_array = explode('/', str_replace('\\', '/', dirname(__FILE__)));
  $config_folder_name = $document_root_array[count($document_root_array) - 1] == basename(__DIR__) ? '' : implode('/', array_diff($parent_folders_array, $document_root_array));
  $config_root_url = $config_folder_name == '' ? '/' : '/' . $config_folder_name . '/';
  $config_root_path = dirname(__FILE__);
  $config_views_path = $config_root_path . '/app/views/';
  $config_backend_path = $config_root_url . 'app/backend/';
  $config_assets_path = $config_root_url . 'assets/';
  $config_admin_mail = 'admin@jonaszkadziela.pl';

  define('ROOT_URL', $config_root_url);
  define('ROOT_PATH', $config_root_path);
  define('VIEWS_PATH', $config_views_path);
  define('BACKEND_PATH', $config_backend_path);
  define('ASSETS_PATH', $config_assets_path);
  define('ADMIN_MAIL', $config_admin_mail);
?>
