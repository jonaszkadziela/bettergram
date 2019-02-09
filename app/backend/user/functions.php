<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }
  require_once(BACKEND_PATH . 'shared/classes.php');

  function get_user($user_id)
  {
    require(ROOT_PATH . 'env.php');

    $user = null;

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query("SELECT id, login, email, sign_up_date, permissions, active FROM users WHERE id='$user_id'");
      if (!$result)
      {
        throw new Exception($connection->errno);
      }
      if ($result->num_rows > 0)
      {
        $row = $result->fetch_assoc();
        $user = new User
        (
          $row['id'],
          $row['login'],
          $row['email'],
          $row['sign_up_date'],
          $row['permissions'],
          $row['active']
        );
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się pobrać danych użytkownika! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $user;
  }
?>
