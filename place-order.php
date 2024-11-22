<?php

$mid = $_SESSION['member_id'];

// retrieve data from the submitted form with some predefined values
// store data in "orders" table
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');
$sql = "INSERT INTO orders
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->stmt_init();
$stmt->prepare($sql);
$now = strtotime('now');
$d = date('Y-m-d', $now); // Order date(Present)
$p = [0, $mid, $_POST['firstname'], $_POST['lastname'], $_POST['address'], 
    $_POST['phone'], $_POST['payment'], 'pending', $d, '', '', '', 'no'];

$stmt->bind_param('iisssssssssss', ...$p);
$stmt->execute();

// retrieve order id to store it in the "orders_item" table.
$order_id = $stmt->insert_id;
$stmt->close();

// retrieve data from "cart" table for the items added to the cart by that customer
$sql = "SELECT * FROM cart WHERE member_id = $mid";
$result = $mysqli->query($sql);

// store each product info in the "orders_item" table.
while ($cart = $result->fetch_object()) {
    $pid = $cart->product_id;
    $q = $cart->quantity;
    $sql = "INSERT INTO orders_item VALUES (0, $order_id, $pid, $q)";
    $mysqli->query($sql);
}

// remove item added to the cart by that customer from the "cart" table.
$sql = "DELETE FROM cart WHERE member_id = $mid";
$mysqli->query($sql);
$mysqli->close();

?>

<h6 class="text-info text-center my-4">Order Completed</h6>
<p class="">Reviewing information about your order...</p>
<div class="mt-4 mb-3 text-center">Thank you for purchasing with us...</div>
<div class="text-center mt-4">
    <a href="index.php" class="btn btn-primary btn-sm px-4">Continue to shop</a>
</div>
</div> <!-- main container -->