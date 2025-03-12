<?php  
// Include the header
include '../includes/header.php'; 
?>
<div class="container mt-4">
    <h1>Manage Digital Assets</h1>
    <a href="add_asset.php" class="btn btn-primary mb-3">Add New Asset</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Asset number</th>
                <th>Asset Name</th>
                <th>Description</th>
                <th>Type</th> <!-- Added Type column -->
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Read assets from JSON file
            $json_file = __DIR__ . '/assets.json';
            if (file_exists($json_file)) {
                $json_data = file_get_contents($json_file);
                $assets = json_decode($json_data, true);

                if (!empty($assets)) {
                    foreach ($assets as $row) {
                        $status = isset($row['status']) ? $row['status'] : 'available'; // Default to 'available' if status is not set
                        $asset_type = isset($row['asset_type']) ? $row['asset_type'] : 'Unknown'; // Default to 'Unknown' if asset_type is not set
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['asset_no']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['description']}</td>
                                <td>{$asset_type}</td> <!-- Display asset type -->
                                <td>
                                    <select class='form-select' onchange='updateStatus({$row['id']}, this.value)'>
                                        <option value='available' " . ($status == 'available' ? 'selected' : '') . ">Available</option>
                                        <option value='in_use' " . ($status == 'in_use' ? 'selected' : '') . ">In Use</option>
                                        <option value='reserved' " . ($status == 'reserved' ? 'selected' : '') . ">Reserved</option>
                                        <option value='in_repair' " . ($status == 'in_repair' ? 'selected' : '') . ">In Repair</option>
                                        <option value='retired' " . ($status == 'retired' ? 'selected' : '') . ">Retired</option>
                                    </select>
                                </td>
                                <td>
                                    <a href='edit_asset.php?id={$row['id']}' class='btn btn-warning' onclick='return confirmEdit()'>Edit</a>
                                    <a href='delete_asset.php?id={$row['id']}' class='btn btn-danger' onclick='return confirmDelete()'>Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No assets found</td></tr>"; // Updated colspan
                }
            } else {
                echo "<tr><td colspan='7'>Error: assets.json file not found</td></tr>"; // Updated colspan
            }
            ?>
        </tbody>
    </table>
</div>
<script>
function updateStatus(id, status) {
    // Send an AJAX request to update the status
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                alert("Status updated successfully");
            } else {
                alert("Error updating status: " + xhr.statusText);
            }
        }
    };
    xhr.send("id=" + id + "&status=" + status);
}

function confirmEdit() {
    return confirm("Are you sure you want to edit this asset?");
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this asset?");
}
</script>
<?php 
// Include the footer
include '../includes/footer.php'; 
?>