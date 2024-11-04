<form method="post" class="m-auto pt-5">
    <!--Tonnam ถ้าได้ล็อคอินเข้าสู่ระบบ -->
    <?php
    //if (?????) {
    echo <<<HTML
    <h6 class="text-center text-info mb-3">For member</h6>
    <a href="cart.php" class="btn bt-sm btn-info d-block w-75 mb-2 mx-auto">Check cart and order</a>
    ...
    <a href="member-signout.php" class="btn bt-sm btn-danger d-block w-75 mb-2 mx-auto">Logout</a>
    HTML;

    include 'recently-viewed.php'; //ดูรายระเอียดที่เพจแสดงข้อมูลสินค้า
    include 'footer.php';
    exit('<br><br><br></form></body></html>');
    //} 

    // Tonnam ถ้าเป็นการโพสข้อมูลกลับขึ้นมา
    // if (????) {

    // }
    ?>

    <h6 class="text-center text-info mb-4">Sign In</h6>
    <input type="email" name="email" placeholder="Email" class="form-control form-control-sm mb-4" required>
    <input type="password" name="pswd" placeholder="Password" class="form-control form-control-sm mb-4" required>
    <button type="submit" class="btn btn-sm btn-primary d-block mx-auto mb-4 w-50">Login</button>
    <a href="member-signup.php" class="btn btn-sm btn-info d-block mx-auto w-50">Register</a>
</form>

<form method="post" class="m-auto pt-4">
    <!-- Tonnam Post Login and password of admin by check by "admin" is id and password is "12345" -->
    <?php ?>
</form>