<?php require 'index.php' ?>

<div class="main-container mx-auto px-3 pt-4">
    <?php
    $order_id = $_GET['id'] ?? 0;
    $mysqli = new mysqli('localhost', 'root', '', 'project1');
    if (isset($_POST['delete_id'])) {
        $del_id = $_POST['delete_id'];
        $sql = "DELETE FROM orders WHERE id = $del_id";
        $mysqli->query($sql);
        $sql = "DELETE FROM orders_item WHERE order_id = $del_id";
        $mysqli->query($sql);
    }

    if (isset($_POST['payment'])) {
        $sql = "UPDATE orders SET pay_status = 'paid' WHERE id = $order_id";
        $mysqli->query($sql);
    } else {
        $sql = "UPDATE orders SET pay_status = 'pending' WHERE id = $order_id";
        $mysqli->query($sql);
    }

    if (isset($_POST['delivery'])) {
        $sql = "UPDATE orders SET delivery = 'yes' WHERE id = $order_id";
        $mysqli->query($sql);
    } else {
        $sql = "UPDATE orders SET delivery = 'no' WHERE id = $order_id";
        $nysqli->query($sql);
    }
    $sql = "SELECT * FROM orders WHERE id = $order_id";
    $result_orders = $mysqli->query($sql);
    $order = $result_orders->fetch_object();
    $t = strtotime($order->order_date);
    $d = date("'d-m-Y");
    echo <<<HTML
    <h6 class="text-info mb-4 text-center">Detail of orders</h6>
    <div class="containter">
    <div class="row pb-3">
        <div class="col">
            $order->firstname $order->lastname <br>
            $order->address <br>
            $order->phone
        </div>
        <div class="col text-md-right">
            Order code #$order_id<br>Day $d
        </div>
    </div>
    
    HTML;
    // ขั้นตอนการแสดงรายการสินค้า มูลค่าการสั่งซื้อ สถานะการชำระเงินและจัดส่งก็ทำเช่นเดียวกับ เพจ member-order-detail.php (ผู้ซื้อ)
    $pay = '';
    $payment_chk = '';
    if ($order->pay_status == 'paid') {
        $pay = 'Paid';
        $payment_chk = ' checked';
    } else if ($order->pay_status == 'pending') {
        if ($order->payment == 'cod') {
            $pay = 'Pay on delivery';
        } else if (!empty($order->bank_transfer)) {
            $pay = 'Waiting to check';
        } else {
            $pay = 'overdue';
        }
    }

    $pay_notify = '';
    if (!empty($order->bank_transfer)) {
        $dt = date('d-m-Y', strtotime($order->date_transfer));
        $tm = date('H:i', strtotime($order->time_transfer));
        $pay_notify = <<<TEXT
        Bank$order->bank_transfer<br>$dt $tm
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
        <div class="col text-center">
        Payment method</div>
        <div class="col text-right">$pay</div>
    </div>

    <div class="row pt-3 pb-2">
        <div class="col text-center">
        Edit status <br><br><br>
        <form id= "form-delete" method="post">
            <input type="hidden" name="delete_id" value="$order->id">
            <a href="#" id="delete" title="Cancel order">
            <i class="fas fa-trash"></i></a>
        </form>            
        </div>
        <div class="col text-right">
            <form id="form-delete" method="post">
                <input type="checkbox" name="payment" $payment_chk>
                <span>Payment made</span>
                <button type="button" id="update" class="btn btn-sm btn-primary">Confirm</button>
            </form>
        </div>
    </div>
    HTML;
    echo '</div>'; //end grid container
    end_page:
    $mysqli->close();
    ?>