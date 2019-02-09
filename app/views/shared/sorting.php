<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  if (isset($sorting_options) && is_array($sorting_options))
  {
    echo
      '<div class="row">' . PHP_EOL .
        '<div class="col-12 col-md-auto d-flex flex-column justify-content-center px-md-0-5 ml-auto">' . PHP_EOL .
          '<label class="m-md-0" for="select_sort">Sortuj według</label>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="col-12 col-md-6 px-md-0-5 mr-auto">' . PHP_EOL .
          '<select id="select_sort" class="js-select-links custom-select">' . PHP_EOL;
    for ($i = 0; $i < count($sorting_options); $i++)
    {
      $option = $sorting_options[$i];

      $result = '<option value="' . ROOT_URL . modify_get_parameters(['sort' => $option]) . '"' . ($sort == $option ? ' selected' : '') . '>';
      switch ($option)
      {
        case 'title':
          $result .= 'Tytułu albumu';
        break;

        case 'author':
          $result .= 'Nazwy autora';
        break;

        case 'login':
          $result .= 'Nazwy użytkownika';
        break;

        case 'date_asc':
          $result .= 'Daty (od najnowszych do najstarszych)';
        break;

        case 'date_desc':
          $result .= 'Daty (od najstarszych do najnowszych)';
        break;

        default:
          $result .= ucfirst($option);
      }

      echo $result . '</option>' . PHP_EOL;
    }
    echo
          '</select>' . PHP_EOL .
        '</div>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
