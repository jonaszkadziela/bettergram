<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';

  $login = isset($_POST['login']) ? $_POST['login'] : null;
  $email = isset($_POST['email']) ? $_POST['email'] : null;
  $password1 = isset($_POST['password1']) ? $_POST['password1'] : null;
  $password2 = isset($_POST['password2']) ? $_POST['password2'] : null;
  $recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : null;

  $_SESSION['create_user_form']['login'] = sanitize_text($login);
  $_SESSION['create_user_form']['email'] = sanitize_text($email);
  $_SESSION['create_user_form']['password1'] = sanitize_text($password1);
  $_SESSION['create_user_form']['password2'] = sanitize_text($password2);

  if (!validate_request('post', [$login, $email, $password1, $password2]))
  {
    header('Location: ' . get_referrer_url());
    exit();
  }

  $errors = [];

  if (!preg_match('/^.{6,20}$/m', $login))
  {
    $errors[] = 'Login musi posiadać od 6 do 20 znaków!';
  }
  else if (!preg_match('/^[a-zA-Z0-9]{6,20}$/m', $login))
  {
    $errors[] = 'Login może składać się tylko z liter i cyfr (bez polskich znaków)!';
  }

  $email_filtered = filter_var($email, FILTER_SANITIZE_EMAIL);
  if (!filter_var($email_filtered, FILTER_VALIDATE_EMAIL) || $email_filtered != $email)
  {
    $errors[] = 'Podany adres email jest nieprawidłowy!';
  }

  if ($password1 != $password2)
  {
    $errors[] = 'Podane hasła nie są identyczne!';
  }
  else if (!preg_match('/^.{6,20}$/m', $password1))
  {
    $errors[] = 'Hasło musi posiadać od 6 do 20 znaków!';
  }
  else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m', $password1))
  {
    $errors[] = 'Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!';
  }

  if (count($errors) == 0)
  {
    try
    {
      $login = strtolower($login);
      $email = strtolower($email);

      $db = Database::get_instance();
      $result = $db->prepared_select_query('SELECT id FROM users WHERE email = ?;', [$email]);

      if ($result && count($result) > 0)
      {
        $errors[] = 'Istnieje już konto przypisane do tego adresu email!';
      }

      $result = $db->prepared_select_query('SELECT id FROM users WHERE login = ?;', [$login]);

      if ($result && count($result) > 0)
      {
        $errors[] = 'Istnieje już użytkownik o takim loginie!';
      }

      if (count($errors) == 0)
      {
        if (!check_recaptcha($recaptcha))
        {
          header('Location: ' . get_referrer_url());
          exit();
        }

        $password = password_hash($password1, PASSWORD_DEFAULT);
        $registration_date = (new DateTime())->format('Y-m-d');
        $permissions = 'użytkownik';
        $active = 1;

        $db->prepared_query('INSERT INTO users(id, login, email, password, registration_date, permissions, active) VALUES (NULL, ?, ?, ?, ?, ?, ?);', [$login, $email, $password, $registration_date, $permissions, $active]);

        $_SESSION['current_user']['id'] = $db->insert_id;
        $_SESSION['current_user']['login'] = $login;
        $_SESSION['current_user']['email'] = $email;
        $_SESSION['current_user']['registration_date'] = $registration_date;
        $_SESSION['current_user']['permissions'] = $permissions;
        $_SESSION['current_user']['active'] = $active;
        $_SESSION['current_user']['logged_in'] = true;
        $_SESSION['current_user']['registered'] = true;
        unset($_SESSION['create_user_form']);

        header('Location: ' . get_redirect_url());
        exit();
      }
    }
    catch (Exception $e)
    {
      $_SESSION['alert'][] =
        '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
        '<p class="m-0">Nie udało się stworzyć konta! Przepraszamy za niedogodności.</p>' . PHP_EOL;
      header('Location: ' . get_referrer_url());
      exit();
    }
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
