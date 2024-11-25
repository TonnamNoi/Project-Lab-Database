<?php
@session_start();
$msg = '';
$contextual = '';

if (!isset($_SESSION['member_id'])) {
      $msg = 'Customers need to sign in before adding items to the cart';
      $contextual = 'alert-danger';
      goto end;
}

if (isset($_POST['pro_id'])) {
      $mid = $_SESSION['member_id'];
      $pid = $_POST['pro_id'];

      $mysqli = new mysqli("localhost", "root", "root", "project1");
      $sql = "REPLACE INTO cart VALUES (?, ?, ?, ?)";
      $stmt = $mysqli->stmt_init();
      $stmt->prepare($sql);
      $stmt->bind_param("iiii", ...[0, $mid, $pid, 1]);
      $stmt->execute();
      $aff_row = $stmt->affected_rows;
      if ($stmt->error || $aff_row == 0) {
            $msg = 'Failed to add product to cart';
            $contextual = 'alert-danger'; 
      } else {
            $msg = 'Product successfully added to cart';
            $contextual = 'alert-success';
      }
}

$stmt->close();
$mysqli->close();

end:
echo <<<HTML
<div class="alert $contextual mb-4" role="alert">
      $msg
      <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
</div>             
HTML;
?>

