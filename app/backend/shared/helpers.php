<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  /**
   * Determines whether the session has been already started
   *
   * @return bool Returns true if the session has been already started
   */
  function is_session_started() : bool
  {
    if ((defined('PHP_SESSION_ACTIVE') && session_status() === PHP_SESSION_ACTIVE) || session_id())
    {
      return true;
    }
    return false;
  }

  /**
   * Determines a page title depending on current page or error
   *
   * @param string $title A string that will be displayed in front of suffix
   * @return string Returns a page title
   */
  function get_page_title($title = 'BetterGram') : string
  {
    if (isset($_GET['error']))
    {
      return $title .= ' - Błąd #' . $_GET['error'];
    }
    switch ($_SESSION['current_page'])
    {
      case 'login':
        return $title .= ' - Zaloguj się';
      break;

      case 'register':
        return $title .= ' - Zarejestruj się';
      break;

      case 'welcome':
        return $title .= ' - Witamy!';
      break;

      case 'gallery':
        return $title .= ' - Galeria';
      break;

      case 'album':
        return $title .= ' - Album';
      break;

      case 'top_photos':
        return $title .= ' - Najlepiej oceniane';
      break;

      case 'latest_photos':
        return $title .= ' - Najnowsze';
      break;

      case 'photo':
        return $title .= ' - Zdjęcie';
      break;

      case 'create_photo':
        return $title .= ' - Dodaj zdjęcie';
      break;

      case 'create_album':
        return $title .= ' - Załóż album';
      break;

      case 'account':
        return $title .= ' - Moje konto';
      break;

      case 'admin_panel':
        return $title .= ' - Panel administracyjny';
      break;
    }
    return $title;
  }

  /**
   * Truncates a given text and appends specified string at the end
   *
   * @param string $text The text to be truncated
   * @param int $length A length after which the function will attempt to truncate the given text
   * @param string $append A string that will be appended to the truncated text
   * @return string Returns a truncated text
   */
  function truncate($text, $length = 100, $append = '&hellip;') : string
  {
    $text = trim($text);
    if (strlen($text) > $length)
    {
      $text = wordwrap($text, $length, "\n", true);
      $text = explode("\n", $text, 2);
      $text = $text[0] . $append;
    }
    return $text;
  }

  /**
   * Converts human-readable size to bytes understood by a machine
   *
   * @param string $size Human-readable formatted size (e.g. 512K, 256M, 1G)
   * @return int Returns formatted size converted to bytes
   */
  function formatted_size_to_bytes($size) : int
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

  /**
   * Builds URL-compliant get parameters string
   *
   * @param array $params An array to be evaluated as a get parameters array
   * @return string Returns get parameters as a URL-compliant string
   */
  function get_parameters($params = null) : string
  {
    // This deliberately has a default value of null, since otherwise setting $_GET to an empty array doesn't work
    $params = is_null($params) ? $_GET : $params;
    return count($params) > 0 ? '?' . http_build_query($params) : '';
  }

  /**
   * Modifies current get parameters
   *
   * @param array $params_hash An associative array of parameters to modify
   * @return string Returns modified get parameters as a URL-compliant string
   */
  function modify_get_parameters(array $params_hash) : string
  {
    $get_parameters = $_GET;
    foreach ($params_hash as $param => $value)
    {
      if ($value == '' || is_null($value))
      {
        unset($get_parameters[$param]);
        continue;
      }
      $get_parameters[$param] = $value;
    }
    return get_parameters($get_parameters);
  }

  /**
   * Determines the current URL and removes unnecessary 'index.php' from it
   *
   * @param bool $include_get_params Determines whether to include get parameters into the returned URL
   * @return string Returns the current URL
   */
  function get_url($include_get_params = true) : string
  {
    $url = str_replace('index.php', '', $_SERVER['PHP_SELF']);
    if ($include_get_params)
    {
      return $url . get_parameters();
    }
    return $url;
  }

  /**
   * Determines the best URL to redirect a user to the previous page
   *
   * @return string Returns a URL to which a user will be redirected
   */
  function get_referrer_url() : string
  {
    if (isset($_SESSION['referrer_url']))
    {
      return $_SESSION['referrer_url'];
    }
    else if (isset($_SERVER['HTTP_REFERER']))
    {
      return $_SERVER['HTTP_REFERER'];
    }
    return ROOT_URL;
  }

  /**
   * Determines the best URL to redirect a user after successful interaction with backend
   *
   * @return string Returns a URL to which a user will be redirected
   */
  function get_redirect_url() : string
  {
    if (isset($_SESSION['redirect_url']))
    {
      return $_SESSION['redirect_url'];
    }
    return get_referrer_url();
  }

  /**
   * Validates whether the request met the specified requirements
   *
   * @param string $request_type The accepted request type
   * @param array $request_params An array of variables to test
   * @return bool Returns true if the request met all the specified requirements
   */
  function validate_request($request_type, array $request_params) : bool
  {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($request_type))
    {
      $_SESSION['alert'][] = '<h5>Błąd!</h5> Niepoprawna metoda przesłanego żądania!';
      return false;
    }
    for ($i = 0; $i < count($request_params); $i++)
    {
      if (empty($request_params[$i]))
      {
        $_SESSION['alert'][] = '<h5>Błąd!</h5><p class="m-0">Należy wypełnić wszystkie wymagane pola w formularzu!</p>' . PHP_EOL;
        return false;
      }
    }
    return true;
  }

  /**
   * Fetches the result of reCAPTCHA test
   *
   * @param string $token reCAPTCHA token
   * @return array Returns an associative array of result
   */
  function get_recaptcha_response($token) : array
  {
    require ROOT_PATH . 'env.php';
    $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $env['recaptcha']['secret_key'] . '&response=' . $token);
    return json_decode($response, true);
  }

  /**
   * Checks if recaptcha verification was completed successfully
   *
   * @param string $recaptcha Value of reCAPTCHA field
   * @param bool $silent When set to true no error message will be generated
   * @return bool Returns true if recaptcha verification was completed successfully, otherwise returns false
   */
  function check_recaptcha($recaptcha, $silent = false) : bool
  {
    if (!RECAPTCHA_ENABLED)
    {
      return true;
    }

    if (empty($recaptcha))
    {
      if (!$silent)
      {
        $_SESSION['alert'][] = '<h5>Błąd!</h5> Nie przesłano tokenu niezbędnego do weryfikacji reCAPTCHA!';
      }
      return false;
    }

    $response = get_recaptcha_response($recaptcha);
    if (!$response['success'] || $response['score'] < RECAPTCHA_SCORE_THRESHOLD)
    {
      if (!$silent)
      {
        $_SESSION['alert'][] = '<h5>Błąd!</h5> Weryfikacja reCAPTCHA zakończona niepowodzeniem! Spróbuj ponownie później.';
      }
      return false;
    }
    return true;
  }

  /**
   * Converts permissions given as a string to an integer value (higher is better)
   *
   * @param string $permissions Permissions that will be converted
   * @return int Returns permissions as an integer value
   */
  function permissions_to_int($permissions) : int
  {
    switch (strtolower($permissions))
    {
      default:
      case 'uzytkownik':
        return 10;

      case 'moderator':
        return 20;

      case 'administrator':
        return 30;
    }
  }

  /**
   * Determines whether a user has enough permissions to perform a certain action
   *
   * @param string $required_permissions Required permissions to perform a certain action
   * @param string $user_permissions User's permissions (by default they are retrieved from $_SESSION)
   * @return bool Returns true if a user has met the requirements
   */
  function has_enough_permissions($required_permissions, $user_permissions = null) : bool
  {
    $user_permissions = is_null($user_permissions) && isset($_SESSION['current_user']['permissions']) ? $_SESSION['current_user']['permissions'] : null;
    return (permissions_to_int($user_permissions) >= permissions_to_int($required_permissions));
  }

  /**
   * Clamps a value between specified min and max
   *
   * @param mixed $value The value to be clamped
   * @param mixed $min A minimum accepted value
   * @param mixed $max A maximum accepted value
   * @return mixed Returns a value clamped between min and max
   */
  function clamp($value, $min, $max)
  {
    return max($min, min($max, $value));
  }

  /**
   * Determines an appropriate polish suffix to the given noun based on a specified count and case
   *
   * @see https://en.wikibooks.org/wiki/Polish/Noun_cases
   * @param string $noun The noun to which a polish suffix will be added
   * @param int $count A count of items described by the noun
   * @param string $case A noun case that will be utilized
   * @return string Returns the given noun with an appropriate polish suffix at the end
   */
  function polish_suffix($noun, $count, $case = 'mianownik') : string
  {
    $last_digit = substr($count, -1);
    $last_two_digits = substr($count, -2);
    $suffix = '';

    switch (mb_strtolower($noun))
    {
      // Currently tested case only for 'mianownik'
      case 'głos':
        if (($last_two_digits != 12 && $last_two_digits != 13 && $last_two_digits != 14) &&
            ($last_digit == 2 || $last_digit == 3 || $last_digit == 4))
        {
          switch (mb_strtolower($case))
          {
            default:
              $suffix = 'y';
            break;
          }
        }
        else if ($count != 1)
        {
          switch (mb_strtolower($case))
          {
            default:
              $suffix = 'ów';
            break;
          }
        }
        else
        {
          switch (mb_strtolower($case))
          {
            default:
              $suffix = '';
            break;
          }
        }
      break;

      // Currently tested case only for 'mianownik' and 'biernik'
      case 'miniatur':
        if (($last_two_digits != 12 && $last_two_digits != 13 && $last_two_digits != 14) &&
        ($last_digit == 2 || $last_digit == 3 || $last_digit == 4))
        {
          switch (mb_strtolower($case))
          {
            default:
              $suffix = 'y';
            break;
          }
        }
        else if ($count != 1)
        {
          switch (mb_strtolower($case))
          {
            default:
              $suffix = '';
            break;
          }
        }
        else
        {
          switch (mb_strtolower($case))
          {
            case 'biernik':
              $suffix = 'ę';
            break;

            default:
              $suffix = 'a';
            break;
          }
        }
      break;
    }
    return $noun . $suffix;
  }

  /**
   * Determines an appropriate path to an asset based on the configured environment
   *
   * @param string $file Path to an asset relative to assets javascripts/stylesheets folder
   * @return string Returns a relative path to the requested asset
   */
  function get_asset_path($file) : string
  {
    $file_array = pathinfo($file);
    $targets =
    [
      'development' => 'compiled/' . $file,
      'production' => 'dist/' . ($file_array['dirname'] != '.' ? $file_array['dirname'] . '/' : '') .
                       $file_array['filename'] . ASSETS_VERSION_SUFFIX . '.' . $file_array['extension']
    ];

    switch (strtolower($file_array['extension']))
    {
      case 'js':
        $target_path = JAVASCRIPTS_PATH;
        $target_url = JAVASCRIPTS_URL;
      break;

      case 'css':
        $target_path = STYLESHEETS_PATH;
        $target_url = STYLESHEETS_URL;
      break;

      default:
        return '';
    }

    if (ENVIRONMENT === 'development')
    {
      $target_array = explode('.', $targets['development']);
      $target = implode('.', array_slice($target_array, 0, count($target_array) - 1));
      $files = glob($target_path . $target . '*');
      $targets['development'] = count($files) > 0 ? 'compiled/' . basename($files[0]) : '';
    }

    if (file_exists($target_path . $targets[ENVIRONMENT]))
    {
      return $target_url . $targets[ENVIRONMENT];
    }

    if (ENVIRONMENT !== 'production')
    {
      if (file_exists($target_path . $targets['production']))
      {
        return $target_url . $targets['production'];
      }
    }

    return '';
  }

  /**
   * Sanitizes a given variable using htmlspecialchars() and filter_var() functions
   *
   * @see http://php.net/manual/en/function.htmlspecialchars.php
   * @see http://php.net/manual/en/function.filter-var.php
   * @param string $text Text that will be decoded
   * @return string Returns sanitized text
   */
  function sanitize_text($text, $trim = true) : string
  {
    $text = $trim ? trim($text) : $text;
    $text = htmlspecialchars($text, ENT_COMPAT | ENT_HTML5, CHARACTER_ENCODING);
    return filter_var($text, FILTER_SANITIZE_STRING);
  }

  /**
   * Recursively removes directory and its contents
   *
   * @param string $dir Absolute path to a directory
   * @return bool Returns true if removed the directory successfully
   */
  function rmdir_recursive($dir) : bool
  {
    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file)
    {
      (is_dir("$dir/$file")) ? rmdir_recursive("$dir/$file") : unlink("$dir/$file");
    }

    return rmdir($dir);
  }

  /**
   * Executes given php file and returns its output
   *
   * @param string $file Path to a php file relative to project's root folder
   * @param array $args Optional associative array containing variables that will be passed to the given script
   * @return string Returns the output of executed php script
   */
  function get_php_output($file, array $args = []) : string
  {
    $file_array = pathinfo($file);
    $result = '';

    if (strtolower($file_array['extension']) === 'php' && file_exists($file))
    {
      if (!empty($args))
      {
        foreach ($args as $variable => $value)
        {
          ${$variable} = $value;
        }
      }
      ob_start();
      include $file;
      $result = ob_get_clean();
    }

    return $result;
  }

  /**
   * Converts charsets supported by php function htmlspecialchars() to format understood by mysqli
   *
   * @see http://php.net/manual/en/function.htmlspecialchars.php
   * @param string $charset Charset name to be converted
   * @return string Returns charset name compliant with mysqli
   */
  function php_to_mysqli_charset($charset) : string
  {
    $charset = strtolower($charset);
    switch ($charset)
    {
      case 'utf-8':
        return 'utf8';

      case 'ibm866':
      case '866':
        return 'cp866';

      case 'windows-1251':
      case 'win-1251':
      case '1251':
        return 'cp1251';

      case 'cp1252':
      case 'windows-1252':
      case '1252':
        return 'latin1';

      case 'koi8-r':
      case 'koi8-ru':
        return 'koi8r';

      case '950':
        return 'big5';

      case '936':
        return 'gb2312';

      case 'shift_jis':
      case 'sjis':
      case 'sjis-win':
      case '932':
        return 'cp932';

      case 'euc-jp':
      case 'eucjp':
        return 'ujis';

      case 'eucjp-win':
        return 'eucjpms';

      default:
        return $charset;
    }
  }

  /**
   * Fetches gravatar for a given email address
   *
   * @see https://en.gravatar.com/site/implement/images/
   * @param string $email Email address linked to the gravatar
   * @param int $size Avatar size (width and height) in pixels
   * @param string $rating Decency rating of avatar
   * @param string $default Specify default image when gavatar was not found
   * @return string Returns URL to the gravatar
   */
  function get_gravatar_url($email, $size = 64, $rating = 'pg', $default = null) : string
  {
    $default = is_null($default) ? GRAVATAR_DEFAULT_IMAGE : $default;
    $email = empty($email) ? '' : md5(strtolower(trim($email)));
    $gravatar_url = "https://www.gravatar.com/avatar/$email?s=$size&r=$rating&d=$default";

    return $gravatar_url;
  }
?>
