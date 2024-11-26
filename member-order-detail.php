<?php
@session_start();
if (!isset($_SESSION['member_id'])) {
      header('location: member-signin.php');
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
            html,
            body {
                  width: 100%;
                  height: 100%;
                  background: azure;
            }

            div.main-container {
                max-width: 600px;
                min-width: 450px;
            }
            
            img.product {
                max-width: 64px;
                max-height: 64px;
            } 
            
            div.row {
                  border-bottom: solid 1px lightgray;
            }
            
            #main-container button {
                  width: 120px;
            }
      </style>
      <script>
      $(function() {
            $('a#cancel').click(function() {
                  if (confirm('Confirm to cancel this order?')) {
                         $('#form-cancel').submit();
                  }
            });
      });
      </script>
</head>
<body class="pt-5">
<?php require 'navbar.php'; ?> 
    
<div class="main-container mx-auto px-3 pt-5">
<?php      
$order_id = $_GET['id'] ?? 0;
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');

// if cancellation ID is sent, use it to remove data from "orders" and "orders_item" tables
if (isset($_POST['cancel_id'])) {
      $cancel_id = $_POST['cancel_id'];
      $sql = "DELETE FROM orders WHERE id = $cancel_id";
      $mysqli->query($sql);

      $sql = "DELETE FROM orders_item WHERE order_id = $cancel_id";
      $mysqli->query($sql);                
}

// if payment info is sent, update "orders" table
if (isset($_POST['bank'])) {
      $bank = $_POST['bank'];
      $date = $_POST['date'];
      $time = $_POST['time'];

      $sql = "UPDATE orders 
                  SET pay_status = 'pending', bank_transfer = '$bank',
                         date_transfer = '$date', time_transfer = '$time'
                  WHERE id = $order_id";

      $mysqli->query($sql);
}

// fetch order info and display it
$sql = "SELECT * FROM orders WHERE id = $order_id";
$result_orders = $mysqli->query($sql);

if ($mysqli->error || $result_orders->num_rows == 0) {
      echo '<h6 class="text-center text-danger">No data found <br>or the order has been canceled</h6>';
      goto end_page;
}

$order = $result_orders->fetch_object();
$t = strtotime($order->order_date);
$d = date('d-m-Y', $t);       // change format to day-month-year

// show info as grid for bootstrap
 echo <<<HTML
<h6 class="text-info mb-4 text-center">Order and payment details</h6>
<div class="container">
<div class="row pb-3">
      <div class="col-12 col-md-6">Transaction ID #$order_id</div>
      <div class="col-12 col-md-6 text-md-right">Date $d</div>
</div>        
HTML;           

 // each item in the order requires data from "orders_item" and "product" tables to be complete
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

$result_items = $mysqli->query($sql);
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
                              Price: $price<br>
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


$bs_class = 'btn-primary';
$pay = '';
if ($order->pay_status == 'paid') {
      $pay = 'Paid';
      $bs_class = 'd-none';
} else if ($order->pay_status == 'pending') {
      if ($order->payment == 'cod') {
            $pay = 'Cash on delivery(COD)';
            $bs_class = 'd-none';
      } else if (!empty($order->bank_transfer)) {
            $pay = 'Pending verification';
      } else {
            $pay = 'Unpaid';
      }
}

$btn_notify =<<<BTN_NOTIFY
<button id="pay-notify" class="btn btn-sm $bs_class mb-4" data-toggle="modal" data-target="#modalNotify">Transfer notification</button>
BTN_NOTIFY;   

$btn_cancel ='';
if ($order->pay_status != 'paid' && $order->delivery != 'yes') {
      $btn_cancel =<<<CANCEL
      <form id="form-cancel" method="post">
            <input type="hidden" name="cancel_id" value="$order->id">
            <a href=# id="cancel" title="Cancel order"><i class="fas fa-trash"></i></a>
      </form>
      CANCEL;                     
}

$dvr = '';
if ($order->delivery == 'yes') {
      $dvr = '<div class="text-success">
                        <i class="fa fa-truck"></i><br>
                        Item has been shipped</div>';
}

echo <<<HTML
<div class="row pt-3 pb-2">
      <div class="col text-center">Payment method</div>
      <div class="col text-right">$pay</div>
</div>
<div class="row pt-3 pb-2">
      <div class="col text-center">
            <p class="text-info">Delivery address</p>
            $order->firstname  $order->lastname <br>
            $order->address <br>
            $order->phone               
      </div>
      <div class="col text-right">
            $btn_notify<br>
            $btn_cancel
            $dvr
      </div>
</div>                      
HTML;            

echo '</div>';  //end grid container
?>
<br><br><br><br>        
</div> <!-- main container -->

<!-- ฺbootstrap modal display payment form -->
<div id="modalNotify" class="modal fade">
<form id="modal-form" method="post">
<div class="modal-dialog">
      <div class="modal-content">
            <div class="modal-header">
                  <h6 class="modal-title">Payment confirmation</h6>
                  <button class="close" data-dismiss="modal">
                  <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                  <div class="form-group">
                        <label>Bank of transfer:</label>
                        <select name="bank" class="form-control form-control-sm w-auto">
                              <option value="BBL">Bangkok Bank</option>
                              <option value="KTB">Krungthai Bank</option>
                              <option value="KBANK">Kasikorn Bank</option>
                              <option value="SCB">Siam Commercial Bank</option>
                        </select>
                  </div>
                  <div class="form-group">
                      <label>Date and time of payment:</label><br>
                        <input type="date" name="date" class="form-control form-control-sm d-inline w-auto" required>
                        <input type="time" name="time" class="form-control form-control-sm d-inline w-auto" required>
                  </div>
            </div>
            <div class="modal-footer justify-content-center">
                  <button id="modal-btn-submit" class="btn btn-primary">Confirm</button>
            </div>
      </div>
</div>
</form>   
</div>   <!-- end modal -->

<?php
end_page:
$mysqli->close();

include 'footer.php'; 
?>
</body>
</html>
