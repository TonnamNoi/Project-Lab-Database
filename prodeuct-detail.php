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
                data: {'pro_id': id},
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

                    updateCart();
                }
            })
        })
    })
</script>

<div id="main-container" class="mx-auto mt-5">
    <div id="show-alert"></div>
    <?php 
    $product_id = $GET['id'] ?? 0;
    //Tonnam เช็ค สินค้า จาก table product ว่ามีอยุ่จริงไหม หมดยัง ถ้าหมดให้ปลุ่มอยุ่สถานะคลิกไม่ได้ 
    ?>
</div>