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
        $mysqli = new mysqli('localhost', 'root', '', 'project1');
        $sql = 'SELECT * FROM product';
        $result = $page -> query($mysqli, $sql, 20);
        while ($p = $result->fetch_object()) {
            $n = $p->name;
            if(strlen($n) > 20) {
                $n = mb_substr($n, 0, 20) . '...';
            }
            $images = explode(',', $p->img_files);
            $src = "product-images/$p->id/{$images[0]}";
            $prc = number_format($p->price);
            echo <<<HTML
            <div class="card border border-info pt-2 shadow mb-3">
                <img class="card-img-top d-block mt-2" src="$src">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h6 class="card-title text-success">$n</h6>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="mt-2">฿$prc</span>
                        <a class="btn btn-info btn-sm p-1" href="product-detail.php?id=$p->id">
                            <i class="fa fa-search-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            HTML;
        }
        $mysqli->close();
        ?>
    </div>
    <?php 
    if ($page->total_pages() > 1) {
        $page->echo_pagenums_bootstrap();
    }
    include 'recently-viewed.php'
    ?>

    <?php require 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
</body>

</html>