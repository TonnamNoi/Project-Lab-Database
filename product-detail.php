<?php require 'index.php' ?>
<script>
    $(function() {
        $("#unite0gallery").unitegallery({
            gallery_width: 400,
            gallery_height: 300
        });

        $('#add-cart').click(function() {
            var id = $(this).attr('data-id');
            $.ajax({
                url: 'ajax-add-cart.php',
                data: {
                    'pro_id': id
                },
                type: 'post',
                dataType: 'html',

                beforeSend: () => {
                    $.LoadingOvverlay('show', {
                        image: 'clock-loading.grif',
                        badckground: 'rgba(200, 200, 200, 0.6)',
                        text: 'processing...',
                        textResiazeFactor: 0.15
                    });
                },
                error: (xhr, textStatus) => alert(textStatus),
                success: (result) => {
                    $.LoadingOverlay("hide");
                    $('#show-alert').html(result);

                    // function to count product in cart
                    // show cart button on Navbar
                    updateCart();
                }
            });
        });
    });
</script>

<div id="main-container" class="mx-auto mt-5">
    <div id="show-alert"></div>
    <?php
    $product_id = $GET['id'] ?? 0;
    //Tonnam เช็ค สินค้า จาก table product ว่ามีอยุ่จริงไหม หมดยัง ถ้าหมดให้ปุ่มอยุ่สถานะคลิกไม่ได้ 
    $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
    $sql = "SELECT * FROM product WHERE id = $ product_id";
    $result = $mysqli->query($sql);

    if ($mysqli->error || $result->num_rows == 0) {
        $mysqli->close();
        echo '<h6 class="text-danger text-center mb-4">can not find...</h6>';
        include 'recently-viewed.php';
        include 'footer.php';
        exit('</div></body></html>');
    }

    $p = $result->fetch_object();
    $img_files = explode(',', $p->img_files);
    $img_tag = '';
    foreach ($img_files as $img) {
        $src = "product-images/$product_id/$img";
        $img_tags .= "<img src=\"$src\" data-image=\"$src\">";
    }

    $r = ($p->remain > 0) ? 'Have product' : '<span class="text-danger">Product out of stock</span>';

    // if have product button is normal else button can't
    $cart_class = ($p->remain > 0) ? 'btn btn-primary' : 'btn-secondary disabled';

    $price = number_format($p->price);
    echo <<<HTML
<div class="container"> <!--grid-->
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
            <a href=# id="add-cart" class="btn btn-sm <?php echo $cart_class; ?> mb-2" data-id="<?php echo $p->id; ?>">
                <i class="fa fa-cart-plus"></i>Add to cart</a>
            <a href=# class="btn btn-sm btn-info">
                <i class="fa fa-heart"></i>Favorite</a>
        </div>
    </div>
</div> <!--row-->

<div class="row mt-2 mt-md-4">
        <div class="col-12">$p->detail</div>
</div>

</div> <!--container-->
HTML;

    // store product info to show in Recently Viewed
    // info: product pic(first pic) and name(first 15)
    // turn into link and store in session
    $url = $_SERVER['PHP_SELF'] . '?' . $$_SERVER['QUERY_STRING'];
    $src = $img_files[0];
    $n = mb_substr($p->name, 0, 15);

    $link = <<<LINK
<div>
<a href="$url"><img src="product-images/$product_id/$src" style="max-width:60px;max-height:60px"></a>
</div>
<div class="text-info mt-2 small">$n</div>
LINK;

    // check if session is created
    // if not create an empty array
    if (!isset($_SESSION['recently_viewed'])) {
        $_SESSION['recently_viewed'] = [];
    }

    // check if we store the link as type of product in session or not
    // purpose is to prevent duplicate data
    // if not add it as first array
    if (!in_array($link, $_SESSION['recently_viewed'])) {
        array_unshift($_SESSION['recently_viewed'], $link);
    }
