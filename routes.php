<?php
  require_once('config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  $views_for_anonymous_user = ['login', 'register'];
  $views_for_signed_in_user = ['welcome', 'create_photo', 'create_album'];
  $views_universal = ['gallery', 'photo', 'album'];

  $views_for_anonymous_user = array_merge($views_for_anonymous_user, $views_universal);
  $views_for_signed_in_user = array_merge($views_for_signed_in_user, $views_universal);

  $view = isset($_SESSION['user_signed_in']) ? 'gallery' : 'login';

  if (isset($_GET['view']))
  {
    if (empty($_SESSION['user_signed_in']))
    {
      foreach ($views_for_anonymous_user as $v)
      {
        if ($_GET['view'] == $v)
        {
          $new_view = $v;
          break;
        }
        else
        {
          $new_view = $view;
        }
      }
      $view = $new_view;
    }
    else
    {
      foreach ($views_for_signed_in_user as $v)
      {
        if ($_GET['view'] == $v)
        {
          $new_view = $v;
          break;
        }
        else
        {
          $new_view = $view;
        }
      }
      if ($new_view == 'welcome' && empty($_SESSION['user_signed_up']))
      {
        $new_view = $view;
      }
      $view = $new_view;
    }

    if ($_GET['view'] != $view)
    {
      if (empty($_SESSION['user_signed_in']))
      {
        $_SESSION['target_url'] = get_url();
        $_GET['redirect'] = 1;
      }
      else
      {
        $_SESSION['alert'] = 'Nie można wyświetlić żądanej strony!';
        $_SESSION['alert_class'] = 'alert-info';
      }
      header('Location: ' . modify_url_parameters(array('view' => $view)));
      exit();
    }
  }

  if (isset($_GET['action']))
  {
    switch ($_GET['action'])
    {
      case 'sign_out':
        header('Location: ' . BACKEND_URL . 'user/sign_out.php');
      break;
    }
    exit();
  }

  $views_for_target_url = ['login', 'register', 'welcome'];
  if (!isset($_GET['view']) || !in_array($_GET['view'], $views_for_target_url))
  {
    unset($_SESSION['target_url']);
  }

  if (isset($_GET['redirect']))
  {
    if (isset($_SESSION['target_url']))
    {
      $activity = 'zakończeniu czynności';
      if (isset($_GET['view']))
      {
        switch ($_GET['view'])
        {
          case 'login':
            $activity = 'zalogowaniu';
          break;

          case 'register':
            $activity = 'zarejestrowaniu';
          break;
        }
      }
      $_SESSION['alert'] = 'Po ' . $activity . ' nastąpi przekierowanie na żądaną stronę!';
      $_SESSION['alert_class'] = 'alert-info';
    }
    else
    {
      header('Location: ' . modify_url_parameters(array('redirect' => null)));
      exit();
    }
  }

  $_SESSION['current_view'] = $view;
?>
