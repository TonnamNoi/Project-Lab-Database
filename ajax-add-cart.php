<?php
// @session_start();
// $msg = '';
// $contextual = '';

// if (!isset($_SESSION['member_id'])) {
//       $msg = 'Customers need to sign in before adding items to the cart';
//       $contextual = 'alert-danger';
//       goto end;
// }

// if (isset($_POST['pro_id'])) {
//       $mid = $_SESSION['member_id'];
//       $pid = $_POST['pro_id'];

      
//       $mysqli = new mysqli("localhost", "root", "root", "project1");
//       $sql = "REPLACE INTO cart VALUES (?, ?, ?, ?)";
//       $stmt = $mysqli->stmt_init();
//       $stmt->prepare($sql);
//       $stmt->bind_param("iiii", ...[0, $mid, $pid, 1]);
//       $stmt->execute();
//       $aff_row = $stmt->affected_rows;
//       if ($stmt->error || $aff_row == 0) {
//             $msg = 'Failed to add product to cart';
//             $contextual = 'alert-danger'; 
//       } else {
//             $msg = 'Product successfully added to cart';
//             $contextual = 'alert-success';
//       }
// }

// $stmt->close();
// $mysqli->close();

// end:
// echo <<<HTML
// <div class="alert $contextual mb-4" role="alert">
//       $msg
//       <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
// </div>             
// HTML;

@session_start();
$msg = '';
$contextual = '';

if (!isset($_SESSION['member_id'])) {
    $msg = 'Customers need to sign in before adding items to the cart';
    $contextual = 'alert-danger';
    goto end;
}

if (isset($_POST['pro_id'])) {
    $mid = $_SESSION['member_id'];
    $pid = $_POST['pro_id'];

    $mysqli = new mysqli("localhost", "root", "root", "project1");

    // Get the available stock for the product
    $sql = "SELECT remain FROM product WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

    if ($stock <= 0) {
        $msg = 'This product is out of stock';
        $contextual = 'alert-danger';
    } else {
        // Check if the product already exists in the cart
        $sql = "SELECT quantity FROM cart WHERE member_id = ? AND product_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $mid, $pid);
        $stmt->execute();
        $stmt->bind_result($current_qty);
        
        if ($stmt->fetch()) {
            // Product already in cart
            if ($current_qty >= $stock) {
                $msg = 'You have reached the maximum available quantity for this product';
                $contextual = 'alert-warning';
            } else {
                // Increase quantity if not at stock limit
                $new_qty = $current_qty + 1;
                $stmt->close();
                $sql = "UPDATE cart SET quantity = ? WHERE member_id = ? AND product_id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("iii", $new_qty, $mid, $pid);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    $msg = 'Quantity updated in cart';
                    $contextual = 'alert-success';
                } else {
                    $msg = 'Failed to update quantity in cart';
                    $contextual = 'alert-danger';
                }
            }
        } else {
            // Product not in cart, add as new entry if stock allows
            $stmt->close();
            $sql = "INSERT INTO cart (member_id, product_id, quantity) VALUES (?, ?, 1)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ii", $mid, $pid);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $msg = 'Product added to cart';
                $contextual = 'alert-success';
            } else {
                $msg = 'Failed to add product to cart';
                $contextual = 'alert-danger';
            }
        }
    }

    $stmt->close();
    $mysqli->close();
}

end:
echo <<<HTML
<div class="alert $contextual mb-4" role="alert">
    $msg
    <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
</div>
HTML;


?>

