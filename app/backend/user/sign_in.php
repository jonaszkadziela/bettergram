<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');

  $login = isset($_POST['login']) ? $_POST['login'] : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';

  $_SESSION['sign_in_form_login'] = $login;

  if (!validate_request('post', array(empty($_POST['login']), empty($_POST['password']))))
  {
    header('Location: ' . ROOT_URL . '?view=login');
    exit();
  }

  $errors = array();

  require(ROOT_PATH . 'env.php');

  try
  {
    $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

    if ($connection->connect_errno != 0)
    {
      throw new Exception($connection->connect_errno);
    }

    $connection->set_charset('utf8');

    $login = htmlentities($login, ENT_QUOTES, 'UTF-8');
    $login = strtolower($login);

    $result = $connection->query(sprintf("SELECT * FROM users WHERE login='%s'", mysqli_real_escape_string($connection, $login)));
    if (!$result)
    {
      throw new Exception($connection->errno);
    }
    if ($result->num_rows > 0)
    {
      $row = $result->fetch_assoc();

      if ($row['active'] != 1)
      {
        $errors[] = 'Konto jest nieaktywne!';
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

        if (isset($_SESSION['target_url']))
        {
          $target_url = $_SESSION['target_url'];
          unset($_SESSION['target_url']);
          header('Location: ' . $target_url);
          exit();
        }
        else
        {
          header('Location: ' . ROOT_URL . '?view=gallery');
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
    $result->close();
    $connection->close();
  }
  catch (Exception $e)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
      '<p class="mb-0">Nie udało się zalogować do systemu! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    header('Location: ' . ROOT_URL . '?view=login');
    exit();
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Wystąpiły następujące błędy:</h5>' . PHP_EOL .
      '<ul class="mb-0">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $_SESSION['alert'] .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $_SESSION['alert'] .= '</ul>' . PHP_EOL;
    header('Location: ' . ROOT_URL . '?view=login');
    exit();
  }
?>
