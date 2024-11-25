<?php @session_start(); ?>
<!DOCTYPE html>
<html>
<head>
      <?php require 'head.php'; ?>
      <style>
            html, body {
                  width: 100%;
                  height: 100%;
                  background: azure;
            }
          
            #main-form {
                  min-width: 270px;
                  max-width: 350px;
            }
      </style>
</head>
<body class="d-flex pt-5 px-3">
<?php require 'navbar.php'; ?> 

<form id="main-form" method="post" class="m-auto pt-4">
<?php     
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $login = $_POST['login'];
      $pswd = $_POST['pswd'];

      if ($login = 'admin' && $pswd == '12345') {
            $_SESSION['admin'] = '1';
      } else  {
             echo <<<HTML
            <div class="alert alert-danger mb-4" role="alert">
                  Invalid username or password
                  <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
            HTML; 
      }
}

if (!isset($_SESSION['admin'])) {
     echo <<<HTML
      <h6 class="text-info text-center mb-4">Admin username and password</h6>
      <input type="text" name="login" placeholder="Username" class="form-control form-control-sm mb-3">
      <input type="password" name="pswd" placeholder="Password"  class="form-control form-control-sm mb-4">   
      <button class="btn btn-primary btn-sm d-block m-auto px-5">Confirm</button>
     HTML;
} else {
      echo <<<HTML
      <h6 class="text-success text-center mb-3">For the website's administrator</h6>
      <a href="admin-order-list.php" class="btn btn-success btn-sm mb-2 d-block mx-auto px-5">Inspect order list</a>
      <a href="admin-add-product.php" class="btn btn-info btn-sm mb-5 d-block mx-auto px-5">Add Productา</a>
      <a href="admin-signout.php" class="btn btn-danger btn-sm mb-3 d-block mx-auto px-5">Sign out</a> 
      HTML;
}
?>      
<br><br><br>
</form>
    
<?php require 'footer.php'; ?> 
</body>
</html>