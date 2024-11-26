<?php
@session_start();
if (!isset($_SESSION['member_id'])) {
      header('location: member-signin.php');
      exit;
}
?>
<!DOCTYPE html>
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

            div.main-container {
                max-width: 800px;
                min-width: 600px;
            }
      </style>
      <script>
      $(function() {

      });
      </script>
</head>
<body class="pt-5">
<?php require 'navbar.php'; ?> 
    
<div class="main-container mx-auto px-3 pt-5">
<h6 class="text-info mb-4 text-center">Purchase history</h6>
<?php      
require 'lib/pagination-v2.class.php';
$page = new PaginationV2();

$mid = $_SESSION['member_id'];
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');

// get all past order details for customer (one item per order)
$sql = "SELECT * FROM orders WHERE member_id = $mid ORDER BY id DESC";
$result = $page->query($mysqli, $sql);
if ($mysqli->error || $result->num_rows == 0) {
      echo '<h6 class="text-center text-danger">Data not found</h6>';
      goto end_page;
}

// show on the table
echo <<<HTML
<table class="table table-striped table-sm table-bordered mt-4 m-auto">
<thead class="thead-dark">
<tr class="text-center">
      <th>Date</th><th>Total value</th><th>Payment methods</th><th>Shipping</th><th></th>
</tr>
</thead>
<tbody>
HTML;

while ($order = $result->fetch_object()) {
      $order_id = $order->id;
      $t = strtotime($order->order_date);
      $d = date('d-m-Y', $t);

      // calculate total value of each order which needs to be retrieved from the "orders_item" table which contains each item purchased
      // some data such as price and shipping costs are stored in the "product table" so it need to read data from multiple tables together.
      $sql = "SELECT SUM((oi.quantity * p.price) + (oi.quantity * p.delivery_cost)) AS total
                  FROM orders_item oi 
                  LEFT JOIN product p
                  ON oi.product_id = p.id
                  WHERE oi.order_id = $order_id";

      $result2 = $mysqli->query($sql);
      $row = $result2->fetch_object();
      $total = number_format($row->total);

      // display payment status
      $p = '';
      if ($order->pay_status == 'paid') {
            $p = 'Paid';
      } else if ($order->pay_status == 'pending') {
            if ($order->payment == 'cod') {
                  $p = 'Cash on delivery(COD)';
            } else if (!empty($order->bank_transfer)) {
                  $p = 'Pending verification';
            } else {
                  $p = 'Unpaid';
            }
      }
      
      $dvr = '<i class="far fa-times-circle"></i>';
      if ($order->delivery == 'yes') {
            $dvr = '<i class="far fa-check-circle"></i>';
      }

      $a = "<a href=\"member-order-detail.php?id=$order_id\" target=\"_blank\">Payment details and notification</a>";
      echo "<tr class=\"text-center\"><td>$d</td><td>$total</td><td>$p</td><td>$dvr</td><td>$a</td></tr>";
}
echo '</tbody></table><br><br>';

if ($page->total_pages() > 1) {
      $page->echo_pagenums_bootstrap();
}

end_page:
$mysqli->close();
?>
<br><br><br><br>    
</div>
    
<?php include 'footer.php';  ?>
</body>
</html>
