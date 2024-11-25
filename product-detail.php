<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
      <?php require 'head.php'; ?>
      <link href="js/unite-gallery/css/unite-gallery.css" rel="stylesheet">
      <!-- <link href="js/unite-gallery/themes/compact/ug-theme-compact.css" rel="stylesheet"> -->
      <style>
            div#main-container {
                  max-width: 800px;
                  min-width: 400px;
            }
      </style>
      <script src="js/unite-gallery/themes/compact/ug-theme-compact.js"></script>
      <script src="js/unite-gallery/js/unitegallery.min.js"></script>
      <script src="js/loadingoverlay.min.js"></script>
      <script>
            //var ug;      
            $(function() {
                  var ug = $("#unite-gallery").unitegallery({
                        gallery_width: 400,
                        gallery_height: 300,
                        gallery_autoplay: true,
                        gallery_play_interval: 2000

                  });
                  //ug.play();       
                  ug.on("item_change", function() {
                        ug.resetZoom();
                  });

                  // when click "Add to Cart" button, read the product ID from the data-id attribute of the button and send it via AJAX to the ajax-add-cart.php page.
                  $('#add-cart').click(function() {
                        var id = $(this).attr('data-id');
                        // alert('The inputed value is ' + (id ? id : 'empty'));
                        $.ajax({
                              url: 'ajax-add-cart.php',
                              data: {
                                    'pro_id': id
                              },
                              type: 'post',
                              dataType: 'html',
                              // while sending the request, hide the background with a LoadingOverlay
                              beforeSend: () => {
                                    $.LoadingOverlay('show', {
                                          image: 'loading.png',
                                          background: 'rgba(200, 200, 200, 0.6)',
                                          text: 'Processing...',
                                          textResizeFactor: 0.15
                                    });
                              },
                              error: (xhr, textStatus) => alert(textStatus),
                              success: (result) => {
                                    $.LoadingOverlay("hide");
                                    $('#show-alert').html(result);

                                    updateCart();
                                    // this function is in header.php to count the number of items in the cart and display it on the cart button in the Navbar.
                              }
                        });
                  });
            });
      </script>
</head>

<body class="px-2 pt-5">
      <?php require 'navbar.php'; ?>

      <div id="main-container" class="mx-auto mt-5">
            <div id="show-alert"></div>
            <?php
            $product_id = $_GET['id'] ?? 0;

            $mysqli = new mysqli('localhost', 'root', 'root', 'project1');

            $sql = "SELECT * FROM product WHERE id = $product_id";
            $result = $mysqli->query($sql);

            if ($mysqli->error || $result->num_rows == 0) {
                  $mysqli->close();
                  echo '<h6 class="text-danger text-center mb-4">ไม่พบข้อมูล</h6>';
                  include 'recently-viewed.php';
                  include 'footer.php';
                  exit('</div></body></html>');
            }

            $p = $result->fetch_object();
            $img_files = explode(',', $p->img_files);
            $img_tags = '';
            foreach ($img_files as $img) {
                  $src = "product-images/$product_id/$img";
                  $img_tags .= "<img src=\"$src\" data-image=\"$src\">";
            }

            $r = ($p->remain > 0) ? 'Products in stock' : '<span class="text-danger">Product out of stock</span>';
            $cart_class = ($p->remain > 0) ? 'btn-primary' : 'btn-secondary disabled';
            $price = number_format($p->price);
            echo <<<HTML
<div class="container"> <!-- grid -->
<div class="row">
<div class="col-12 col-md-6 mt-3">
      <div id="unite-gallery" style="display: none">
            $img_tags
      </div>
</div>
<div class="col-12 col-md-6 d-flex flex-column justify-content-between">
      <div>
            <h6 class="text-success my-3">$p->name</h6>
            <p>Price: $price THB</p>
            $r
      </div>
      <div class="mt-2 mt-md-0">
            <a href="#" id="add-cart" class="btn btn-sm $cart_class mb-2" data-id="$p->id">
                  <i class="fa fa-cart-plus mr-1"></i> Add to cart
            </a><br>
            <a href="#" id="wishlist" class="btn btn-sm btn-info">
                  <i class="fa fa-heart mr-1"></i> Favorite
            </a>
      </div>
</div>                 
</div>     <!-- /row -->

<div class="row mt-2 mt-md-4">
      <div class="col-12">$p->detail</div>
</div>

</div>     <!-- /container -->            
HTML;

            // store some info about this product for displaying Recently Viewed
            // the info includes product image (use first image) and the name (first 15) then create a link and store it in the session.
            $url = $_SERVER['PHP_SELF'] .  '?' . $_SERVER['QUERY_STRING'];
            $img_src = $img_files[0];
            $n = mb_substr($p->name, 0, 15);
            $link = <<<LINK
<div>       
<a href="$url">
      <img src="product-images/$product_id/$img_src" style="max-width:60px;max-height:60px">
</a>
</div>
<div class="text-info mt-2 small">$n</div>
LINK;
            // check if the session has been created to store the data if not create an empty array for it
            if (!isset($_SESSION['recently_viewed'])) {
                  $_SESSION['recently_viewed'] = [];
            }
            // Check if the link for this product has already been stored in the session to prevent duplicate data
            // if not, add it as first item in the array.
            if (!in_array($link, $_SESSION['recently_viewed'])) {
                  array_unshift($_SESSION['recently_viewed'], $link);
            }
            // display the previously viewed items
            include 'recently-viewed.php';
            $mysqli->close()
            ?>
      </div>

      <br><br><br><br>
      <?php require 'footer.php'; ?>
</body>

</html>