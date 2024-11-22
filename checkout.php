<?php
@session_start();

if (!isset($_SESSION['member_id'])) {
    header('location: member-signin.php');
    exit();
} else if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('location: cart.php');
    exit();
}
?>

<form method="post" id="main-form" action="place-order.php" class="m-auto">

    <h6 class="text-success text-center" style="font-size: 1.5rem">
        Simple Store
    </h6>
    <hr>
    <h6 class="mb-5 text-center text-info">Payment method and Address...</h6>
    <span class="mt-4 mb-2 d-block text-success">Payment Method</span>

    <div class="custom-control custom-radio">
        <input type="radio" class="custom-control-input" name="payment" value="cod" checked>
        <label class="custom-control-label">Cash on Delivery(COD)</label>
    </div>

    <div class="custom-control custom-radio">
        <input type="radio" class="custom-control-input" id="radio2" name="payment" value="bank_transfer">
        <label class="custom-control-label">Bank Transfer/ATM Transfer</label>
    </div>

    <?php
    // read the customer's info that was set when registration.
    // display on the form in case need to change address
    $mid = $_SESSION['member_id'];
    $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
    $sql = "SELECT * FROM member WHERE id = $mid";
    $result = $mysqli->query($sql);
    $m = $result->fetch_object();
    ?>

    <span class="mt-4 mb-3 d-block text-success">
        Shipping address (can use the existing info or edit it)
    </span>
    <div class="input-group input-group-sm mb-2">
        <input type="text" name="firstname" placeholder="Name" class="form-control" value="<?= $m->firstname ?>">
        <input type="text" name="lastname" placeholder="Last name" class="form-control" value="<?= $m->lastname ?>">
    </div>

    <textarea name="address" rows="3" class="form-control form-control-sm mb-2" placeholder="Address">
    <?= $m->address ?></textarea>
    <input type="text" name="phone" placeholder="Phone No." class="form-control form-control-sm" value="<?= $m->phone ?>">

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-danger btn-sm px-4 mr-5">
            Cancel
        </a>
        <button type="button" class="placeorder btn btn-primary btn-sm px-4">
            Order Product
        </button>
    </div>

</form>