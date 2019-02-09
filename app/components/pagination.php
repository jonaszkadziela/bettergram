<?php
  require_once(str_replace('\\', '/', dirname(__FILE__)) . '/../../config.php');
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    $_SESSION['alert'] = '<strong>Błąd!</strong> Niewłaściwa metoda wywołania pliku!';
    header('Location: ' . ROOT_URL);
    exit();
  }

  $errors = array();
  if (empty($page))
  {
    $errors[] = 'Nie zdefiniowano numeru strony!';
  }
  if (empty($page_count))
  {
    $errors[] = 'Nie zdefiniowano ilości stron!';
  }

  if (count($errors) == 0)
  {
    echo
      '<nav class="mt-1-5">' . PHP_EOL .
        '<ul class="pagination flex-wrap justify-content-center m-0">' . PHP_EOL;
    if ($page <= 1)
    {
      echo
          '<li class="page-item disabled">' . PHP_EOL .
            '<a class="page-link" href="#" tabindex="-1">&laquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL .
          '<li class="page-item disabled">' . PHP_EOL .
            '<a class="page-link" href="#" tabindex="-1">&lsaquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL;
    }
    else
    {
      echo
          '<li class="page-item">' . PHP_EOL .
            '<a class="page-link" href="' . modify_url_parameters(array('page' => 1)) . '">&laquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL .
          '<li class="page-item">' . PHP_EOL .
            '<a class="page-link" href="' . modify_url_parameters(array('page' => $page - 1)) . '">&lsaquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL;
    }

    $first_page = $page - floor(PAGINATION_LENGTH / 2);
    $last_page = $page + floor(PAGINATION_LENGTH / 2);
    $current_pagination_length = min($page_count, $last_page) - max(1, $first_page) + 1;
    $more = PAGINATION_LENGTH - $current_pagination_length;

    $halfway = min($page_count, $page + PAGINATION_LENGTH) / 2;
    $more_front = $page > $halfway ? $more : 0;
    $more_back = $page <= $halfway ? $more : 0;

    for ($i = max(1, $first_page - $more_front); $i <= min($page_count, $last_page + $more_back); $i++)
    {
      echo
          '<li class="page-item ' . ($page == $i ? 'active' : '') . '">' . PHP_EOL .
            ($page == $i ? '<span class="page-link">' . $i . '</span>' : '<a class="page-link" href="' . modify_url_parameters(array('page' => $i)) . '">' . $i . '</a>') . PHP_EOL .
          '</li>' . PHP_EOL;
    }

    if ($page >= $page_count)
    {
      echo
          '<li class="page-item disabled">' . PHP_EOL .
            '<a class="page-link" href="#" tabindex="-1">&rsaquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL .
          '<li class="page-item disabled">' . PHP_EOL .
            '<a class="page-link" href="#" tabindex="-1">&raquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL;
    }
    else
    {
      echo
          '<li class="page-item">' . PHP_EOL .
            '<a class="page-link" href="' . modify_url_parameters(array('page' => $page + 1)) . '">&rsaquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL .
          '<li class="page-item">' . PHP_EOL .
            '<a class="page-link" href="' . modify_url_parameters(array('page' => $page_count)) . '">&raquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</nav>' . PHP_EOL;
  }

  if (count($errors) > 0)
  {
    $_SESSION['alert'] =
      '<h5 class="alert-heading">Nie można wyświetlić paginacji, gdyż:</h5>' . PHP_EOL .
      '<ul class="mb-0">' . PHP_EOL;
    for ($i = 0; $i < count($errors); $i++)
    {
      $_SESSION['alert'] .= '<li>' . $errors[$i] . '</li>' . PHP_EOL;
    }
    $_SESSION['alert'] .= '</ul>' . PHP_EOL;
  }
?>
