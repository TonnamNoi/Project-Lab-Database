<?php
@session_start();
if (!isset($_SESSION['admin'])) {
      header('location: admin-signin.php');
      exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
      <?php require 'head.php'; ?>
      <style>
            /* General Styling */
            * {
                  font-size: 0.93rem;
            }

            html,
            body {
                  background: azure;
                  min-height: 100vh;
                  display: flex;
                  align-items: center;
                  justify-content: center;
            }

            /* Main container */
            div.main-container {
                  max-width: 680px;
                  min-width: 400px;
                  background-color: white;
                  padding: 20px;
                  border: 2px solid #0D1821;
                  border-radius: 10px;
                  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Table Styling */
            table {
                  width: 100%;
                  margin-top: 20px;
                  border-radius: 8px;
            }

            thead {
                  background-color: #17a2b8;
                  /* Navbar color */
                  color: white;
            }

            .thead-dark th {
                  background-color: #17a2b8;
                  /* Matching navbar */
                  color: white;
            }

            tbody tr:hover {
                  background-color: #f1f1f1;
            }

            .text-center {
                  text-align: center;
            }

            /* Heading Styling */
            h6 {
                  color: #17a2b8;
                  /* Navbar color */
                  font-size: 1.4rem;
            }

            /* Icon styling for order status */
            .text-danger {
                  color: #dc3545;
                  /* Red for error */
            }

            .text-success {
                  color: #28a745;
                  /* Green for success */
            }

            /* Pagination */
            .pagination a {
                  color: #17a2b8;
                  text-decoration: none;
                  padding: 8px 12px;
                  border-radius: 4px;
            }

            .pagination a:hover {
                  background-color: #f8f9fa;
            }
      </style>
</head>

<body class="pt-5">
      <?php require 'navbar.php'; ?>

      <div class="main-container mx-auto px-2 pt-4">
            <h6 class="text-info mb-4 text-center">Recent Order List</h6>

            <?php
            require 'lib/pagination-v2.class.php';
            $page = new PaginationV2();

            $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
            $sql = "SELECT * FROM orders ORDER BY id DESC";
            $result = $page->query($mysqli, $sql);

            if ($mysqli->error || $result->num_rows == 0) {
                  echo '<div class="text-center text-danger lead">Data not found</div>';
                  goto end_page;
            }

            echo <<<HTML
        <table class="table table-striped table-sm table-bordered mt-4 m-auto">
        <thead class="thead-dark">
        <tr class="text-center">
            <th>Date</th><th>Buyer</th><th>Total value</th><th>Payment methods</th><th>Shipping</th><th></th>
        </tr>
        </thead>
        <tbody>
        HTML;

            while ($order = $result->fetch_object()) {
                  $order_id = $order->id;
                  $t = strtotime($order->order_date);
                  $d = date('d-m-Y', $t);
                  $n = $order->firstname . '&nbsp;&nbsp;' . $order->lastname;

                  $sql = "SELECT SUM((oi.quantity * p.price) + (oi.quantity * p.delivery_cost)) AS total
                      FROM orders_item oi 
                      LEFT JOIN product p
                      ON oi.product_id = p.id
                      WHERE oi.order_id = $order_id";

                  $result2 = $mysqli->query($sql);
                  $row = $result2->fetch_object();
                  $total = number_format($row->total);

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

                  $dvr = '<i class="far fa-times-circle text-danger"></i>';
                  if ($order->delivery == 'yes') {
                        $dvr = '<i class="far fa-check-circle text-success"></i>';
                  }

                  $a = "<a href=\"admin-order-detail.php?id=$order_id\" target=\"_blank\" class=\"text-info\">Detail</a>";

                  echo <<<ROW
            <tr class="text-center">
                <td>$d</td><td>$n</td><td>$total</td><td>$p</td><td>$dvr</td><td>$a</td>
            </tr>
            ROW;
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
      <?php require 'footer.php'; ?>
</body>

</html>