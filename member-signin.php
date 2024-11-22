<?php require 'index.php' ?>

<form method="post" class="m-auto pt-5">
    <!--Tonnam ถ้าได้ล็อคอินเข้าสู่ระบบ -->
    <?php
    if (isset($_SESSION['member_name'])) {  // Check if the user is logged in
        echo <<<HTML
        <h6 class="text-center text-info mb-3">For member</h6>
        <a href="cart.php" class="btn bt-sm btn-info d-block w-75 mb-2 mx-auto">Check cart and order</a>
        ...
        <a href="member-signout.php" class="btn bt-sm btn-danger d-block w-75 mb-2 mx-auto">Logout</a>
        HTML;

        include 'recently-viewed.php'; //ดูรายระเอียดที่เพจแสดงข้อมูลสินค้า
        include 'footer.php';
        exit('<br><br><br></form></body></html>');
    }

    // Tonnam ถ้าเป็นการโพสข้อมูลกลับขึ้นมา
    if ($_SERVER['REQUEST_METHOD'] === 'POST') // make sure login and pwd is filled 
    {
        // info retrieved
        $email = $_POST['email'];
        $password = $_POST['pswd'];
        $mysqli = new mysqli('localhost', 'root', '', 'project1');
        $sql = 'SELECT * FROM member WHERE email = ? AND password = ?';
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($sql);
        $stmt->bind_param('ss', $email, $pswd);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        if ($num_rows == 1) {
            $data = $result->fetch_object();
            $_SESSION['member_id'] = $data->id;
            $_SESSION['member_name'] = $data->firstname;
            $mysqli->close();
            echo '<script>location="member-sighin.php"</script>';
            exit();
        } else if ($num_rows == 0) {
            echo <<<HTML
                <div class="alert alert-danger mb-4" role="alert">
                    Email or Password is wrong
                    <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
                HTML;
        }
    }
    ?>

    <h6 class="text-center text-info mb-4">Sign In</h6>
    <input type="email" name="email" placeholder="Email" class="form-control form-control-sm mb-4" required>
    <input type="password" name="pswd" placeholder="Password" class="form-control form-control-sm mb-4" required>
    <button type="submit" class="btn btn-sm btn-primary d-block mx-auto mb-4 w-50">Login</button>
    <a href="member-signup.php" class="btn btn-sm btn-info d-block mx-auto w-50">Register</a>
</form>