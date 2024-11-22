<form method="post" class="m-auto pt-4">
    <!-- Tonnam Part ทำ Post Register -->
    <?php

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect data
        $email = $_POST['email'];
        $pswd = $_POST['password'];
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        // Database connection
        $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
        $sql = 'INSERT INTO member VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($sql);
        $p = [0, $email, $pswd, $fname, $lname, $address, $phone];
        $stmt->bind_param('issssss', ...$p);
        $stmt->execute();
        $err = $stmt->error;
        $aff_rows = $stmt->affected_rows;
        $insert_id = $mysqli->insert_id;
        $stmt->close();
        $mysqli->close();
        if ($err || $aff_rows != 1) {
            $msg = 'An error occurred while registering for membership.<br>The specified email address is already in use';
            $contextual = 'alert-danger';
            echo <<<HTML
            <div class="alert $contextual alert-dismissible">
                $msg
                <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
            HTML;
        } else {
            $_SESSION['member_id'] = $insert_id;
            $_SESSION['member_name'] = $name;
            echo '<script>location="member-signin.php"</script>';
            exit;
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