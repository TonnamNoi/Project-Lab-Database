<?php require 'index.php' ?>

<?php 

$mysqli = new mysqli('localhost','root','','project1');
$sql = "SELECT * FROM orders ORDER BY id DESC";
$result = $page->query($musqli, $sql);

echo <<<HTML
<table class="table table-striped table-sm">

HTML;
while ($order = $result->fetch_object()) {
    $order_id = $order->id;
    $t = strtotime($order->order_date);
    $d = date('d-m-Y', $t);
    $n = $order->firstname . '&nbsp;&nbsp' . $order->lastname;
    $sql = "SELECT SUM((oi.quantity * p.price) + (oi.quantity * p.delivery_cost)) AS total FROM orders_item oi LEFT JOIN product p ON oi.product_id = p.id WHERE oi,order_id = $order_id";

    $result2 = $mysqli->query($sql);
    $row = $result->fetch_object();
    $total = number_format($row->total);
    $p = '';
    if ($order->pay_status == 'paid') {
        $p = 'Paid';
    } else if ($order->pay_status == 'pending') {
        if ($order->payment == 'cod') {
            $p = 'Pay on delivery';
        } else if ($order->pay_status == 'pending') {
            $p = 'Waiting to check';
        } else {
            $p = 'overdue';
        }
    }

    $dvr = '<i class="far fr-times-circle text-danger"></i>';
    if ($order->delivery == 'yes') {
        $dvr = '<i class="far fa-check-circle"></i>';
    }
    $a = "<a href=\"admin-order-detail.php?id=$order_id\">Detail of product</a>";
    echo <<<ROW
    <tr class="text-center">
        <td>$d</td><td>$n</td><td>$tital</td><td>$p</td>
        <td>$dvr</td><td>$a</td>
    </tr>
    ROW;
}
echo '</tbody></table>';

?>