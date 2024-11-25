<?php @session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <?php require 'head.php'; ?>
    <style>
        html, body {
            width: 100%;
            height: 100%;
            background: azure;
        }
    
        #main-form {
            min-width: 270px;
            max-width: 350px;
        }
    </style>
</head>
<body class="d-flex pt-5 px-3">
<?php require 'navbar.php'; ?> 

<form id="main-form" method="post" class="m-auto pt-4">
<?php     
// Database connection
$mysqli = new mysqli('localhost', 'root', 'root', 'project1');

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $pswd = $_POST['pswd'];

    // Check if the username exists in the database
    $sql = "SELECT * FROM admin WHERE admin_username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Decrypt the stored password
        $decrypted_password = openssl_decrypt($admin['admin_password'], 'aes-256-cbc', '12345', 0, base64_decode($admin['admin_iv']));
        
        // Verify the password
        if ($pswd === $decrypted_password) {
            $_SESSION['admin'] = '1';
        } else {
            echo <<<HTML
            <div class="alert alert-danger mb-4" role="alert">
                  Invalid Username or Password
                  <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
            HTML;
        }
    } else {
        echo <<<HTML
        <div class="alert alert-danger mb-4" role="alert">
              Invalid Username or Password
              <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
        HTML;
    }
}

if (!isset($_SESSION['admin'])) {
    echo <<<HTML
    <h6 class="text-info text-center mb-4">Admin username and password</h6>
    <input type="text" name="login" placeholder="Admin Username" class="form-control form-control-sm mb-3">
    <input type="password" name="pswd" placeholder="Password" class="form-control form-control-sm mb-4">   
    <button class="btn btn-primary btn-sm d-block m-auto px-5">Confirm</button>
    HTML;
} else {
    echo <<<HTML
    <h6 class="text-success text-center mb-3">For the website's administrator</h6>
    <a href="admin-order-list.php" class="btn btn-success btn-sm mb-2 d-block mx-auto px-5">Inspect order list</a>
    <a href="admin-add-product.php" class="btn btn-info btn-sm mb-5 d-block mx-auto px-5">Add Product</a>
    <a href="admin-signout.php" class="btn btn-danger btn-sm mb-3 d-block mx-auto px-5">Sign out</a> 
    HTML;
}
?>      
<br><br><br>
</form>
    
<?php require 'footer.php'; ?> 
</body>
</html>
