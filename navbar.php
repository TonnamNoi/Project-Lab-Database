<?php
function is_active(...$file)
{
    $path = $_SERVER['PHP_SELF'];
    foreach ($file as $f) {
        if (stripos($path, $f) != null) {
            return ' active';
        }
    }
    return '';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-0 pr-2 justify-content-start">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="nav-brand textwarning">
        <i class="fa fa-shopping-bag fa-1x mr-2 d-none d-lg-inline"></i>
        <a href="index.php" style="text-decoration: none">
            <span class="navbar-brand text-warning">Simple Store</span>
        </a>
    </div>

    <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav">
            <li class="nav-item<?= is_active('/index.php') ?>">
                <a class="nav-link" href="index.php">First Page</a>
                ...
            </li>
        </ul>
    </div>

    <div class="col text-right my-2 pr-2">
        <?php
        @session_start();
        if (!isset($_SESSION['member_name'])) {
            echo '<a href="member-signin.php" class="btn btn-sm btn-danger">Login</a>';
        } else {
            $name = mb_substr($_SESSION['member_name'], 0, 16);
            echo <<<HTML
                <div class="dropdown d-inline">
                    <a href="#" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown">$name</a>
                    <div class="dropdown-menu mt-2 bg-light">
                        <a class="dropdown-item w-auto" href="cart.php">Check cart</a>
                        <a class="dropdown-item w-auto" href="member-order-list.php">History...</a>
                    </div>
                </div>
            HTML;
        }
        ?>
    </div>
    <!-- Tonnam หากมีการส่งคีเวิร์ดเข้ามา ให้นำไปเติมลงในอินพุตของฟอม -->
    <?php $q = $_GET['q'] ?? '';  ?>
    <form class="form-inline mr-2 my-2" action="search.php">
        <div class="input-group input-group-sm">
            <input type="text" name="q" class="form-control" placeholder="Search..." value="<?= $q ?>">
            <div class="input-group-append">
                <button class="btn btn-success">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <a href="cart.php" class="btn bg-primary btn-sm text-white my-2">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="badge badge-pill badge-danger"></span>
    </a>
</nav>

<script>
    function updateCart() {
        $.ajax({
            url: 'ajax-update-cart.php',
            success: (result) => {
                if (result = 0) {
                    result = '';
                }
                $('span.badge').text(result);
            }
        });
    }
    $(function() {
        updateCart();
    });
</script>