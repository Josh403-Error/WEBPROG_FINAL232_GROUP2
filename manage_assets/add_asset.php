<?php
// Include the header
include '../includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_file = __DIR__ . '/assets.json';
    $assets = [];

    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        $assets = json_decode($json_data, true);
    }

    $new_asset = [
        'id' => count($assets) + 1,
        'asset_no' => $_POST['asset_no'],
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'asset_type' => $_POST['asset_type'],
        'status' => 'available'
    ];

    $assets[] = $new_asset;
    file_put_contents($json_file, json_encode($assets, JSON_PRETTY_PRINT));

    header('Location: manage_assets.php');
    exit;
}
?>

<div class="container mt-4">
    <h1>Add New Asset</h1>
    <form method="POST" action="add_asset.php">
        <div class="mb-3">
            <label for="asset_no" class="form-label">Asset Number</label>
            <input type="text" class="form-control" id="asset_no" name="asset_no" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Asset Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="asset_type" class="form-label">Asset Type</label>
            <input type="text" class="form-control" id="asset_type" name="asset_type" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Asset</button>
    </form>
</div>

<?php 
// Include the footer
include '../includes/footer.php'; 
?>