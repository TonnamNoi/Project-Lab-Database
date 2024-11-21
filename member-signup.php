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

        // Check if passwords match
        if ($password != $password2) {
            $error = "Passwords do not match";
        }

        if (empty($error)) {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        }

        // Database connection
        $mysqli = new mysqli('localhost', 'root', '', 'your_database_name'); // Modify your DB name

        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        // Prepare the SQL INSERT statement
        $stmt = $mysqli->prepare("INSERT INTO users (email, password, firstname, lastname, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $email, $hashed_password, $firstname, $lastname, $address, $phone);
        
        $error = $stmt->error;
        
        // Execute the statement
        if ($stmt->execute()) {
            // Registration successful
            $insert_id = $stmt->insert_id; // Get the inserted record's ID
            $_SESSION['member_id'] = $insert_id;
            $_SESSION['member_name'] = $firstname . ' ' . $lastname;

            // Redirect to login or another page after successful registration
            echo '<script>location="member-signin.php"</script>';
            exit;
        } else {
            // Error occurred during registration
            $msg = 'There was an issue with registration, please try again.';
            $contextual = 'alert-danger';
            echo <<<HTML
            <div class="alert $contextual alert-dismissable">
                $msg
                <button class="close" data-dismiss="alert" aria-hidden="true">&time;</button>
            </div>
            HTML;
        }

        // Close the statement and connection
        $stmt->close();
        $mysqli->close();
    } else {
        // Display error message if passwords do not match
        echo '<h3 class="text-center text-danger">Registration Failed</h3>';
        echo '<h3 class="text-center text-danger">' . $error . '</h3>';
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