<?php
$id = $_GET['id'];

$json_file = __DIR__ . '/assets.json';
$json_data = file_get_contents($json_file);
$assets = json_decode($json_data, true);

foreach ($assets as $key => $asset) {
    if ($asset['id'] == $id) {
        unset($assets[$key]);
        break;
    }
}

file_put_contents($json_file, json_encode(array_values($assets), JSON_PRETTY_PRINT));

header("Location: manage_assets.php");
exit();
?>