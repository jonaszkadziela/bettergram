<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $pagination_errors = [];
  if (empty($page))
  {
    $pagination_errors[] = 'Nie zdefiniowano numeru strony!';
  }
  if (empty($page_count))
  {
    $pagination_errors[] = 'Nie zdefiniowano ilości stron!';
  }

  if (count($pagination_errors) == 0)
  {
    echo
      '<nav>' . PHP_EOL .
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
            '<a class="page-link" href="' . ROOT_URL . modify_get_parameters(['p' => 1]) . '">&laquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL .
          '<li class="page-item">' . PHP_EOL .
            '<a class="page-link" href="' . ROOT_URL . modify_get_parameters(['p' => $page - 1]) . '">&lsaquo;</a>' . PHP_EOL .
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
            ($page == $i ? '<span class="page-link">' . $i . '</span>' : '<a class="page-link" href="' . ROOT_URL . modify_get_parameters(['p' => $i]) . '">' . $i . '</a>') . PHP_EOL .
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
            '<a class="page-link" href="' . ROOT_URL . modify_get_parameters(['p' => $page + 1]) . '">&rsaquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL .
          '<li class="page-item">' . PHP_EOL .
            '<a class="page-link" href="' . ROOT_URL . modify_get_parameters(['p' => $page_count]) . '">&raquo;</a>' . PHP_EOL .
          '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</nav>' . PHP_EOL;
  }

  if (count($pagination_errors) > 0)
  {
    echo
      '<div class="p-1 mt-1 border rounded">' . PHP_EOL .
        '<h5>Nie można wyświetlić paginacji, gdyż:</h5>' . PHP_EOL .
        '<ul class="list-unstyled m-0">' . PHP_EOL;
    for ($i = 0; $i < count($pagination_errors); $i++)
    {
      echo '<li>' . $pagination_errors[$i] . '</li>' . PHP_EOL;
    }
    echo
        '</ul>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
?>
