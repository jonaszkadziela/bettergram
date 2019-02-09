<?php
  require_once 'config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $pages_for_anonymous_user = ['login', 'register'];
  $pages_for_signed_in_user = ['welcome', 'create_photo', 'create_album', 'account', 'admin_panel'];
  $pages_universal = ['gallery', 'top_photos', 'latest_photos', 'photo', 'album'];

  $pages_for_anonymous_user = array_merge($pages_for_anonymous_user, $pages_universal);
  $pages_for_signed_in_user = array_merge($pages_for_signed_in_user, $pages_universal);

  $target_page = 'gallery';

  if (isset($_GET['page']))
  {
    $pages = isset($_SESSION['current_user']['logged_in']) ? $pages_for_signed_in_user : $pages_for_anonymous_user;
    foreach ($pages as $page)
    {
      if ($_GET['page'] == $page)
      {
        $target_page = $page;
        break;
      }
    }

    if ($_GET['page'] != $target_page)
    {
      if (empty($_SESSION['current_user']['logged_in']) && in_array($_GET['page'], $pages_for_signed_in_user))
      {
        $_SESSION['target_url'] = get_url();
        $_GET['redirect'] = 1;
        header('Location: ' . ROOT_URL . modify_get_parameters(['page' => 'login']));
        exit();
      }
      else
      {
        $_SESSION['notice'][] = 'Nie można wyświetlić żądanej strony!';
      }
      header('Location: ' . ROOT_URL . modify_get_parameters(['page' => $target_page]));
      exit();
    }
  }

  if (isset($_GET['action']))
  {
    switch ($_GET['action'])
    {
      case 'log_out':
        header('Location: ' . BACKEND_URL . 'user/log_out.php');
      break;

      case 'create_thumbnails':
        $get_parameters = $_GET;
        unset($get_parameters['action']);
        header('Location: ' . BACKEND_URL . 'admin/create_thumbnails.php' . get_parameters($get_parameters));
      break;
    }
    exit();
  }

  $pages_for_target_url = ['login', 'register', 'welcome'];
  if (!in_array($target_page, $pages_for_target_url))
  {
    unset($_SESSION['target_url']);
  }

  if (isset($_GET['redirect']))
  {
    if (isset($_SESSION['target_url']))
    {
      $activity = 'zakończeniu czynności';
      if (isset($_GET['page']))
      {
        switch ($_GET['page'])
        {
          case 'login':
            $activity = 'zalogowaniu';
          break;

          case 'register':
            $activity = 'zarejestrowaniu';
          break;
        }
      }
      $_SESSION['notice'][] = 'Po ' . $activity . ' nastąpi przekierowanie na żądaną stronę!';
    }
    else
    {
      header('Location: ' . ROOT_URL . modify_get_parameters(['redirect' => null]));
      exit();
    }
  }

  $_SESSION['current_page'] = $target_page;
?>
