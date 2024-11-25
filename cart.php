<?php
@session_start();
if (!isset($_SESSION['member_id'])) {
      header('location: member-signin.php');
      exit;
}
?>
<!DOCTYPE html>
<html>

<head>
      <?php require 'head.php'; ?>
      <style>
            div.main-container {
                  max-width: 600px;
                  min-width: 450px;
            }

            img.product {
                  max-width: 64px;
                  max-height: 64px;
            }

            div.row {
                  border-bottom: solid 1px lightgray;
            }

            input[type="number"] {
                  max-width: 50px;
            }
      </style>
      <script>
            $(function() {
                  $('a.delete').click(function() {
                        if (confirm('Are you sure you want to remove this item from the cart?')) {
                              var del_id = $(this).attr('data-id');
                              $('#delete-id').val(del_id);
                              $('#form-delete').submit();
                        }
                  });

                  // when click the 'Place Order' button.
                  $('a.checkout').click(function() {
                        $('#form-checkout').submit();
                  });
            });
      </script>
</head>

<body class="px-3 pt-5">
      <?php require 'navbar.php'; ?>

      <div class="main-container mx-auto pt-4">
            <form method="post" id="form-cart">
                  <?php
                  $mid = $_SESSION['member_id'];
                  $mysqli = new mysqli('localhost', 'root', 'root', 'project1');

                  // When delete ID is sent, apply it as a condition to remove data from "cart" table
                  if (isset($_POST['delete_id'])) {
                        $pid = $_POST['delete_id'];
                        $sql = "DELETE FROM cart WHERE member_id = $mid AND product_id = $pid";
                        $mysqli->query($sql);
                  }
                  // when quantity is given, update every entry with the form data
                  if (isset($_POST['qty'])) {
                        $count = count($_POST['qty']);
                        for ($i = 0; $i < $count; $i++) {
                              $qty = $_POST['qty'][$i];
                              $pid = $_POST['pid'][$i];
                              $sql = "UPDATE cart SET quantity = $qty
                        WHERE member_id = $mid AND product_id = $pid";

                              $mysqli->query($sql);
                        }
                  }

                  // fetch and display data from the "cart" and "product" tables
                  $sql = <<<SQL
      SELECT p.*,  c.quantity 
      FROM product p 
      LEFT JOIN  cart c
      ON p.id = c.product_id
      WHERE c.member_id = $mid
SQL;

                  $result = $mysqli->query($sql);
                  if ($mysqli->error || $result->num_rows == 0) {
                        echo '<h6 class="text-center text-danger">Cart is empty</h6>';
                        $mysqli->close();
                        include 'footer.php';
                        exit('</form></div></body></html>');
                  }

                  echo '<h6 class="text-info mb-4 text-center">Cart items</h6>';
                  echo '<div class="container">';

                  $grand_total = 0;
                  $dvr_cost = 0;
                  while ($p = $result->fetch_object()) {
                        $n = mb_substr($p->name, 0, 20);    // first 20 product name

                        $img_files = explode(',', $p->img_files);       // split the image and name
                        $src = "product-images/$p->id/{$img_files[0]}";

                        $price = number_format($p->price);
                        $subtotal = $p->price * $p->quantity;     // subtotal for each item
                        $st = number_format($subtotal);
                        $grand_total += $subtotal;                      // grand total of all items
                        $dvr_cost += $p->delivery_cost * $p->quantity;  // total shipping cost for all items

                        echo <<<HTML
      <div class="row py-2">
            <div class="col-2 text-right"><img src="$src" class="product"></div>
             <div class="col-10">
                   <h6><a href="product-detail.php?id=$p->id" target="_blank">$n</a></h6>
                   <div class="d-flex justify-content-between align-items-center">
                        <div class="small">
                              Price: $price<br>
                              Quantity: <input type="number" name="qty[]" class="" size="3" maxlength="3" min="1" max="$p->remain" value="$p->quantity">
                              <input type="hidden" name="pid[]" value="$p->id">
                              <div>
                                    <a href=# class="delete" data-id="$p->id">
                                          <i class="fas fa-trash"></i>
                                    </a>
                              </div>
                        </div>
                        <div class="">$st</div>
                  </div>      <!-- d-flex --> 
              </div>          <!-- col-10 -->
       </div>                 <!-- row -->
      HTML;
                  }

                  $f_dvr_cost = number_format($dvr_cost);

                  $grand_total += $dvr_cost;
                  $gt = number_format($grand_total);

                  echo <<<HTML
<div class="row py-3">
      <div class="col-7 col-md-9 text-center">
            Total delivery cost
      </div>
      <div class="col-5 col-md-3 text-right">$f_dvr_cost</div>
</div>
<div class="row py-3">
      <div class="col-7 col-md-9 text-center">
            Total amount
      </div>
      <div class="col-5 col-md-3 text-right">$gt</div>
</div>
<div class="row py-3">
      <div class="col-7 col-md-9 text-center">
            To update items quantity, click the <b>Recalculate</b> button to save changes
      </div>
      <div class="col-5 col-md-3 text-right">
            <button type="submit" class="btn btn-primary btn-sm">Recalculate</button>
      </div>
</div>
HTML;

                  echo '</div>';          //end grid container

                  $mysqli->close();
                  ?>

                  <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-sm btn-info mr-2 mr-md-5 px-md-5">&laquo; Add more product</a>
                        <a href="#" class="checkout btn btn-sm btn-success px-md-5">Purchase items &raquo;</a>
                  </div>

                  <br><br><br><br>
            </form>
      </div> <!-- main container -->

      <form id="form-delete" method="post">
            <input type="hidden" name="delete_id" id="delete-id" value="">
      </form>

      <form id="form-checkout" action="checkout.php" method="post"></form>

      <?php include 'footer.php';  ?>
</body>

</html>