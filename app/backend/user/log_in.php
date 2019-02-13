<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $login = isset($_POST['login']) ? $_POST['login'] : null;
  $password = isset($_POST['password']) ? $_POST['password'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  $_SESSION['log_in_user_form']['login'] = sanitize_text($login);

  if (!validate_request('post', [$login, $password]))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  if (!check_recaptcha($recaptcha))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  $errors = [];

  try
  {
    $login = strtolower($login);

    $db = Database::get_instance();
    $result = $db->prepared_select_query('SELECT * FROM users WHERE login = ?;', [$login]);

    if ($result && count($result) > 0)
    {
      if ($result[0]['active'] != 1)
      {
        $errors[] = 'Konto jest nieaktywne!';
      }
      else if (password_verify($password, $result[0]['password']))
      {
        $_SESSION['current_user']['id'] = $result[0]['id'];
        $_SESSION['current_user']['login'] = $result[0]['login'];
        $_SESSION['current_user']['email'] = $result[0]['email'];
        $_SESSION['current_user']['registration_date'] = $result[0]['registration_date'];
        $_SESSION['current_user']['permissions'] = $result[0]['permissions'];
        $_SESSION['current_user']['active'] = $result[0]['active'];
        $_SESSION['current_user']['logged_in'] = true;
        unset($_SESSION['log_in_user_form']);

        if (isset($_SESSION['target_url']))
        {
          $target_url = $_SESSION['target_url'];
          unset($_SESSION['target_url']);
          header('Location: ' . $target_url);
          exit();
        }
        else
        {
          header('Location: ' . get_redirect_url());
          exit();
        }
      }
      else
      {
        $errors[] = 'Nieprawidłowy login lub hasło!';
      }
    }
    else
    {
      $errors[] = 'Nie ma takiego użytkownika!';
    }
  }
  catch (Exception $e)
  {
    $_SESSION['alert'][] =
      '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
      '<p class="m-0">Nie udało się zalogować do systemu! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    header('Location: ' . get_referrer_url());
    exit();
  }

  if (count($errors) > 0)
  {
    $alert =
      '<h5>Wystąpiły następujące błędy:</h5>' . PHP_EOL .
      '<ul class="mb-0 pl-1-25">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $alert .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $alert .= '</ul>' . PHP_EOL;
    $_SESSION['alert'][] = $alert;
    header('Location: ' . get_referrer_url());
    exit();
  }
?>
