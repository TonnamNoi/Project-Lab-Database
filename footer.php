<footer class="text-center fixed-bottom text-white bg-dark py-1">

    <?php
    @session_start();
    if (isset($_SESSION['admin'])) {
        echo <<<HTML
        <div class="dropup d-inline">
            <button type="button" class="btn btn-info btn-sm dropdown-toggle small" data-toggle="dropdown">admin</button>
            <div class="dropdown-menu">
                <a href="admin-add-product.php" class="dropdown-item">Add</a>
                <a href="admin-order-list.php" class="dropdown-item">Order</a>
                <a href="admin-signout.php" class="dropdown-item">Logout</a>
            </div> <!-- dropdown -->
        </div> <!-- dropup -->
        HTML;
    } else {
        echo '[<a href="admin-signin.php" class="text-warning">admin</a>]';
    }
    ?>
</footer>