<form method="post" class="m-auto pt-4">
    <!-- Tonnam Part ทำ Post Register -->
    <?php

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect data
        $email = $_POST['email'];
        $password = $_POST['password'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        // ลืมทำ Insert และเชื่อมเข้า table ใน Database นะ



        
        //

        //ตรงนีต้องสร้างตัวแปลมาเพื่อเก็บค่าเพื่อเช็ค Server error ไหม
        //error = '';
        $error = $stmt->error;
        
        if ($error || $aff_rows != 1) {
            $msg = 'Have some problem with registration<br>Probably email is wrong';
            $contextual = 'alert-danger';
            echo <<<HTML
            <div class="alert $contextual alert-dismissable">
                $msg
                <button class="close" data-dismiss="alert" aria-hidden="true">&time;</button>
            </div>
            HTML;
        } else {
            $_SESSION['member_id'] = $insert_id;
            $_SESSION['member_name'] = $name;
            echo '<script>location="member-signin.php"</script>';
            exit;
        }
        // Check if passwords match
        if ($password != $password2) {
            $error = "Passwords do not match";
        }

        if (empty($error)) {
            echo '<h3 class="text-center text-success">Registration Success</h3>';
        } else {
            echo '<h3 class="text-center text-danger">Registration Failed</h3>';
            echo '<h3 class="text-center text-danger">' . $error . '</h3>';
        }
    }
    ?>

    <h6 class="text-center text-info mb-4">Register</h6>
    <input type="email" name="email" placeholder="Email" class="form-control form-control-sm mb-3" required>
    <div class="input-group input-group-sm my-2 mb-4">
        <input type="password" name="password" placeholder="Password" class="form-control" required>
        <input type="password" name="password2" placeholder="Please put the same password" class="form-control" required>
    </div>
    <div class="input-group input-group-sm mt-1 mb-4">
        <input type="text" name="firstname" placeholder="FirstName" class="form-control" required>
        <input type="text" name="lastname" placeholder="Surname" class="form-control" required>
    </div>
    <textarea name="address" rows="2" class="form-control form-control-sm mb-3" placeholder="Address" required></textarea>
    <input type="text" name="phone" placeholder="Phone" class="form-control form-control-sm mb-3" required>
    <button type="button" id="ok" class="btn btn-primary btn-sm d-block w-25 mx-auto mt-4">Confirm</button><br>
</form>