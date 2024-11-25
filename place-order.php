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
<!DOCTYPE html>
<html>

<head>
      <?php require 'head.php'; ?>
      <style>
            html,
            body {
                  width: 100%;
                  height: 100%;
            }

            #main-container {
                  max-width: 450px;
            }

            img.product {
                  max-width: 64px;
                  max-height: 64px;
            }

            div.row {
                  border-bottom: solid 1px darkgray;
            }

            input[type="number"] {
                  max-width: 50px;
            }
      </style>
      <script>
            $(function() {

            });
      </script>
</head>

<body class="d-flex pt-5">

      <div id="main-container" class="mt-5 m-auto p-3">
            <h6 class="text-success text-center" style="font-size: 1.5rem">Simple Store</h6>
            <hr>
            <?php
            $mid = $_SESSION['member_id'];
            $fname = $_POST['firstname'];
            $lname = $_POST['lastname'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            $pment = $_POST['payment'];
            $status = 'pending,paid';
            $delivery = 'no,yes';
            // take data submitted from the form, along with some predefined information and store it into the "orders" table
            $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
            $sql = 'INSERT INTO orders VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = $mysqli->stmt_init();
            $stmt->prepare($sql);
            $now = strtotime('now');
            $d = date('Y-m-d'); // order Date (Current)
            $param2 = [0, $mid, $fname, $lname, $address, $phone, $pment, 'pending', $d, '', $d, '', 'no'];

            $stmt->bind_param('iisssssssssss', ...$param2);
            $stmt->execute();
            // retrieve order ID to store it in the "orders_item" table
            $order_id = $stmt->insert_id;
            $stmt->close();

            // retrieve data from "cart" table for the items that customer has added to their cart
            $sql = "SELECT * FROM cart
                        WHERE member_id = $mid";

            $result = $mysqli->query($sql);
            // store data of each product item in the "orders_item" table.
            while ($cart = $result->fetch_object()) {
                  $pid = $cart->product_id;
                  $q = $cart->quantity;
                  $sql = "INSERT INTO orders_item VALUES 
                              (0, $order_id, $pid, $q)";

                  $mysqli->query($sql);
            }
            // remove items that the customer has added to the cart from the "cart" table.
            $sql = "DELETE FROM cart WHERE member_id = $mid";
            $mysqli->query($sql);
            $mysqli->close();

            ?>

            <h6 class="text-info text-center my-4">การสั่งซื้อเสร็จเรียบร้อย</h6>
            <p class="">
                  การตรวจสอบข้อมูลต่างๆ เกี่ยวกับการสั่งซื้อสินค้าของท่าน
                  เช่น แจ้งโอนเงิน, สถานะการโอนเงิน หรือการจัดส่ง
                  โดยล็อกอินเข้าสู่ระบบแล้วเลือกที่เมนู "ประวัติการสั่งซื้อและแจ้งชำระเงิน"
            </p>
            <div class="mt-4 mb-3 text-center">ขอขอบพระคุณที่สั่งซื้อสินค้าจากเรา</div>
            <div class="text-center mt-4">
                  <a href="index.php" class="btn btn-primary btn-sm px-4">กลับไป Shopping ต่อ</a>
            </div>

            <br><br><br><br>
      </div> <!-- main-container -->

</body>

</html>