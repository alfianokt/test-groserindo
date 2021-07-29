<?php if (!defined('ALF')) die('Cant render page') ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<style>
  #app {
    display: flex;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
  }
</style>

<body>
  <div id="app">
    <div class="px-5">
      <h3 class="mb-3">Test Aplikasi Pertama</h3>
      <form action="" id="form" method="POST">
        <div class="mb-3">
          <label for="number" class="form-label">Masukkan Angka</label>
          <input type="number" id="number" name="number" class="form-control" value="<?= $number ?? '' ?>">
        </div>

        <div class="mb-3">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
      <?php if ($message) : ?>
        <div>
          <div class="alert alert-success" id="message">
            <?= $message ?>
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>
</body>

</html>