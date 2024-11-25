<?php
@session_start();
if (!isset($_SESSION['admin'])) {
      header('location: admin-signin.php');
      exit;
}

if (!isset($_GET['id'])) {
       header('location: index.php');
      exit;           
}
?>
<!DOCTYPE html>
<html>
<head>
      <?php require 'head.php'; ?>
      <style>
            div.main-container {
                max-width: 680px;
                min-width: 400px;
            }
            
            img.product {
                max-width: 64px;
                max-height: 64px;
            } 
            
            div.row {
                  border-bottom: solid 1px lightgray;
            }
            
            input[type="number"] {
                  max-width: 50px;
            }
      </style>
      <script>
      $(function() {
            $('a#delete').click(function() {
                  if (confirm('Confirm cancellation of this order?')) {
                         $('#form-delete').submit();
                  }
            });

            $('button#update').click(function() {                  
                  if (confirm('Confirm status update for this order?')) {
                         $('#form-delivery').submit();
                  }
            });
      });
      </script>
</head>
<body class="pt-5">
<?php require 'navbar.php'; ?> 
    
<div class="main-container mx-auto px-3 pt-4">
<?php      
$order_id = $_GET['id'] ?? 0;
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');

if (isset($_POST['delete_id'])) {
      $del_id = $_POST['delete_id'];
      $sql = "DELETE FROM orders WHERE id = $del_id";
      $mysqli->query($sql);

      $sql = "DELETE FROM orders_item WHERE order_id = $del_id";
      $mysqli->query($sql);                
}

if (isset($_POST['payment'])) {
      $sql = "UPDATE orders SET pay_status = 'paid'
                  WHERE id = $order_id";

      $mysqli->query($sql);
} else {
      $sql = "UPDATE orders SET pay_status = 'pending'
                  WHERE id = $order_id";

      $mysqli->query($sql);
}

if (isset($_POST['delivery'])) {
      $sql = "UPDATE orders SET delivery = 'yes'
                  WHERE id = $order_id";

      $mysqli->query($sql);
} else {
       $sql = "UPDATE orders SET delivery = 'no'
                  WHERE id = $order_id";

      $mysqli->query($sql);     
}

$sql = "SELECT * FROM orders WHERE id = $order_id";
$result_orders = $mysqli->query($sql);

if ($mysqli->error || $result_orders->num_rows == 0) {
      echo '<h6 class="text-center text-danger">No data found <br>or the order has been canceled</h6>';
      goto end_page;
}

$order = $result_orders->fetch_object();
$t = strtotime($order->order_date);
$d = date('d-m-Y');            

 echo <<<HTML
<h6 class="text-info mb-4 text-center">Order details</h6>
<div class="container">
<div class="row pb-3">
      <div class="col">
            $order->firstname  $order->lastname <br>
            $order->address <br>
            $order->phone               
      </div>
      <div class="col text-md-right">
            Transaction ID #$order_id<br>
            Date $d
      </div>
</div>        
HTML;           

$sql =<<<SQL
      SELECT oi.*,  p.* 
      FROM orders_item oi
      LEFT JOIN  product p
      ON oi.product_id = p.id
      WHERE oi.order_id = $order_id
SQL;

$result_items = $mysqli->query($sql);

 if ($mysqli->error || $result_items->num_rows == 0) {
      echo '<h6 class="text-center text-danger">Data not found</h6>';
      goto end_page;
}

$grand_total = 0;
$dvr_cost = 0;

while ($data = $result_items->fetch_object()) {
      $n = $data->name;
      $files = explode(',', $data->img_files);
      $src = "product-images/$data->id/{$files[0]}";
      $price = number_format($data->price);
      $qty = number_format($data->quantity);
      $subtotal = $data->price * $data->quantity;
      $st = number_format($subtotal);
      $grand_total += $subtotal;   
      $dvr_cost += $data->delivery_cost * $data->quantity;
      
      echo <<<HTML
      <div class="row py-2">
            <div class="col-2 text-right"><img src="$src" class="product"></div>
             <div class="col-10">
                   <h6><a href="product-detail.php?id=$data->id">$n</a></h6>
                   <div class="d-flex justify-content-between align-items-center">
                        <div class="small">
                              Price: $price <br>
                              Quantity: $qty
                        </div>
                        <div>$st</div>
                  </div>
              </div>
       </div>
      HTML;
}

$gt = number_format($grand_total + $dvr_cost);
$f_dvr_cost = number_format($dvr_cost);

echo <<<HTML
<div class="row py-2">
      <div class="col text-center">Total delivery cost</div>
      <div class="col text-right">$f_dvr_cost</div>
</div>   
<div class="row pt-4 pb-2">
      <div class="col text-center">Grand total</div>
      <div class="col text-right">$gt</div>
</div>       
HTML;

$pay = '';
$payment_chk = '';
if ($order->pay_status == 'paid') {
      $pay = 'Pain';
      $payment_chk = 'checked';
} else if ($order->pay_status == 'pending') {
      if ($order->payment == 'cod') {
            $pay = 'Cash on delivery(COD)';
      } else if (!empty($order->bank_transfer)) {
            $pay = 'Pending verification';
      } else {
            $pay = 'Unpaid';
      }
}

$pay_notify = '';
if (!empty($order->bank_transfer)) {
      $dt = date('d-m-Y', strtotime($order->date_transfer));
      $tm = date('H:i', strtotime($order->time_transfer));

      $pay_notify =<<<TEXT
      Bank: $order->bank_transfer<br>
      $dt $tm
      TEXT;        
}

$dvr = '<i class="far fa-times-circle text-danger"></i>';
$dvr_chk = '';
 if ($order->delivery == 'yes') {
      $dvr = '<i class="far fa-check-circle text-success"></i>';
      $dvr_chk = ' checked';
}

echo <<<HTML
<div class="row pt-3 pb-2">
      <div class="col text-center">Payment methods</div>
      <div class="col text-right">$pay</div>
</div>
<div class="row pt-3 pb-2">
      <div class="col text-center">Payment notification</div>
      <div class="col text-right">$pay_notify</div>
</div>   

<div class="row py-2">
      <div class="col text-center">Shipping</div>
      <div class="col text-right">$dvr</div>
</div>  

<div class="row pt-3 pb-2">
      <div class="col text-center">
            Update status <br><br><br>
            <form id="form-delete" method="post">
                  <input type="hidden" name="delete_id" value="$order->id">
                  <a href=# id="delete" title="Cancel the order"><i class="fas fa-trash"></i></a>
            </form>
      </div>
      <div class="col text-right">
      <form id="form-delivery" method="post">
            <input type="checkbox" name="payment" $payment_chk>
            <span>Paid</span><br>
            <input type="checkbox" name="delivery" $dvr_chk>
            <span>Item shipped</span><br><br>
            <button type="button" id="update" class="btn btn-sm btn-primary">Confirm</button>
      </form>                        
      </div>
</div> 
HTML;            

echo '</div>';  //end grid container

end_page:
$mysqli->close();
?>
<br><br><br><br>
</div>      <!-- end main-container -->
    
<?php include 'footer.php';  ?>
</body>
</html>
