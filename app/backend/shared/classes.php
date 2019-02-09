<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  /**
   * Custom mysqli class with additional functionality which uses a singleton pattern
   */
  class Database extends mysqli
  {
    private static $options = [];

    public function __construct()
    {
      require ROOT_PATH . 'env.php';
      $o = self::$options;

      try
      {
        @parent::__construct
        (
          isset($o['host']) ? $o['host'] : $env['db']['host'],
          isset($o['user']) ? $o['user'] : $env['db']['user'],
          isset($o['password']) ? $o['password'] : $env['db']['password'],
          isset($o['name']) ? $o['name'] : $env['db']['name'],
          isset($o['port']) ? $o['port'] : $env['db']['port'],
          isset($o['socket']) ? $o['socket'] : $env['db']['socket']
        );

        if ($this->connect_errno !== 0)
        {
          throw new Exception($this->connect_errno);
        }

        $this->set_charset(php_to_mysqli_charset(CHARACTER_ENCODING));
      }
      catch (Exception $e)
      {
        $_SESSION['alert'][] =
          '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
          '<p class="m-0">Nie udało się połączyć z bazą danych! Przepraszamy za niedogodności.</p>' . PHP_EOL;
      }
    }

    public static function set_options(array $options)
    {
      self::$options = array_merge(self::$options, $options);
    }

    public static function get_instance()
    {
      static $instance = null;

      if (is_null($instance))
      {
        $instance = new static();
      }

      return $instance;
    }

    /**
     * Performs a given database query
     *
     * @param string $query SQL query to be executed
     * @param array $args Optional array containing variables that will be imbedded into the query
     * @return bool|mysqli_result Returns bool or mysqli_result depending on the given query
     */
    public function prepared_query($query, array $args = [])
    {
      if ($this->connect_errno !== 0)
      {
        throw new Exception('PQ_1');
      }

      $stmt = $this->prepare($query);
      if (!$stmt)
      {
        throw new Exception('PQ_2');
      }

      if (!empty($args))
      {
        $params = [];
        $types = array_reduce($args, function($type, &$arg) use (&$params)
        {
          $params[] = &$arg;
          if (is_float($arg))
          {
            $type .= 'd';
          }
          else if (is_integer($arg))
          {
            $type .= 'i';
          }
          else if (is_string($arg))
          {
            $type .= 's';
          }
          else
          {
            $type .= 'b';
          }
          return $type;
        }, '');

        array_unshift($params, $types);
        if (!call_user_func_array([$stmt, 'bind_param'], $params))
        {
          throw new Exception('PQ_3');
        }
      }

      $result = $stmt->execute();
      if (!$result)
      {
        throw new Exception('PQ_4');
      }
      if ($this->field_count)
      {
        $result = $stmt->get_result();
        if (!$result)
        {
          throw new Exception('PQ_5');
        }
      }
      $stmt->close();
      return $result;
    }

    /**
     * Performs a given SELECT query
     *
     * @param string $query SQL SELECT query to be executed
     * @param array $args Optional array containing variables that will be imbedded into the query
     * @return bool|array Returns bool(false) if the query failed, otherwise an array of results
     */
    public function prepared_select_query($query, array $args = [])
    {
      $results_array = [];

      if (stripos($query, 'select') === false)
      {
        return false;
      }

      try
      {
        $result = $this->prepared_query($query, $args);

        for ($i = 0; $i < $result->num_rows; $i++)
        {
          $row = $result->fetch_assoc();
          $results_array[] = $row;
        }
      }
      catch (Exception $e)
      {
        $_SESSION['alert'][] =
          '<h5>Wystąpił błąd #' . $e->getmessage() . '!</h5>' . PHP_EOL .
          '<p class="m-0">Nie udało się wczytać danych! Przepraszamy za niedogodności.</p>' . PHP_EOL;
        return false;
      }
      return $results_array;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
  }

  class Album
  {
    private $id;
    private $title;
    private $date;
    private $user_id;
    private $photos;
    private $author;
    private $unverified_photos_count;

		function __construct($id, $title, $date, $user_id)
		{
			$this->id = $id;
      $this->title = sanitize_text($title);
      $this->date = new DateTime($date);
      $this->user_id = $user_id;
      $this->photos = [];
      $this->author = User::unknown_user();
      $this->unverified_photos_count = 0;
		}

    public function &__get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    public function __tostring()
    {
      return 'album_' . $this->id;
    }

    public static function compare_title($album1, $album2)
    {
      return strcmp($album1->title, $album2->title);
    }

    public static function compare_date_asc($album1, $album2)
    {
      if ($album1->date < $album2->date)
      {
        return 1;
      }
      else if ($album1->date > $album2->date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    public static function compare_date_desc($album1, $album2)
    {
      if ($album1->date > $album2->date)
      {
        return 1;
      }
      else if ($album1->date < $album2->date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    public static function compare_author($album1, $album2)
    {
      return strcmp($album1->author->login, $album2->author->login);
    }
  }

  class Comment
  {
    private $id;
    private $comment;
    private $date;
    private $verified;
    private $photo_id;
    private $user_id;
    private $author;

    public function __construct($id, $comment, $date, $verified, $photo_id, $user_id)
    {
      $this->id = $id;
      $this->comment = sanitize_text($comment);
      $this->date = new DateTime($date);
      $this->verified = $verified;
      $this->photo_id = $photo_id;
      $this->user_id = $user_id;
      $this->author = User::unknown_user();
    }

    public function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    public function __tostring()
    {
      return 'comment_' . $this->id;
    }

    public static function compare_date_asc($comment1, $comment2)
    {
      if ($comment1->date < $comment2->date)
      {
        return 1;
      }
      else if ($comment1->date > $comment2->date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    public static function compare_date_desc($comment1, $comment2)
    {
      if ($comment1->date > $comment2->date)
      {
        return 1;
      }
      else if ($comment1->date < $comment2->date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    public static function compare_author($comment1, $comment2)
    {
      return strcmp($comment1->author->login, $comment2->author->login);
    }
  }

  class Photo
  {
    private $id;
    private $description;
    private $date;
    private $verified;
    private $album_id;
    private $comments;

    public function __construct($id, $description, $date, $verified, $album_id)
    {
      $this->id = $id;
      $this->description = sanitize_text($description);
      $this->date = new DateTime($date);
      $this->verified = $verified;
      $this->album_id = $album_id;
      $this->comments = [];
    }

    public static function unknown_photo()
    {
      return new self(0, '', '', '', '', 0);
    }

    public function &__get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    public static function compare_date_asc($photo1, $photo2)
    {
      if ($photo1->date < $photo2->date)
      {
        return 1;
      }
      else if ($photo1->date > $photo2->date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    public static function compare_date_desc($photo1, $photo2)
    {
      if ($photo1->date > $photo2->date)
      {
        return 1;
      }
      else if ($photo1->date < $photo2->date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    public function get_path($suffix = null)
    {
      if (!is_numeric($this->id) || !is_numeric($this->album_id))
      {
        return null;
      }

      $allowed_photo_extensions = explode(',', ALLOWED_PHOTO_EXTENSIONS);
      $album_path = 'albums/album_' . $this->album_id . '/';
      $photo_name = 'photo_' . $this->id;
      $absolute_path = CONTENT_PATH . $album_path;

      if ($suffix != null)
      {
        $suffix = strtolower($suffix);
        switch ($suffix)
        {
          case 'thumbnail':
            $photo_with_suffix_name = $photo_name . '_' . $suffix;
          break;

          default:
            return null;
        }
        $absolute_path .= $photo_with_suffix_name;
      }
      else
      {
        $absolute_path .= $photo_name;
      }

      $photos = glob($absolute_path . '*');

      if (count($photos) == 0 && $suffix != null)
      {
        $absolute_path = CONTENT_PATH . $album_path . $photo_name;
        $photos = glob($absolute_path . '*');
        if (count($photos) == 0)
        {
          return null;
        }
      }

      for ($i = 0; $i < count($photos); $i++)
      {
        $photo_array = explode('.', $photos[$i]);
        $photo_name = basename($photos[$i]);
        $photo_ext = end($photo_array);

        if (in_array($photo_ext, $allowed_photo_extensions))
        {
          if (file_exists($photos[$i]))
          {
            return CONTENT_URL . $album_path . $photo_name;
          }
        }
      }
      return null;
    }
  }

  class User
  {
    private $id;
    private $login;
    private $email;
    private $registration_date;
    private $permissions;
    private $active;

    public function __construct($id, $login, $email, $registration_date, $permissions, $active)
		{
			$this->id = $id;
      $this->login = $login;
      $this->email = $email;
      $this->registration_date = new DateTime($registration_date);
      $this->permissions = $permissions;
      $this->active = $active;
    }

    public static function unknown_user()
    {
      return new self(0, 'Nieznany użytkownik', '', '', '', 0);
    }

    public function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    public function __tostring()
    {
      return $this->login;
    }

    public static function compare_login($user1, $user2)
    {
      return strcmp($user1->login, $user2->login);
    }

    public static function compare_date_asc($user1, $user2)
    {
      if ($user1->registration_date < $user2->registration_date)
      {
        return 1;
      }
      else if ($user1->registration_date > $user2->registration_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    public static function compare_date_desc($user1, $user2)
    {
      if ($user1->registration_date > $user2->registration_date)
      {
        return 1;
      }
      else if ($user1->registration_date < $user2->registration_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }
  }

  class Rating
  {
    private $rating;
    private $photo_id;
    private $user_id;

    public function __construct($rating, $photo_id, $user_id)
    {
      $this->rating = $rating;
      $this->photo_id = $photo_id;
      $this->user_id = $user_id;
    }

    public function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }
  }

  class RatingAverage
  {
    private $average;
    private $count;
    private $subject_id;

    public function __construct($average, $count, $subject_id)
    {
      $this->average = $average;
      $this->count = $count;
      $this->subject_id = $subject_id;
    }

    public function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }
  }
?>
