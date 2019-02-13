<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  $comment_container_class = isset($comment_container_class) ? $comment_container_class : 'media position-relative bg-white shadow-sm rounded p-1';

  if (isset($comment))
  {
    echo
      '<div class="' . $comment_container_class . '">' . PHP_EOL .
        '<div class="d-flex w-100 flex-column flex-md-row align-items-center align-items-md-start">' . PHP_EOL .
          '<div class="js-spinner-container w-64px h-64px position-relative flex-shrink-0 mb-1 m-md-0 mr-md-1">' . PHP_EOL .
            '<div class="js-spinner d-flex justify-content-center align-items-center overlay text-light bg-dark rounded-circle">' . PHP_EOL .
              '<i class="fas fa-spinner fa-2x fa-spin"></i>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '<img class="w-100 h-100 border rounded-circle" src="#" data-src="' . get_gravatar_url($comment->author->email) . '" alt="#">' . PHP_EOL .
          '</div>' . PHP_EOL .
          '<div class="media-body flex-fill w-100">' . PHP_EOL .
            '<p class="h5 text-center text-md-left font-weight-bold m-0">' . $comment->author . '</p>' . PHP_EOL .
            '<div class="js-truncate text-center text-md-left my-0-5">';
    if ($comment->comment != truncate($comment->comment, 200))
    {
      echo
              '<div class="js-truncate-show-less collapse show">' . nl2br(truncate($comment->comment, 200), false) . '</div>' . PHP_EOL .
              '<div class="js-truncate-show-more collapse">' . nl2br($comment->comment, false) . '</div>' . PHP_EOL .
              '<button class="js-prevent-default js-truncate-button js-swap-text btn btn--clean font-weight-bold my-0-5 mb-md-0" data-swap-text="Pokaż mniej <i class=\'fas fa-angle-up\'></i>">Pokaż więcej <i class="fas fa-angle-down"></i></button>' . PHP_EOL;
    }
    else
    {
      echo nl2br($comment->comment);
    }
    echo
            '</div>' . PHP_EOL .
            '<small class="d-block text-center text-md-right text-muted">Dodano ' . $comment->date->format('d.m.Y') . ' o godzinie ' . $comment->date->format('G:i') . '</small>' . PHP_EOL;
    if (!$comment->verified)
    {
      echo
            '<button class="js-prevent-default btn btn--clean position-absolute position-top-right m-0-5 p-0-25" type="button" data-toggle="tooltip" data-html="true" data-trigger="manual" ' .
            'title="Ten komentarz nie został jeszcze zaakceptowany, dlatego <u>nie jest widoczny</u> publicznie.">' . PHP_EOL .
              '<i class="fas fa-question-circle fa-lg fa-fw"></i>' . PHP_EOL .
            '</button>' . PHP_EOL;
    }
    echo
            '</div>' . PHP_EOL .
          '</div>' . PHP_EOL .
        '</div>' . PHP_EOL;
  }
  else
  {
    echo '<div class="p-0-5 m-0-5 border rounded">Nie udało się wyświetlić komentarza</div>';
  }
?>
