<?php
@session_start();
if (!isset($_SESSION['member_id'])) {
      header('location: member-signin.php');
      exit;
} else if ($_SERVER['REQUEST_METHOD'] != 'POST') {
      header('location: cart.php');
      exit;
}
?>
<!doctype html>
<html>

<head>
      <?php require 'head.php'; ?>
      <style>
            html,
            body {
                  width: 100%;
                  height: 100%;
                  background: azure;
            }

            #main-form {
                  min-width: 400px;

            }
      </style>
      <script>
            $(function() {
                  $('button.placeorder').click(function() {
                        if (confirm('Confirm your purchase?')) {
                              $('#main-form').submit();
                        }
                  })
            });
      </script>
</head>

<body class="d-flex pt-5 px-3">

      <form method="post" id="main-form" action="place-order.php" class="m-auto">
            <h6 class="text-success text-center" style="font-size: 1.5rem">Simple Store</h6>
            <hr>
            <h6 class="mb-5 text-center text-info">Payment methods and shipping details</h6>

<span class="mt-4 mb-2 d-block text-success">Payment methods</span>
<div class="custom-control custom-radio">
      <input type="radio" class="custom-control-input" 
            id="radio1" name="payment" value="cod" checked>
      <label class="custom-control-label" for="radio1">Cash on Delivery(COD)</label>
</div>
<div class="custom-control custom-radio">
      <input type="radio" class="custom-control-input" 
            id="radio2" name="payment" value="bank_transfer">
      <label class="custom-control-label" for="radio2">Transfer via bank/ATM</label>
</div>

<?php
// get customer's personal info provided during registration  
// display it on the form, in case the delivery address needs to be updated
$mid = $_SESSION['member_id'];
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');
$sql = "SELECT * FROM member WHERE id = $mid";
$result = $mysqli->query($sql);
$m = $result->fetch_object();
?>
<span class="mt-4 mb-3 d-block text-success">Shipping address (use the existing address or update it)</span>
<div class="input-group input-group-sm mb-2">
    <input type="text" name="firstname" placeholder="Firstname" class="form-control w-auto" value="<?= $m->firstname ?>" required>
      <input type="text" name="lastname" placeholder="Lastname" class="form-control w-auto" value="<?= $m->lastname ?>" required>
</div>
<textarea name="address" rows="3" class="form-control form-control-sm mb-2" placeholder="Address" required><?= $m->address ?></textarea>
<input type="text" name="phone" placeholder="Phone No."  class="form-control form-control-sm" value="<?= $m->phone ?>" required>

            <span class="mt-4 mb-2 d-block text-success">วิธีการชำระเงิน</span>
            <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input"
                        id="radio1" name="payment" value="cod" checked>
                  <label class="custom-control-label" for="radio1">ชำระเงินปลายทาง</label>
            </div>
            <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input"
                        id="radio2" name="payment" value="bank_transfer">
                  <label class="custom-control-label" for="radio2">โอนผ่านธนาคาร/ATM</label>
            </div>

            <div class="text-center mt-4">
                  <a href="index.php" class="btn btn-danger btn-sm px-4 mr-5">Cancel</a>
                  <button type="button" class="placeorder btn btn-primary btn-sm px-4">Complete order</button>
            </div>

            
            <br><br><br><br>
      </form>

</body>

</html>