<?php
// protect app, disable direct access
define('ALF', true);

class App
{
  protected function handle_get()
  {
    $method = "GET";
    $ses_3_and_5 = intval($_SESSION['ses_3_and_5']) ?? 0;

    require_once './view.php';
  }

  protected function handle_post()
  {
    $method = "POST";
    $number = $_POST['number'];
    $message = '';
    $ses_3_and_5 = intval($_SESSION['ses_3_and_5']) ?? 0;

    if ($number != '') {
      if ($ses_3_and_5 >= 5) {
        $message = 'Jumlah telah melewati batas (5x)';
      } else {
        // kelipatan 3
        if ($number % 3 == 0) $message = $ses_3_and_5 > 2 ? 'Belanja pangan' : 'Pasar 20';
        // kelipatan 5
        if ($number % 5 == 0) $message = $ses_3_and_5 > 2 ? 'Pasar 20' : 'Belanja pangan';
        // kelipatan 3 & 5
        if ($number % 3 == 0 && $number % 5 == 0) {
          // set session baru
          $ses_3_and_5++;
          $_SESSION['ses_3_and_5'] = $ses_3_and_5;
          $message = 'Pasar 20 Belanja Pangan';
        }
      }
    }

    require_once './view.php';
  }

  public function run()
  {
    session_start();
    $_method = $_SERVER['REQUEST_METHOD'];

    if ($_method == 'GET') {
      $this->handle_get();
    } else if ($_method == 'POST') {
      $this->handle_post();
    } else {
      echo "Method not allowed!\n";
    }
  }
}

$app = new App();

$app->run();
