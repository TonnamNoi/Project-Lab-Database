<!DOCTYPE html>
<html>

<head>
    <?php require 'head.php'; ?>
</head>

<body>
    <?php require 'navbar.php'; ?>
    <

        <div class="card-deck mx-4 mt-5 justify-content-center">
        <?php
        // require 'lib/pagination-v2.class.php';
        // $page = new PaginationV2();
        // $mysqli = new mysqli('localhost', 'root', '', 'project1');
        // $sql = 'SELECT * FROM product';
        // $result = $page->query($mysqli, $sql, 20);
        // while ($p = $result->fetch_object()) {
        //     $n = $p->name;
        //     if (strlen($n) > 20) {
        //         $n = mb_substr($n, 0, 20) . '...';
        //     }
        //     $images = explode(',', $p->img_files);
        //     $src = "product-images/$p->id/{$images[0]}";
        //     $prc = number_format($p->price);
        //     echo <<<HTML
        //     <div class="card border border-info pt-2 shadow mb-3">
        //         <img class="card-img-top d-block mt-2" src="$src">
        //         <div class="card-body d-flex flex-column justify-content-between">
        //             <h6 class="card-title text-success">$n</h6>
        //             <div class="d-flex justify-content-between mt-2">
        //                 <span class="mt-2">฿$prc</span>
        //                 <a class="btn btn-info btn-sm p-1" href="product-detail.php?id=$p->id">
        //                     <i class="fa fa-search-plus"></i>
        //                 </a>
        //             </div>
        //         </div>
        //     </div>
        //     HTML;
        // }
        // $mysqli->close();
        // ?>
        </div>
        <?php
        // if ($page->total_pages() > 1) {
        //     $page->echo_pagenums_bootstrap();
        // }
        // include 'recently-viewed.php'
        ?>

        <?php require 'footer.php'; ?>

        
</body>

</html>