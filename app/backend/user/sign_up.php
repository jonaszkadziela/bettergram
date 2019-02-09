<?php
  if ((defined('PHP_SESSION_ACTIVE') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
  {
    session_start();
  }

  $login = $_POST['login'];
  $email = $_POST['email'];
  $password1 = $_POST['password1'];
  $password2 = $_POST['password2'];

  $_SESSION['sign_up_form_login'] = $login;
  $_SESSION['sign_up_form_email'] = $email;
  $_SESSION['sign_up_form_password1'] = $password1;
  $_SESSION['sign_up_form_password2'] = $password2;

  $errors = array();

  require_once('../../../config.php');

  $invalid_request = false;
  if (empty($_POST['login']) || empty($_POST['email']) || empty($_POST['password1']) || empty($_POST['password2']))
  {
    $_SESSION['alert'] =
      '<p class="mb-1"><strong>Błąd!</strong> Niepoprawna ilość przesłanych parametrów!</p>' .
      '<p class="mb-0">Należy wypełnić wszystkie pola w formularzu.</p>';
    $invalid_request = true;
  }
  if ($_SERVER['REQUEST_METHOD'] !== 'POST')
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niepoprawna metoda przesłanego żądania!';
    $invalid_request = true;
  }
  if ($invalid_request)
  {
    header('Location: ' . ROOT_URL . 'index.php?page=register');
    exit();
  }

  require_once(ROOT_PATH . '/env.php');

  if (!preg_match("/^.{6,20}$/m", $login))
  {
    $errors[] = 'Login musi posiadać od 6 do 20 znaków!';
  }
  else if (!preg_match("/^[a-zA-Z0-9]{6,20}$/m", $login))
  {
    $errors[] = 'Login może składać się tylko z liter i cyfr (bez polskich znaków)!';
  }

  $email_filtered = filter_var($email, FILTER_SANITIZE_EMAIL);
  if (!filter_var($email_filtered, FILTER_VALIDATE_EMAIL) || $email_filtered != $email)
  {
    $errors[] = "Podany adres email jest nieprawidłowy!";
  }

  if ($password1 != $password2)
  {
    $errors[] = "Podane hasła nie są identyczne!";
  }
  else if (!preg_match("/^.{6,20}$/m", $password1))
  {
    $errors[] = 'Hasło musi posiadać od 6 do 20 znaków!';
  }
  else if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9!@#$%^&*]{6,20}$/m", $password1))
  {
    $errors[] = 'Hasło musi zawierać przynajmniej 1 dużą literę, 1 małą literę i 1 cyfrę!';
  }

  if (count($errors) == 0)
  {
    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        $error_no = $connection->connect_errno;
        throw new Exception(mysqli_connect_errno());
      }
      else
      {
        $connection->set_charset('utf8');

        $result = $connection->query("SELECT id FROM users WHERE email='$email'");
        if (!$result)
        {
          throw new Exception($connection->error);
        }
        if ($result->num_rows > 0)
        {
          $errors[] = "Istnieje już konto przypisane do tego adresu email!";
        }

        $result = $connection->query("SELECT id FROM users WHERE login='$login'");
        if (!$result)
        {
          throw new Exception($connection->error);
        }
        if ($result->num_rows > 0)
        {
          $errors[] = "Istnieje już użytkownik o takim loginie!";
        }

        if (count($errors) == 0)
        {
          $login = strtolower($login);
          $email = strtolower($email);
          $password_hashed = md5($password1);
          $date = date('Y-m-d');
          $permissions = 'użytkownik';
          $active = 1;

          if ($connection->query("INSERT INTO users VALUES (NULL, '$login', '$password_hashed', '$email', '$date', '$permissions', '$active')"))
          {
            $_SESSION['user_login'] = $login;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_sign_up_date'] = $date;
            $_SESSION['user_permissions'] = $permissions;
            $_SESSION['user_active'] = $active;
            $_SESSION['user_signed_in'] = true;

            $_SESSION['user_signed_up'] = true;

            header('Location: ' . ROOT_URL . 'index.php?page=welcome');
          }
          else
          {
            throw new Exception($connection->error);
          }
        }

        $result->close();
        $connection->close();
      }
    }
    catch(Exception $e)
    {
      $_SESSION['alert'] = '<h5 class="alert-heading">Wystąpił błąd podczas rejestracji!</h5>';
      if (isset($error_no))
      {
        $_SESSION['alert'] .= '<p class="mb-0">Błąd bazy danych #' . $error_no . '.</p>';
      }
      else
      {
        $_SESSION['alert'] .= '<p class="mb-0">Nie udało się stworzyć konta! Przepraszamy za niedogodności.</p>';
      }
      header('Location: ' . ROOT_URL . 'index.php?page=register');
    }
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Wystąpiły następujące błędy:</h5>' .
      '<ul class="mb-0">';
    for ($i = 0; $i < count($errors); $i++)
    {
      $_SESSION['alert'] .= "<li>$errors[$i]</li>";
    }
    $_SESSION['alert'] .= '</ul>';
    header('Location: ' . ROOT_URL . 'index.php?page=register');
  }
?>
