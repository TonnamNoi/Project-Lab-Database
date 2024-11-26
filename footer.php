<!-- <footer class="text-center fixed-bottom text-white bg-dark py-1">

    <?php
    // @session_start();
    // if (isset($_SESSION['admin'])) {
    //     echo <<<HTML
    //     <div class="dropup d-inline">
    //         <button type="button" class="btn btn-info btn-sm dropdown-toggle small" data-toggle="dropdown">admin</button>
    //         <div class="dropdown-menu">
    //             <a href="admin-add-product.php" class="dropdown-item">Add Product</a>
    //             <a href="admin-order-list.php" class="dropdown-item">Inspect order list</a>
    //             <a href="admin-signout.php" class="dropdown-item">Sign out</a>
    //         </div> <!-- dropdown -->
    //     </div> <!-- dropup -->
    //     HTML;
    // } else {
    //     echo '[<a href="admin-signin.php" class="text-warning">admin</a>]';
    // }
    ?>
</footer> -->

<!-- Footer -->
<footer class="text-center fixed-bottom text-white py-2" style="background-color: #1B998B;">
    <?php
    @session_start();
    if (isset($_SESSION['admin'])) {
        echo <<<HTML
        <div class="dropup d-inline">
            <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" style="background-color: #C5D86D; color: black;">
                  Admin
            </button>
            <div class="dropdown-menu">
                <a href="admin-add-product.php" class="dropdown-item">Add Product</a>
                <a href="admin-order-list.php" class="dropdown-item">Inspect order list</a>
                <a href="admin-signout.php" class="dropdown-item">Sign out</a>
            </div>
        </div>
        HTML;
    } else {
        echo '<a href="admin-signin.php" style="font-weight: bold; color: black;">Admin</a>';
    }
    ?>
</footer>