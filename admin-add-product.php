<form method="post" enctype="multipart/form-data" class="mt-5">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $num_upload_files = count($_FILES['upfile']['tmp_name']);
        $num_errs = 0;

        for ($i = 0; $i < $num_upload_files; $i++) {
            if ($_FILES['upfile']['error'][$i] > 0) {
                $num_errs++;
                continue;
            }

            // Validate MIME type using finfo
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $fileMimeType = $finfo->file($_FILES['upfile']['tmp_name'][$i]);
            if (!in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
                $num_errs++;
            }
        }

        if ($num_errs == $num_upload_files) {
            $msg = 'Upload failed';
            $contextual = 'alert-danger';
            goto end_post;
        }

        // Database operations
        $mysqli = new mysqli('localhost', 'root', '', 'pwdb_simple_store');
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $sql = 'INSERT INTO product VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $mysqli->prepare($sql);
        $params = [0, $_POST['name'], $_POST['detail'], $_POST['price'], $_POST['remain'], $_POST['delivery_cost'], ''];
        $stmt->bind_param('issdiis', ...$params);
        $stmt->execute();
        $product_id = $stmt->insert_id;
        $stmt->close();

        $image_folder = "product-images/$product_id";
        if (!is_dir($image_folder)) {
            mkdir($image_folder, 0777, true);
        }

        $img_files = [];
        $n = 1;
        for ($i = 0; $i < $num_upload_files; $i++) {
            if ($_FILES['upfile']['error'][$i] > 0) {
                continue;
            }

            $filePath = $_FILES['upfile']['tmp_name'][$i];
            $old_name = $_FILES['upfile']['name'][$i];
            $ext = pathinfo($old_name, PATHINFO_EXTENSION);
            $new_name = "$product_id-$n.$ext";

            // Get MIME type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $fileMimeType = $finfo->file($filePath);

            // Create source image based on file type
            switch ($fileMimeType) {
                case 'image/jpeg':
                    $sourceImage = imagecreatefromjpeg($filePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($filePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($filePath);
                    break;
                default:
                    continue 2; // Skip to the next file if unsupported
            }

            // Get original dimensions
            list($width, $height) = getimagesize($filePath);

            // Resize dimensions
            $maxWidth = 600;
            $maxHeight = 600;
            $scale = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int)($width * $scale);
            $newHeight = (int)($height * $scale);

            // Create resized image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($fileMimeType === 'image/png' || $fileMimeType === 'image/gif') {
                imagecolortransparent($resizedImage, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }

            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save resized image
            switch ($fileMimeType) {
                case 'image/jpeg':
                    imagejpeg($resizedImage, "$image_folder/$new_name", 90); // Save as JPEG
                    break;
                case 'image/png':
                    imagepng($resizedImage, "$image_folder/$new_name"); // Save as PNG
                    break;
                case 'image/gif':
                    imagegif($resizedImage, "$image_folder/$new_name"); // Save as GIF
                    break;
            }

            imagedestroy($resizedImage);
            imagedestroy($sourceImage);

            $img_files[] = $new_name;
            $n++;
        }

        $img_file = implode(',', $img_files);

        // Update database with image file names
        $stmt = $mysqli->prepare("UPDATE product SET img_files=? WHERE id=?");
        $stmt->bind_param('si', $img_file, $product_id);
        $stmt->execute();
        $stmt->close();

        $msg = 'Data Saved';
        $contextual = 'alert-success';

        end_post:

        echo <<<HTML
    <div class="alert $contextual mb-4" role="alert">
        $msg
        <button class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    </div>
    HTML;

        $mysqli->close();
    }
    ?>

    <h6 class="text-info text-center">Add list of order</h6>
    <div class="form-group mt-3">
        <label>Name of product</label>
        <input type="text" name="name" class="form-control formcontrol-sm" required>
    </div>
    <div class="form-group mt-2">
        <label>Detail</label>
        <textarea name="detail" class="form-control form-control-sm" rows="3" required></textarea>
    </div>
    <div class="d-flex justify-content-between flex-wrap">
        <div class="form-group mt-2">
            <label>Cost</label>
            <input type="text" name="price" class="form-control form-control-sm w-auto" required>
        </div>
        <div class="form-group" mt-2>
            <label>Balance</label>
            <input type="text" name="remain" class="form-control form-control-sm w-auto" required>
        </div>
        <div class="form-group mt-2">
            <label>
                Shipping cost</label>
            <input type="text" name="delivery_cost" required class="form-control form-control-sm w-auto">
        </div>
    </div>
    <div class="mt-2 mb-2">Product images (1 - 4 images)
        <?php
        for ($i = 1; $i <= 4; $i++) {
            //Create input file limit 4
            echo <<<HTML
        <div class="custom-file mb-2">
            <input type="file" name="upfile[]" class="custom-file-input" accept="image/*">
            <label class="custom-file-label">Select file</label>
        </div>
        HTML;
        }
        ?>
    </div>
    <button class="btn btn-primary btn-sm d-block mx-auto mt-4 px-5">Confirm</button>
</form>