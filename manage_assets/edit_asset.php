<?php 
// Start the session
session_start();

// Include the header
include '../includes/header.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json_file = __DIR__ . '/assets.json';
    
    // Check if the file exists and is readable
    if (file_exists($json_file) && is_readable($json_file)) {
        $json_data = file_get_contents($json_file);
        $assets = json_decode($json_data, true);
    } else {
        $assets = [];
    }

    // Ensure $assets is an array
    if (!is_array($assets)) {
        $assets = [];
    }

    foreach ($assets as &$asset) {
        if ($asset['id'] == $_POST['id']) {
            $asset['asset_no'] = $_POST['asset_no'];
            $asset['name'] = $_POST['name'];
            $asset['description'] = $_POST['description'];
            break;
        }
    }

    file_put_contents($json_file, json_encode($assets, JSON_PRETTY_PRINT));

    // Store the success message in a session variable
    $_SESSION['success_message'] = "Asset updated successfully!";
    
    // Redirect to the same page to clear the form and display the message
    header("Location: edit_asset.php?id=" . $_POST['id']);
    exit();
} else {
    $id = $_GET['id'];
    $json_file = __DIR__ . '/assets.json';
    
    // Check if the file exists and is readable
    if (file_exists($json_file) && is_readable($json_file)) {
        $json_data = file_get_contents($json_file);
        $assets = json_decode($json_data, true);
    } else {
        $assets = [];
    }

    // Ensure $assets is an array
    if (!is_array($assets)) {
        $assets = [];
    }

    $current_asset = null;
    foreach ($assets as $asset) {
        if ($asset['id'] == $id) {
            $current_asset = $asset;
            break;
        }
    }

    // Handle case where asset is not found
    if ($current_asset === null) {
        echo "<div class='alert alert-danger' role='alert'>Asset not found!</div>";
        include '../includes/footer.php';
        exit();
    }
}

// Display the success message if it exists
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success' role='alert'>{$_SESSION['success_message']}</div>";
    // Clear the success message
    unset($_SESSION['success_message']);
    // Display the confirmation prompt using JavaScript
    echo "<script>
        if (confirm('Asset updated successfully! Do you want to edit another asset?')) {
            window.location.href = 'manage_assets.php';
            } else {
            window.location.href = 'manage_assets.php';
        }
    </script>";
        }
?>

<div class="container mt-4">
    <h1>Edit Asset</h1>
    <form method="POST" action="edit_asset.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($current_asset['id']); ?>">
        <div class="form-group">
            <label for="asset_no">Asset Number</label>
            <input type="text" class="form-control" id="asset_no" name="asset_no" value="<?php echo htmlspecialchars($current_asset['asset_no']); ?>" required>
        </div>
        <div class="form-group">
            <label for="name">Asset Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($current_asset['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($current_asset['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Asset</button>
    </form>
</div>

<?php 
// Include the footer
include '../includes/footer.php'; 
?>