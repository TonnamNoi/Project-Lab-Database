<div class="main-container mx-auto px-3 pt-5">
    <h6 class="text-info mb-4 text-center">Purchase history</h6>

    <?php
    // require 'lib/pagination-v2.class.php';
    // $page = new PaginationV2();
    $mid = $_SESSION['member_id'];
    $mysqli = new mysqli('localhost', 'root', 'root', 'project1');

    // retrieve all items ordered by that customer (one item per order per transaction)
    $sql = "SELECT * FROM orders WHERE member_id = $mid ORDER BY id DESC";
    $result = $page->query($mysqli, $sql);

    if ($mysqli->error || $result->num_rows == 0) {
        echo '<h6>No data found</h6>';
        goto end_page;
    }

    // display as table
    echo <<<HTML
<table class="table table-striped table-sm table-bordered">
<thead class="thead-dark">
<tr>
    <th>Date</th><th>Total</th><th>Payment</th>
    <th>Shipping</th><th>&nbsp;</th>
</tr>
</thead>
<tbody>
HTML;

    while ($order = $result->fetch_object()) {
        $order_id = $order->id;
        $t = strtotime($order->order_date);
        $d = date('d-m-Y', $t);

        // calculate total value of each order, which need to be retrieved from the "orders_item" table 
        // these are the individual items ordered, but some information such as price and shipping cost is stored in the product table
        // so it is necessary to use a method to read data from multiple tables together
        $sql = "SELECT SUM((oi.quantity * COALESCE(p.price, 0)) + (oi.quantity * COALESCE(p.delivery_cost, 0))) AS total 
    FROM orders_item oi LEFT JOIN product p 
    ON oi.product_id = p.id WHERE oi.order_id = $order_id";

        $result2 = $mysqli->query($sql);
        $row = $result2->fetch_object();
        $total = number_format($row->$total);

        // display the payment status
        $p = '';
        if ($order->pay_status == 'paid') {
            $p = 'Paid';
        } else if ($order->pay_status == 'pending') {
            if ($order->payment == 'cod') {
                $p = 'Cash on Delivery(COD)';
            } else if ($order->payment == 'bank_transfer') {
                $p = 'Awaiting verification';
            } else {
                $p = 'Unpaid';
            }
        }

        $dvr = '<i class="far fa-times-circle"></i>';

        if ($order->delivery == 'yes') {
            $dvr = '<i class="far fa-check-circle"></i>';
        }

        $a = "<a href=\"member-order-detail.php?id=$order_id\">Details and Payment Notification</a>";
        echo "<tr class=\"text-center\">
        <td>$d</td><td>$total</td><td>$p</td>
        <td>$dvr</td><td>$a</td>
        </tr>";
    }

    echo '</tbody></table>';
    if ($page->total_pages() > 1) {
        $page->echo_pagenums_bootstrap();
    }
    // end_page;
    $mysqli->close();
    ?>
</div>