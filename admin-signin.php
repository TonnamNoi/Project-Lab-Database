<?php require 'index.php' ?>

<form method="post" class="m-auto pt-4">
    <?php
    //Tonnam ตรงนี้ POST ตัว username กับ password และเช็คด้วยว่า username เท่ากับ admin กับ password เท่ากับ 12345 รึเปล่า  
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $login = $_POST['login'];
        $pswd = $_POST['pswd'];
        if ($login ='admin' && $pswd =='12345') {
            $_SESSION['admin'] = '1';
        } else {
            echo <<<HTML
            <div class="alert alert-danger mb-4" role="alert">
                Username or Password is not correct
                <button class="close" data-dismiss="alert">&times;</button>
            </div>
            HTML;
        }
    }

    if (!isset($_SESSION['admin'])) {
        echo <<<HTML
        <div class="container">
            <h6 class="text-info text-center mt-5 mb-4">Welcome Admin</h6>
            <input type="text" name="login" placeholder="Username" class="form-control form-control-sm mb-3">
            <input type="password" name="pswd" placeholder="Password" class="form-control form-control-sm mb-4">
            <button class="btn btn-primary btn-sm d-block m-auto px-5">Submit</button>
        </div>
        HTML;
    } else { // if able to login show the option for admin
        echo <<<HTML
        <h6 class="text-info text-center mt-5 mb-3">for Admin...</h6>
        <a href="admin-add-product.php" class="btn btn-primary btn-sm d-block mx-auto mb-4 px-5">Add Product</a>
        <a href="admin-signout.php" class="btn btn-danger btn-sm d-block mx-auto px-5">Sign out</a>
        HTML;
    }
    ?>
</form>