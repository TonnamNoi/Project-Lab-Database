<?php
// function to check which menu corresponds to the current page, highlighting it as active (selected).
function is_active(...$file) {
      $path = $_SERVER['PHP_SELF'];
      foreach ($file as $f) {
            if (stripos($path, $f) != null) {
                 return ' active';
           } 
      }
      return '';
}
?>



<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top py-0 pr-2 navbar-light bg-dark">
      <button class="navbar-toggler btn btn-m bg-white" type="button" data-toggle="collapse" data-target="#navbarToggler">
            <span class="navbar-toggler-icon custom-toggler-icon"></span>
      </button>    

      <div class="navbar-brand text-light">
            <i class="fa fa-shopping-bag fa-1x mr-2 d-none d-lg-inline"></i>
            <a href="index.php" class="navbar-brand text-light ml-2" style="text-decoration: none; font-weight: bold;">Simple Store</a>
      </div>

      <div class="collapse navbar-collapse" id="navbarToggler">
            <ul class="navbar-nav">
                  <li class="nav-item<?= is_active('/index.php') ?>">
                        <a class="nav-link text-white" href="index.php">Home</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link text-white" href="#">Order and Delivery</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link text-white" href="#">Payment Method</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link text-white" href="#">Contact</a>
                  </li>
            </ul>         
      </div>

      <div class="ml-auto">
      <?php
      @session_start();
      if (!isset($_SESSION['member_name'])) {
            echo  '<a href="member-signin.php" class="btn btn-sm text-white" style="background-color: #1B9988; display: flex;margin-right: 5px;">Sign In</a>';
      } else {
            $name = mb_substr($_SESSION['member_name'], 0, 16);

            echo <<<HTML
            <div class="dropdown d-inline mr-2">
                  <a href="#" class="btn btn-sm dropdown-toggle text-white" data-toggle="dropdown" style="background-color: #1B9988; max-width: 160px; ">$name</a>
                  <div class="dropdown-menu mt-2 bg-light" style="max-width: 300px">
                        <a class="dropdown-item w-auto" href="cart.php">Inspect cart and place order</a>
                        <a class="dropdown-item w-auto" href="member-order-list.php">Order history and payment notice</a>
                        <a class="dropdown-item" href="#">Favorite</a>    
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="member-signin.php">Personal Information</a>              
                        <a class="dropdown-item" href="member-signout.php">Sign Out</a>
                  </div>
            </div>                    
            HTML;
      }
      ?>
      </div>

      <?php $q = $_GET['q'] ?? ''; ?>
      
      <form class="form-inline mr-2 my-2" method="get" action="search.php"> 
            <div class="input-group input-group-sm">
                  <input type="text" name="q" class="form-control" placeholder="Search..." size="10" value="<?= $q ?>">
                  <div class="input-group-append">
                        <button class="btn text-white" style="background-color: #C5D86D;">
                              <i class="fa fa-search"></i>
                        </button> 
                  </div>
            </div>
      </form>

      <a href="cart.php" class="btn btn-sm text-white my-2" style="background-color: #1B998B;">
            <i class="fas fa-lg fa-shopping-cart"></i>
            <span class="badge badge-pill badge-danger"></span>
      </a>
</nav>

<script>
function updateCart() {
     $.ajax({
           url: 'ajax-update-cart.php', 
           success: (result) => {  
                  if (result == 0) {
                        result = '';
                  }               
                  $('span.badge').text(result);
           }                 
      });
}
$(function() {
     updateCart();     
});
</script>
