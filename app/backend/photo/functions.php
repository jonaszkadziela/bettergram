<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  function resize_photo($photo_path, $target_path, $target_width, $target_height, $upscale_smaller = false)
  {
    if (!file_exists($photo_path))
    {
      return false;
    }

    $photo_path_array = explode('/', str_replace('\\', '/', $photo_path));
    $photo_full_name = end($photo_path_array);
    $path = implode('/', array_diff($photo_path_array, [$photo_full_name]));

    $photo_full_name_array = explode('.', $photo_full_name);
    $photo_name = strtolower($photo_full_name_array[0]);
    $photo_extension = strtolower(end($photo_full_name_array));

    switch ($photo_extension)
    {
      case 'jpg':
      case 'jpeg':
        $old_photo = imagecreatefromjpeg($photo_path);
      break;

      case 'png':
        $old_photo = imagecreatefrompng($photo_path);
      break;

      case 'gif':
        $old_photo = imagecreatefromgif($photo_path);
      break;

      default:
        return false;
    }

    if (!$old_photo)
    {
      return false;
    }

    $old_width = imagesx($old_photo);
    $old_height = imagesy($old_photo);

    if (!$upscale_smaller)
    {
      if ($old_width < $target_width)
      {
        $target_width = $old_width;
      }
      if ($old_height < $target_height)
      {
        $target_height = $old_height;
      }
    }
    if ($old_width > $old_height)
    {
      $new_width = $target_width;
      $new_height = $old_height / $old_width * $target_width;
    }
    else if ($old_width < $old_height)
    {
      $new_width = $old_width / $old_height * $target_height;
      $new_height = $target_height;
    }
    else
    {
      $new_width = $target_width;
      $new_height = $target_height;
    }

    $new_photo = imagecreatetruecolor($new_width, $new_height);

    if (!$new_photo)
    {
      return false;
    }

    $white = imagecolorallocate($new_photo, 255, 255, 255);
    imagefill($new_photo, 0, 0, $white);
    imagecopyresampled($new_photo, $old_photo, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

    switch ($photo_extension)
    {
      case 'jpg':
      case 'jpeg':
        $result = imagejpeg($new_photo, $target_path, 80);
      break;

      case 'png':
        $result = imagepng($new_photo, $target_path, 8);
      break;

      case 'gif':
        $result = imagegif($new_photo, $target_path);
      break;

      default:
        return false;
    }

    imagedestroy($new_photo);
    imagedestroy($old_photo);

    return $result;
  }
?>
