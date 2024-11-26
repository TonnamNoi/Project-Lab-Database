<?php
@session_start(); // Ensure session is started

// Check if user is logged in (assumed that 'user_id' exists in the session)
if (!isset($_SESSION['member_id'])) {
    echo "<p>Please log in to see your recently viewed products.</p>";
    exit();
}

// Check if there are any recently viewed products
if (isset($_SESSION['recently_viewed']) && !empty($_SESSION['recently_viewed'])) {

    echo <<<HTML
    <hr>
    <div class="mt-4 mb-4 text-center">
        <h6 class="text-secondary">Recently viewed products</h6>
        <div class="d-flex mt-3 justify-content-center">
    HTML;

    // Limit to 20 products, as per your original logic
    $i = 1;
    foreach ($_SESSION['recently_viewed'] as $product_id) {
        echo <<<HTML
        <div class="d-flex flex-column justify-content-between border p-2 mr-2 text-center" style="max-width:100px;">
            $product_id
        </div>
        HTML;

        if ($i == 20) {
            break;
        }
        $i++;
    }

    echo <<<HTML
        </div>
    </div>
    HTML;

} else {
    // If there are no recently viewed products
    echo "<p>No recently viewed products.</p>";
}
