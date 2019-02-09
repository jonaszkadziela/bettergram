<?php
  $pages_for_anonymous_user = ['login', 'register'];
  $pages_for_signed_in_user = ['welcome'];
  $pages_universal = ['gallery'];

  $pages_for_anonymous_user = array_merge($pages_for_anonymous_user, $pages_universal);
  $pages_for_signed_in_user = array_merge($pages_for_signed_in_user, $pages_universal);

  $page = isset($_SESSION['user_signed_in']) ? 'gallery' : 'login';

  if (isset($_GET['page']))
  {
    if (empty($_SESSION['user_signed_in']))
    {
      foreach ($pages_for_anonymous_user as $p)
      {
        if ($_GET['page'] == $p)
        {
          $new_page = $p;
          break;
        }
        else
        {
          $new_page = $page;
        }
      }
      $page = $new_page;
    }
    else
    {
      foreach ($pages_for_signed_in_user as $p)
      {
        if ($_GET['page'] == $p)
        {
          $new_page = $p;
          break;
        }
        else
        {
          $new_page = $page;
        }
      }
      if ($new_page == 'welcome' && empty($_SESSION['user_signed_up']))
      {
        $new_page = $page;
      }
      $page = $new_page;
    }

    if ($_GET['page'] != $page)
    {
      $url = $_GET;
      $url['page'] = $page;
      $url = $_SERVER['PHP_SELF'] . '?' . http_build_query($url);
      header('Location: ' . $url);
    }
  }

  if (isset($_GET['action']))
  {
    switch ($_GET['action'])
    {
      case 'sign_out':
        header('Location: ' . BACKEND_PATH . 'user/sign_out.php');
      break;
    }
  }
?>
