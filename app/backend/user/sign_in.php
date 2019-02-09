<?php
  if ((defined('PHP_SESSION_ACTIVE') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
  {
    session_start();
  }

  $login = $_POST['login'];
  $password = $_POST['password'];

  $_SESSION['sign_in_form_login'] = $login;

  $errors = array();

  require_once('../../../config.php');

  $invalid_request = false;
  if (empty($_POST['login']) || empty($_POST['password']))
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
    header('Location: ' . ROOT_URL . 'index.php?page=login');
    exit();
  }

  require_once(ROOT_PATH . '/env.php');

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

      $login = htmlentities($login, ENT_QUOTES, 'UTF-8');
      $login = strtolower($login);

      $result = $connection->query(sprintf("SELECT * FROM users WHERE login='%s' OR email='%s'", mysqli_real_escape_string($connection, $login), mysqli_real_escape_string($connection, $login)));
      if (!$result)
      {
        throw new Exception($connection->error);
      }
      if ($result->num_rows > 0)
      {
        $row = $result->fetch_assoc();

        if ($row['active'] != 1)
        {
          $errors[] = "Konto jest nieaktywne!";
        }
        else if (md5($password) == $row['password'])
        {
          $_SESSION['user_id'] = $row['id'];
          $_SESSION['user_login'] = $row['login'];
          $_SESSION['user_email'] = $row['email'];
          $_SESSION['user_sign_up_date'] = $row['sign_up_date'];
          $_SESSION['user_permissions'] = $row['permissions'];
          $_SESSION['user_active'] = $row['active'];

          $_SESSION['user_signed_in'] = true;

          unset($_SESSION['sign_in_form_login']);

          header('Location: ' . ROOT_URL . 'index.php?page=gallery');
        }
        else
        {
          $errors[] = "Nieprawidłowy login lub hasło!";
        }
      }
      else
      {
        $errors[] = "Nie ma takiego użytkownika!";
      }

      $result->close();
      $connection->close();
    }
  }
  catch(Exception $e)
  {
    $_SESSION['alert'] = '<h5 class="alert-heading">Wystąpił błąd podczas logowania!</h5>';
    if (isset($error_no))
    {
      $_SESSION['alert'] .= '<p class="mb-0">Błąd bazy danych #' . $error_no . '.</p>';
    }
    else
    {
      $_SESSION['alert'] .= '<p class="mb-0">Nie udało się zalogować do systemu! Przepraszamy za niedogodności.</p>';
    }
    header('Location: ' . ROOT_URL . 'index.php?page=login');
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
    header('Location: ' . ROOT_URL . 'index.php?page=login');
  }
?>
