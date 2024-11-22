<script>
    $(function() {
        $('a.delete').click(function() {
            if(confirm('Confirm to delete this product from cart.')) {
                var del_id = $(this).attr('data-id');
                $('#delete-id').val(del_id);
                $('#form-delete').submit();
            }
        });
        $('a.checkout').click(function() {
            $('#form-checkout').submit();
        });
    });
</script>

<div class="main-container mx-auto p-2">
    <form method="post" id="form-cart">
        <?php 
        $mid = $_SESSION['member_id'];
        $mysqli = new mysqli('localhost','root','', 'project1');
        //ถ้าส่งค่า id สำหรับการลบขึ้นมา ก็นำไปกำหนดเงื่อนไขเพื่อลบข้อมูลออกจากตาราง cart
        if (isset($_POST['delete_id'])) {
            $pid = $_POST['delete_id'];
            $sql = "DELETE FROM cart WHERE member_id = $mid AND product_id = $pid";
            $mysqli->query($sql);
        }
        //ถ้าส่่งจำนวนขึ้นมา เราต้องนำค่าทั้งหมดจากฟอร์ม ไปอัปเดตทุกรายการ
        if (isset($_POST['qty'])) {
            $count = count($_POST['qty']);
            for ($i = 0; $i < $count; $i++) {
                $qty = $_POST['qty'][$i];
                $pid = $_POST['pid'][$i];
                $sql = "UPDATE cart SET quantity = $qty WHERE member_id = $mid AND product_id = $pid";
                $mysqli->query($sql);
            }
        }
        //อ่านข้อมูลจากตาราง cart+product มาแสดง
        $sql = "SELECT p.*. c.quantity FROM product p LEFT JOIN cart c ON p.id = c.product_id WHERE c.member_id = $mid";

        $result = $mysqli->query($sql);
        if ($mysqli->error || $result->num_rows == 0) {
            echo '<h6 class="text-center text-danger">Out of product...</h6>';
            $mysqli->close();
            include 'footer.php';
            exit ('</form></div></body></html>');
        }

        echo '<h6 class="text-info mb-4 text-center">Product in cart</h6>';
        echo '<div class="container"></div>';
        $grand_total = 0;
        while ($p = $result->fetch_object()) {
            $n = mb_substr($p->name, 0, 20); //เอา 20 ตัวแรกของชื่อสินค้า
            $img_files = explode(',', $p->img_files); //แยกชื่อภาพออกจากัน
            $src = "product-images/$p->id/{$img_files[0]}"; //ผลรวมย่อยของแต่ละรายการ
            $price = number_format($p->price);
            $subtotal = $p->price * $p->quantity;  //ผลรวมสะสมของทุกรายการ
            $st = number_format($subtotal);
            $grand_total += $subtotal;
            $dvr_cost += $p->delivery_cost * $p->quantity; //ค่าจัดส่งสะสม
            echo <<<HTML
            <div class="col-2 text-right">
                <img src="$src" class="product">
            </div>
            <div class="col-10">
                <h6><a href="product-detail.php?id=$p->id" target="_blank">$n</a></h6>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small">
                        Cost: $price<br>
                        Amount: <input type="number" name="qty[]"
                        size="3" min="1" max="p->remain" value="$p->quantity">
                        <input type="hidden" name="pid[]" value="$p->id">
                        <div>
                            <a href="#" class="delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        <div>$st</div>
                    </div> <!--d-flex-->
                </div> <!-- col-10 -->
            </div> <!-- row -->
            HTML;
        }
        
        $f_dvr_cost = number_format($dvr_cost);
        $grand_total += $dvr_cost;
        $gt = number_format($grand_total);
        echo <<<HTML
        <div class="row py-3">
            <div class="col-7 col-md-9 text-center">All of delivery cost</div>
            <div class="col-5 col-md-3 text-right">$f_dvr_cost
            </div>
        </div>
        <div class="row py-3">
            <div class="col-7 col-md-9 text-center">Total</div>
            <div class="col-5 col-md-3 text-right">$gt</div>
        </div>
        <div class="row py-3">
            <div class="col-7 col-md-9 text-center">If changes</div>
            <div class="col-5 col-md-3 text-right">
                <button class="btn btn-primary">New calculate</button>
            </div>
        </div>
        HTML;
        echo '</div>';
        $mysqli->close();
        ?>
        <p class="mt-2 text-secondary small text-center">
            if you want to change the product in order Click here<b> Calculate</b> To record... </p>
        </p>
        <div class="text-center my-5">
            <a href="index.php" class="btn btn-sm btn-info mr-2 mr-md-5 px-md-5">$laquo; Select more product</a>
            <a href="#" class="checkout bt btn-sm btn-success px-md-5">Order products &raquo;</a>
        </div>
    </form>
</div>

<form id="form-delete" meythod="post">
    <input type="hidden" name="delete_id" id="delete-id">
</form>
<form id="form-checkout" action="checkout.php" method="post">   
</form>