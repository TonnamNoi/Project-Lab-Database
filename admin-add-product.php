<?php
@session_start();
if (!isset($_SESSION['admin'])) {
      header('location: admin-signin.php');
      exit;
}
?>
<!DOCTYPE html>
<html>
<head>
      <?php require 'head.php'; ?>
      <link href="js/summernote/summernote-bs4.css" rel="stylesheet">
      <style>
            html, body {
                  width: 100%;
                  height: 100%;
                  background:  azure;
                  padding-top:  1rem;
            }
            
            * {
                font-size: 0.93rem;
            }
            
            form {
                  max-width: 500px;
                  margin: auto;
            }
            
            [name="price"], [name="remain"], [name="delivery_cost"] {
                  max-width: 150px;
            }
      </style> 
      <script src="js/summernote/summernote-bs4.min.js"></script>
      <script src="js/summernote/lang/summernote-th-TH.js"></script>
      <script>
      $(function() {
            $('[name="detail"]').summernote({lang: 'th-TH'});

            $(':file').change(function() { 
                   var filename = $(this).val().split('\\').slice(-1)[0];
                  $(this).next().after().text(filename);
            });              
      });
      </script>
</head>
<body class="p-3">
 <?php require 'navbar.php'; ?> 
    
<form method="post" enctype="multipart/form-data" class="mt-5">
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $num_upload_files = count($_FILES['upfile']['tmp_name']);
      $num_errs = 0;
      for ($i = 0; $i < $num_upload_files; $i++) {               
            if ($_FILES['upfile']['error'][$i] > 0) {
                  $num_errs += 1;
                  continue;
            } 

            $type = explode('/', $_FILES['upfile']['type'][$i]);
            if ($type[0] != 'image') {
                  $num_errs += 1;
            }
      }      
      
      if ($num_errs == $num_upload_files) {    
            $msg = 'There was an error uploading the image';
            $contextual = 'alert-danger';
            goto end_post; 
      }     
      
      // add the product name to the table
      $mysqli = new mysqli('localhost', 'root', 'root', 'project1');
      $sql = 'INSERT INTO product VALUES (?, ?, ?, ?, ?, ?, ?)';
      $stmt = $mysqli->stmt_init();        
      $stmt->prepare($sql);
      $params = [0, $_POST['name'], $_POST['detail'], $_POST['price'], 
                         $_POST['remain'], $_POST['delivery_cost'], ''];
      
      $stmt->bind_param('issdiis', ...$params);
      $stmt->execute();
      $product_id = $stmt->insert_id;     // ID of latest row
      $stmt->close(); 

      require 'lib/image-sizing.class.php';
      
      // create folders step by step, one level at a time.
      $image_folder = 'product-images';
      @mkdir($image_folder);
      $image_folder .= "/$product_id";
      @mkdir($image_folder);

      $img_files = [];
      $n = 1;
      for ($i = 0; $i < $num_upload_files; $i++) {
            if ($_FILES['upfile']['error'][$i] > 0) {
                  continue;
            } 

            $type = explode('/', $_FILES['upfile']['type'][$i]);
            if ($type[0] != 'image') {
                  continue;
            }
            // resize the image
            $image = ImageSizing::from_upload('upfile', $i);
            $image->resize_max(600, 600);  // maximum w x h not exceed 600

            $old_name = $_FILES['upfile']['name'][$i];

            // extract file extension for use in saving the file
            $ext = pathinfo($old_name, PATHINFO_EXTENSION);	

            // combine product ID, image sequence number, and file extension
            $new_name ="$product_id-$n.$ext";   // ex. 1-1.png, 1-2.png
            $image->save("$image_folder/$new_name");           
            $img_files[] = $new_name;
            $n++;
      }

      $img_file = implode(',', $img_files);
      // update file name in the table
      $sql = "UPDATE product SET img_files = '$img_file' 
                  WHERE id = $product_id";

      $mysqli->query($sql);     
      $msg = 'Data saved';
      $contextual = 'alert-success';
      
      end_post:             
      echo  <<<HTML
      <div class="alert $contextual mb-4" role="alert">
            $msg
            <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      </div>    
      HTML;
      
      $mysqli->close();     
}
?>
<h6 class="text-info text-center">Add Product</h6>
<div class="form-group mt-3">
      <label>Product Name</label>
      <input type="text" name="name" class="form-control form-control-sm" required>
</div>
<div class="form-group mt-2">
      <label>Details</label>
      <textarea name="detail" class="form-control form-control-sm" rows="3" required></textarea>
</div>
<div class="d-flex justify-content-between flex-wrap">
      <div class="form-group mt-2">
            <label>Price</label>
            <input type="text" name="price" class="form-control form-control-sm w-auto" required>
      </div>
      <div class="form-group mt-2">
            <label>Remain</label>
            <input type="text" name="remain" class="form-control form-control-sm w-auto" required>
      </div>
      <div class="form-group mt-2">
            <label>Delivery cost</label>
            <input type="text" name="delivery_cost" class="form-control form-control-sm w-auto" required>
      </div>
</div>
<div class="mt-2 mb-2">Product images (1 - 4 images)</div>
<?php
for ($i = 1; $i <= 4; $i++) {       // create 4 file input file
      echo <<<HTML
      <div class="custom-file mb-2">
      <input type="file" name="upfile[]" class="custom-file-input" accept="image/*">
      <label class="custom-file-label">Select File</label>
      </div>               
      HTML;
}
?>
</div>
<button class="btn btn-primary btn-sm d-block mx-auto mt-4 px-5">Confirm</button>
<br><br><br><br>
</form>

<?php require 'footer.php'; ?>     
</body>
</html>