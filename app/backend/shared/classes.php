<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  class User
  {
    private $id;
    private $login;
    private $email;
    private $sign_up_date;
    private $permissions;
    private $active;

		function __construct($id, $login, $email, $sign_up_date, $permissions, $active)
		{
			$this->id = $id;
      $this->login = $login;
      $this->email = $email;
      $this->sign_up_date = $sign_up_date;
      $this->permissions = $permissions;
      $this->active = $active;
    }

    static function unknown_user()
    {
      return new self(0, 'Nieznany użytkownik', '', '', '', 0);
    }

    function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    function __tostring()
    {
      return $this->login;
    }

    function debug()
    {
      console_log('=== User ===');
      console_log('id: ' . $this->id);
      console_log('login: ' . $this->login);
      console_log('email: ' . $this->email);
      console_log('sign_up_date: ' . $this->sign_up_date);
      console_log('permissions: ' . $this->permissions);
      console_log('active: ' . $this->active);
    }
  }

  class Photo
  {
    private $id;
    private $description;
    private $date;
    private $verified;
    private $album_id;

    function __construct($id, $description, $date, $verified, $album_id)
    {
      $this->id = $id;
      $this->description = $description;
      $this->date = $date;
      $this->verified = $verified;
      $this->album_id = $album_id;
    }

    static function unknown_photo()
    {
      return new self(0, '', '', '', '', 0);
    }

    function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    static function compare_date_asc($photo1, $photo2)
    {
      $photo1_date = date($photo1->date);
      $photo2_date = date($photo2->date);
      if ($photo1_date > $photo2_date)
      {
        return 1;
      }
      else if ($photo1_date < $photo2_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    static function compare_date_desc($photo1, $photo2)
    {
      $photo1_date = date($photo1->date);
      $photo2_date = date($photo2->date);
      if ($photo1_date < $photo2_date)
      {
        return 1;
      }
      else if ($photo1_date > $photo2_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    function get_path($suffix = null)
    {
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

    function debug()
    {
      console_log('=== Photo ===');
      console_log('id: ' . $this->id);
      console_log('description: ' . $this->description);
      console_log('date: ' . $this->date);
      console_log('verified: ' . $this->verified);
      console_log('album_id: ' . $this->album_id);
    }
  }

  class Rating
  {
    private $rating;
    private $photo_id;
    private $user_id;

    function __construct($rating, $photo_id, $user_id)
    {
      $this->rating = $rating;
      $this->photo_id = $photo_id;
      $this->user_id = $user_id;
    }

    function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    function debug()
    {
      console_log('=== Rating ===');
      console_log('rating: ' . $this->rating);
      console_log('photo_id: ' . $this->photo_id);
      console_log('user_id: ' . $this->user_id);
    }
  }

  class Comment
  {
    private $comment;
    private $date;
    private $verified;
    private $photo_id;
    private $user_id;
    private $author;

    function __construct($comment, $date, $verified, $photo_id, $user_id)
    {
      $this->comment = $comment;
      $this->date = $date;
      $this->verified = $verified;
      $this->photo_id = $photo_id;
      $this->user_id = $user_id;
      $this->author = User::unknown_user();
    }

    function __get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    function __tostring()
    {
      return 'comment_' . $this->photo_id . '_' . $this->user_id . '_' . str_replace(' ', '_', $this->date);
    }

    static function compare_date_asc($comment1, $comment2)
    {
      $comment1_date = date($comment1->date);
      $comment2_date = date($comment2->date);
      if ($comment1_date < $comment2_date)
      {
        return 1;
      }
      else if ($comment1_date > $comment2_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    static function compare_date_desc($comment1, $comment2)
    {
      $comment1_date = date($comment1->date);
      $comment2_date = date($comment2->date);
      if ($comment1_date > $comment2_date)
      {
        return 1;
      }
      else if ($comment1_date < $comment2_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    static function compare_author_login($comment1, $comment2)
    {
      return strcmp($comment1->author->login, $comment2->author->login);
    }

    function debug()
    {
      console_log('=== Comment ===');
      console_log('comment: ' . $this->comment);
      console_log('date: ' . $this->date);
      console_log('verified: ' . $this->verified);
      console_log('photo_id: ' . $this->photo_id);
      console_log('user_id: ' . $this->user_id);
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

		function __construct($id, $title, $date, $user_id)
		{
			$this->id = $id;
      $this->title = $title;
      $this->date = $date;
      $this->user_id = $user_id;
      $this->photos = array();
      $this->author = User::unknown_user();
		}

    function &__get($var)
		{
			return $this->$var;
		}

		function __set($var, $value)
		{
			$this->$var = $value;
    }

    function __tostring()
    {
      return 'album_' . $this->id;
    }

    static function compare_title($album1, $album2)
    {
      return strcmp($album1->title, $album2->title);
    }

    static function compare_date_asc($album1, $album2)
    {
      $album1_date = date($album1->date);
      $album2_date = date($album2->date);
      if ($album1_date < $album2_date)
      {
        return 1;
      }
      else if ($album1_date > $album2_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    static function compare_date_desc($album1, $album2)
    {
      $album1_date = date($album1->date);
      $album2_date = date($album2->date);
      if ($album1_date > $album2_date)
      {
        return 1;
      }
      else if ($album1_date < $album2_date)
      {
        return -1;
      }
      else
      {
        return 0;
      }
    }

    static function compare_author_login($album1, $album2)
    {
      return strcmp($album1->author->login, $album2->author->login);
    }

    function debug()
    {
      console_log('=== Album ===');
      console_log('id: ' . $this->id);
      console_log('title: ' . $this->title);
      console_log('date: ' . $this->date);
      console_log('user_id: ' . $this->user_id);
    }
  }
?>
