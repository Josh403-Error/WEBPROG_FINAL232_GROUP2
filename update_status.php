<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Read assets from JSON file
    $json_file = __DIR__ . '/assets.json';
    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        $assets = json_decode($json_data, true);

        // Update the status of the asset
        foreach ($assets as &$asset) {
            if ($asset['id'] == $id) {
                $asset['status'] = $status;
                break;
            }
        }

        // Save the updated assets back to the JSON file
        file_put_contents($json_file, json_encode($assets));
        echo "Status updated successfully";
    } else {
        echo "Error: assets.json file not found";
    }
}
?>