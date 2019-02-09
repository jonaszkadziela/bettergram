<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  function is_session_started()
  {
    if ((defined('PHP_SESSION_ACTIVE') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
    {
      return false;
    }
    return true;
  }

  function get_page_title($title = 'BetterGram')
  {
    switch ($_SESSION['current_view'])
    {
      case 'login':
        $title .= ' - Zaloguj się';
      break;

      case 'register':
        $title .= ' - Zarejestruj się';
      break;

      case 'welcome':
        $title .= ' - Witamy!';
      break;

      case 'gallery':
        $title .= ' - Galeria';
      break;

      case 'album':
        $title .= ' - Album';
      break;

      case 'photo':
        $title .= ' - Zdjęcie';
      break;

      case 'create_photo':
        $title .= ' - Dodaj zdjęcie';
      break;

      case 'create_album':
        $title .= ' - Załóż album';
      break;
    }
    return $title;
  }

  function console_log($data)
  {
    if (is_array($data))
    {
      $data = implode(',', $data);
    }
    echo "<script>console.log('PHP Debug: " . $data . "');</script>";
  }

  function truncate($text, $length = 100, $append = '&hellip;')
  {
    $text = trim($text);

    if (strlen($text) > $length)
    {
      $text = wordwrap($text, $length);
      $text = explode("\n", $text, 2);
      $text = $text[0] . $append;
    }

    return $text;
  }

  function formatted_size_to_bytes($size)
  {
    switch (strtolower(substr(trim($size), -1)))
    {
      case 'k':
        return (int)$size * 1024;

      case 'm':
        return (int)$size * 1024 * 1024;

      case 'g':
        return (int)$size * 1024 * 1024 * 1024;

      default:
        return (int)$size;
    }
  }

  function get_url($include_get_parameters = true)
  {
    $url = str_replace('index.php', '', $_SERVER['PHP_SELF']);
    if ($include_get_parameters)
    {
      return $url . '?' . http_build_query($_GET);
    }
    return $url;
  }

  function modify_url_parameters($parameters_hash)
  {
    $get_parameters = $_GET;
    foreach ($parameters_hash as $parameter => $value)
    {
      if ($value == '' || $value == null)
      {
        unset($get_parameters[$parameter]);
        continue;
      }
      $get_parameters[$parameter] = $value;
    }
    return get_url(false) . (count($get_parameters) > 0 ? '?' . http_build_query($get_parameters) : '');
  }

  function validate_request($request_type, $request_params)
  {
    for ($i = 0; $i < count($request_params); $i++)
    {
      if ($request_params[$i])
      {
        $_SESSION['alert'] =
          '<p class="mb-0-25"><strong>Błąd!</strong> Niepoprawna ilość przesłanych parametrów!</p>' .
          '<p class="mb-0">Należy wypełnić wszystkie pola w formularzu.</p>';
        return false;
      }
    }
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($request_type))
    {
      $_SESSION['alert'] = '<strong>Błąd!</strong> Niepoprawna metoda przesłanego żądania!';
      return false;
    }
    return true;
  }

  function clamp($value, $min, $max)
  {
    return max($min, min($max, $value));
  }

  function polish_suffix($count, $type)
  {
    $suffix = '';
    switch (strtolower($type))
    {
      case 'm':
      case 'meski':
        if ($count > 1 && $count < 5)
        {
          $suffix = 'y';
        }
        else if ($count == 0 || $count > 4)
        {
          $suffix = 'ów';
        }
      break;

      case 'z':
      case 'zenski':
        if ($count == 1)
        {
          $suffix = 'ę';
        }
        else if ($count > 1 && $count < 5)
        {
          $suffix = 'y';
        }
      break;
    }
    return $suffix;
  }

  function custom_database_query($sql)
  {
    require(ROOT_PATH . 'env.php');

    $result_array = array();

    try
    {
      $connection = new mysqli($database['host'], $database['user'], $database['password'], $database['name']);

      if ($connection->connect_errno != 0)
      {
        throw new Exception($connection->connect_errno);
      }

      $connection->set_charset('utf8');

      $result = $connection->query($sql);
      if (!$result)
      {
        throw new Exception($connection->errno);
      }
      if ($result->num_rows > 0)
      {
        for ($i = 0; $i < $result->num_rows; $i++)
        {
          $row = $result->fetch_assoc();
          $result_array[] = $row;
        }
      }
      $connection->close();
    }
    catch (Exception $e)
    {
      $_SESSION['alert'] =
        '<h5 class="alert-heading">Wystąpił błąd #' . $e->getMessage() . '!</h5>' . PHP_EOL .
        '<p class="mb-0">Nie udało się wczytać danych! Przepraszamy za niedogodności.</p>' . PHP_EOL;
    }
    return $result_array;
  }
?>
