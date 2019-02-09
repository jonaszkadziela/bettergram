<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }

  if (isset($comment))
  {
    echo
      '<div class="media position-relative flex-column flex-md-row align-items-center align-items-md-start bg-white shadow-sm rounded p-1 mt-1">' . PHP_EOL .
        '<div class="w-64px h-64px border rounded-circle p-1 mb-1 m-md-0 mr-md-1">' . PHP_EOL .
          '<i class="fas fa-user fa-2x m-auto"></i>' . PHP_EOL .
        '</div>' . PHP_EOL .
        '<div class="media-body">' . PHP_EOL .
          '<p class="h5 text-center text-md-left font-weight-bold m-0">' . $comment->author . '</p>' . PHP_EOL .
          '<p class="text-center text-md-left my-0-5">' . nl2br($comment->comment) . '</p>' . PHP_EOL .
          '<small class="d-block text-center text-md-right text-muted">Dodano ' . $comment->date->format('d.m.Y') . ' o godzinie ' . $comment->date->format('G:i') . '</small>' . PHP_EOL;
    if (!$comment->verified)
    {
      echo
        '<button class="js-stop-propagation btn position-absolute position-top-right m-0-5 p-0-25" type="button" data-toggle="tooltip" data-html="true" ' .
        'title="Ten komentarz nie został jeszcze zaakceptowany, dlatego <u>nie jest widoczny</u> publicznie.">' . PHP_EOL .
          '<i class="fas fa-question-circle fa-lg fa-fw"></i>' . PHP_EOL .
        '</button>' . PHP_EOL;
    }
    echo
        '</div>' . PHP_EOL .
      '</div>' . PHP_EOL;
  }
  else
  {
    echo '<div class="p-0-5 m-0-5 border rounded">Nie udało się wyświetlić komentarza</div>';
  }
?>
