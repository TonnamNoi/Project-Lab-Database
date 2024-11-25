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
<body class="d-flex pt-5">
<?php require 'navbar.php'; ?> 
    
<form id="main-form" method="post" class="m-auto pt-4">
<?php      
if (isset($_SESSION['member_id'])) {
      echo <<<HTML
      <h6 class="mb-4 text-center text-info">For members</h6>
      <a href="cart.php" class="btn bt-sm btn-info d-block w-75 mb-2 mx-auto">Inspect cart and place order</a>
      <a href="member-order-list.php" class="btn bt-sm btn-secondary d-block w-75 mb-2 mx-auto">Order history and payment notice</a>
      <a href="#" class="btn bt-sm btn-success d-block w-75 mb-2 mx-auto">Favorite</a><br>
      <a href="#" class="btn bt-sm btn-secondary d-block w-75 mb-2 mx-auto">Update member details</a>
      <a href="member-signout.php" class="btn bt-sm btn-danger d-block w-75 mb-2 mx-auto">Sign out</a>
      HTML;

      include 'recently-viewed.php';
      echo '<br><br><br><br>';
      include 'footer.php';
      exit ('</form></body></html>');
}
// if POST retrive data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {           
      $email = $_POST['email'];
      $pswd = $_POST['pswd'];

      $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
      $sql = 'SELECT * FROM member 
                  WHERE email = ? AND password = ?';

      $stmt = $mysqli->stmt_init();
      $stmt->prepare($sql);
      $stmt->bind_param('ss', $email, $pswd);
      $stmt->execute();
      $result = $stmt->get_result();      
      $num_rows = $result->num_rows;
      if ($num_rows == 1) {
            $data = $result->fetch_object();
            $_SESSION['member_id'] = $data->id;
            $_SESSION['member_name'] = $data->firstname;
            $mysqli->close();
            echo "<script>location='member-signin.php'</script>";
            exit();
      } else if ($num_rows == 0) {
             echo <<<HTML
            <div class="alert alert-danger mb-4" role="alert">
                  Invalid email or password
                  <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>     
            HTML; 
      }           
}
?>
<h6 class="mb-3 text-center text-info">Member sign-in</h6>
<input type="email" name="email" placeholder="Email" class="form-control form-control-sm mb-3" required>
<input type="password" name="pswd" placeholder="Password"  class="form-control form-control-sm mb-4" required>   
<button type="submit" class="btn btn-sm btn-primary d-block mx-auto mb-4 w-50">Sign in</button>
<a href="member-signup.php" class="btn btn-sm btn-info d-block mx-auto w-50">Register</a>
</form>
    
<?php require 'footer.php'; ?> 
</body>
</html>