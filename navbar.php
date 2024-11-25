<?php
// function to check which menu corresponds to the current page, highlighting it as active (selected).
function is_active(...$file) {
      $path = $_SERVER['PHP_SELF'];   // DO NOT USE: __FILE__ it returns full file path and may not match the way URLs or menu items are structured
      foreach ($file as $f) {
            if (stripos($path, $f) != null) {
                 return ' active';
           } 
      }
      return '';
}
?>
 <!-- Bootstrap navbar that expands on screens of size lg and above, and hidden on smaller screens (displayed by clicking the hamburger icon) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-0 pr-2 justify-content-start" style="min-width: 600px">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler">
            <span class="navbar-toggler-icon"></span>
      </button>    
       
      <div class="navbar-brand text-warning">
            <i class="fa fa-shopping-bag fa-1x mr-2 d-none d-lg-inline"></i>
            <a href="index.php" style="text-decoration: none"><span class="navbar-brand text-warning">Simple Store</span></a>
      </div>
    
      <div class="collapse navbar-collapse" id="navbarToggler">
            <ul class="navbar-nav">
                  <li class="nav-item<?= is_active('/index.php') ?>"><a class="nav-link" href="index.php">Home</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Order and Delivery</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Payment Method</a></li>
                  <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            </ul>         
      </div>
    
      <div class="col text-right my-2 pr-2">
      <?php
      @session_start();
      if (!isset($_SESSION['member_name'])) {
            echo  '<a href="member-signin.php" class="btn btn-sm btn-danger">Sign In</a>';
      } else {
            $name = mb_substr($_SESSION['member_name'], 0, 16);

            echo <<<HTML
            <div class="dropdown d-inline">
                  <a href=# class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" style="max-width: 160px">$name</a>
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
    
      <!-- if keyword is sent, insert it into the form input field -->
      <?php $q = $_GET['q'] ?? '';  ?>
      
      <form class="form-inline mr-2 my-2" method="get" action="search.php"> 
            <div class="input-group input-group-sm">
                  <input type="text" name="q" class="form-control" placeholder="Search..." size="12" value="<?= $q ?>">
                  <div class="input-group-append">
                        <button class="btn btn-success">
                              <i class="fa fa-search"></i>
                        </button> 
                  </div>
            </div>
      </form>

      <a href="cart.php" class="btn bg-primary btn-sm text-white my-2">
            <i class="fas fa-lg fa-shopping-cart"></i>
            <span class="badge badge-pill badge-danger"></span>
      </a>
</nav>

<script>    
// navbar and cart button will appear on every page, when opening any page need to update number of items in cart and display it again each time
// this function will send a request to fetch number of items added to the cart so the number can be displayed on the button in the Navbar
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