<?php @session_start(); ?>
<!DOCTYPE html>
<html>



<head>
      <?php require 'head.php'; ?>
      <style>
            html,
            body {
                  background: azure;
                  min-height: 100vh;
                  display: flex;
                  align-items: center;
                  justify-content: center;
            }

            /* Main container styles */
            div#main-container {
                  max-width: 1000px;

                  padding: 20px;
                  border-radius: 8px;
            }

            /* Card styles */
            div.card {
                  min-width: 130px;
                  max-width: 150px;
                  border: 1px solid #17a2b8;
                  /* Matching border with navbar/footer */
                  border-radius: 10px;
            }

            /* Card image styles */
            div.card img {
                  max-width: 100px;
                  max-height: 100px;
                  margin-bottom: 10px;
            }

            /* Card text styles */
            div.card h6 {
                  color: #28a745;
                  /* Greenish color matching the theme */
            }

            div.card .btn-info {
                  background-color: #17a2b8;
                  /* Matching button color with navbar */
                  border: none;
            }

            div.card .btn-info:hover {
                  background-color: #138496;
                  /* Darker shade for hover effect */
            }

            /* Heading styles */
            h6 {
                  color: #28a745;
                  /* Greenish heading to match card text */
            }

            /* Card layout */
            .card-deck .card {
                  margin-bottom: 20px;
            }

            hr {
                  width: 93%;
                  background: #eee;
                  margin-top: 30px;
            }

            /* Pagination styling */
            .pagination {
                  justify-content: center;
            }
      </style>
</head>

<body class="px-3 pt-5">
      <?php require 'navbar.php'; ?>

      <div id="main-container" class="mx-auto">
            <div class="card-deck mx-4 mt-5 justify-content-center">
                  <?php
                  require 'lib/pagination-v2.class.php';
                  $page = new PaginationV2();

                  $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
                  $sql = 'SELECT * FROM product';
                  $result = $page->query($mysqli, $sql, 10);

                  while ($p = $result->fetch_object()) {
                        $n = $p->name;
                        if (strlen($n) > 20) {
                              $n = mb_substr($n, 0, 20) . '...';
                        }
                        $images = explode(',', $p->img_files);
                        $src = "product-images/$p->id/{$images[0]}";
                        $prc = number_format($p->price);
                        $stock = $p->remain;

                        echo <<<HTML
                <div class="card shadow mb-3">
                    <img class="card-img-top d-block mt-1 mx-auto" src="$src">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h6 class="card-title">$n</h6>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="mt-2">฿$prc</span><br>
                            <a class="btn btn-info btn-sm p-1" href="product-detail.php?id=$p->id">
                                <i class="fa fa-search-plus"></i></a>
                        </div>
                    </div>
                </div>
                HTML;
                  }

                  $mysqli->close();
                  ?>
            </div> <!-- card-deck -->
            <br>

            <!-- Pagination if needed -->
            <?php
            if ($page->total_pages() > 1) {
                  $page->echo_pagenums_bootstrap();
            }
            require 'footer.php';
            include 'recently-viewed.php';
            ?>
      </div> <!-- main-container -->

      <br><br><br><br>


</body>

</html>