<?php

include '../includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_file = __DIR__ . '/../manage_assets/assets.json';
    $borrowing_file = __DIR__ . '/borrowing.json';
    $assets = [];
    $borrowings = [];

    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        $assets = json_decode($json_data, true);
    }

    if (file_exists($borrowing_file)) {
        $borrowing_data = file_get_contents($borrowing_file);
        $borrowings = json_decode($borrowing_data, true);
    }

    $asset_no = $_POST['asset_no'];
    $return_date = date('Y-m-d H:i:s');


    $asset_key = array_search($asset_no, array_column($assets, 'asset_no'));
    if ($asset_key !== false && $assets[$asset_key]['status'] === 'borrowed') {

        $assets[$asset_key]['status'] = 'available';
        

        foreach ($borrowings as &$borrowing) {
            if ($borrowing['asset_no'] === $asset_no && !isset($borrowing['return_date'])) {
                $borrowing['return_date'] = $return_date;
                break;
            }
        }


        file_put_contents($json_file, json_encode($assets, JSON_PRETTY_PRINT));
        file_put_contents($borrowing_file, json_encode($borrowings, JSON_PRETTY_PRINT));

        header('Location: ../manage_assets.php');
        exit;
    } else {
        $error_message = "Asset not found or not currently borrowed.";
    }
}
?>

<div class="container mt-4">
    <h1>Return Asset</h1>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST" action="return_asset.php">
        <div class="mb-3">
            <label for="asset_no" class="form-label">Asset Number</label>
            <input type="text" class="form-control" id="asset_no" name="asset_no" required>
        </div>
        <button type="submit" class="btn btn-primary">Return Asset</button>
    </form>
</div>

<?php 

include '../includes/footer.php'; 
?>