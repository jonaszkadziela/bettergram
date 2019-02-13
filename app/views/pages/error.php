<?php
  require_once str_replace('\\', '/', __DIR__) . '/../../../config.php';
  if (basename($_SERVER['PHP_SELF']) === basename(__FILE__))
  {
    header('Location: ' . ROOT_URL . '?error=405');
    exit();
  }
?>
<div class="d-flex flex-column min-vh-100 bg-img-1">
  <div class="container d-flex flex-fill flex-column my-3">
    <div class="row flex-fill">
      <div class="col-lg-6 m-auto">
        <div class="card p-1 shadow-lg">
          <div class="card-body text-center">
            <?php
              $error = isset($_GET['error']) ? $_GET['error'] : $_SERVER['REDIRECT_STATUS'];

              $error_explanations =
              [
                400 =>
                [
                  'title' => 'Bad Request',
                  'description' => 'Nieprawidłowe zapytanie - żądanie nie może być obsłużone przez serwer'
                ],
                401 =>
                [
                  'title' => 'Unauthorized',
                  'description' => 'Nieautoryzowany dostęp - żądanie zasobu, który wymaga uwierzytelnienia'
                ],
                403 =>
                [
                  'title' => 'Forbidden',
                  'description' => 'Zabroniony - serwer zrozumiał zapytanie, lecz konfiguracja bezpieczeństwa zabrania mu zwrócić żądany zasób'
                ],
                404 =>
                [
                  'title' => 'Not Found',
                  'description' => 'Nie znaleziono - serwer nie odnalazł żądanego zasobu'
                ],
                405 =>
                [
                  'title' => 'Method Not Allowed',
                  'description' => 'Niedozwolona metoda - metoda zawarta w żądaniu nie jest dozwolona dla wskazanego zasobu'
                ],
                408 =>
                [
                  'title' => 'Request Timeout',
                  'description' => 'Koniec czasu oczekiwania na żądanie - klient nie przesłał zapytania do serwera w określonym czasie'
                ],
                500 =>
                [
                  'title' => 'Internal Server Error',
                  'description' => 'Wewnętrzny błąd serwera - serwer napotkał niespodziewane trudności, które uniemożliwiły zrealizowanie żądania'
                ],
                502 =>
                [
                  'title' => 'Bad Gateway',
                  'description' => 'Błąd bramy - serwer spełniający rolę bramy lub pośrednika otrzymał niepoprawną odpowiedź od serwera nadrzędnego i nie jest w stanie zrealizować żądania klienta'
                ],
                504 =>
                [
                  'title' => 'Gateway Timeout',
                  'description' => 'Przekroczony czas bramy - serwer spełniający rolę bramy lub pośrednika nie otrzymał w ustalonym czasie odpowiedzi od wskazanego serwera'
                ]
              ];

              $valid_error = (is_numeric($error) && strlen($error) == 3) ? true : false;
              $title = '<h2 class="underline underline-primary mb-1-5">';
              $title .= 'Wystąpił ' . ($valid_error ? 'błąd #' . $error : 'nieznany błąd') . '!';
              echo $title . '</h2>' . PHP_EOL;

              if ($valid_error && array_key_exists($error, $error_explanations))
              {
                echo
                  '<div class="card mt-0-25 mb-1-5">' . PHP_EOL .
                    '<h5 class="card-header">Wyjaśnienie błędu</h5>' . PHP_EOL .
                    '<div class="card-body">' . PHP_EOL .
                      '<p class="m-0">' . PHP_EOL .
                        '<strong class="d-block">' . $error_explanations[$error]['title'] . '</strong>' . $error_explanations[$error]['description'] . '.' .
                      '</p>' . PHP_EOL .
                    '</div>' . PHP_EOL .
                  '</div>' . PHP_EOL;
              }
            ?>
            <p>Przepraszamy za niedogodności. Spróbuj ponownie później.</p>
            <p class="card-text">Jeśli ten błąd powtarza się wielokrotnie, skontaktuj się z administratorem.</p>
            <p class="mb-1-5"><i class="far fa-envelope"></i> Email: <a class="underline underline--narrow underline-primary underline-animation" href="mailto:<?php echo ADMIN_MAIL ?>"><?php echo ADMIN_MAIL ?></a></p>
            <a href="<?php echo ROOT_URL ?>" class="btn btn-lg btn-primary">Wróć na stronę główną</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
