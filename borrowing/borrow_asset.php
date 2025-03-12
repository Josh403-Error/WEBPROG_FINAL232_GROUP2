<?php

include '../includes/header.php'; 


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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_no = $_POST['asset_no'];
    $borrower_name = $_POST['borrower_name'];
    $student_no = $_POST['student_no'];
    $borrow_date = $_POST['borrow_date'];
    $return_date = $_POST['return_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $action = $_POST['action'];

 
    $asset_key = array_search($asset_no, array_column($assets, 'asset_no'));
    $asset_name = $assets[$asset_key]['name'];


    $start_datetime = new DateTime("$borrow_date $start_time");
    $end_datetime = new DateTime("$return_date $end_time");
    $duration = $start_datetime->diff($end_datetime)->h + ($start_datetime->diff($end_datetime)->days * 24);


    if ($asset_key !== false && $assets[$asset_key]['status'] === 'available') {
        if ($action === 'borrow') {

            $assets[$asset_key]['status'] = 'in use';
            

            $new_borrowing = [
                'asset_no' => $asset_no,
                'asset_name' => $asset_name,
                'borrower_name' => $borrower_name,
                'student_no' => $student_no,
                'borrow_date' => $borrow_date,
                'return_date' => $return_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'duration' => $duration
            ];
            $borrowings[] = $new_borrowing;
        } elseif ($action === 'reserve') {

            $assets[$asset_key]['status'] = 'reserved';
            

            $new_borrowing = [
                'asset_no' => $asset_no,
                'asset_name' => $asset_name,
                'borrower_name' => $borrower_name,
                'student_no' => $student_no,
                'borrow_date' => $borrow_date,
                'return_date' => $return_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'duration' => $duration
            ];
            $borrowings[] = $new_borrowing;
        }


        file_put_contents($json_file, json_encode($assets, JSON_PRETTY_PRINT));
        file_put_contents($borrowing_file, json_encode($borrowings, JSON_PRETTY_PRINT));


        header('Location: borrow_asset.php');
        exit;
    } else {
        $error_message = "Asset not available for borrowing or reservation.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Asset</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js'></script>
    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
        }
    </style>
    <script>
        $(document).ready(function() {

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                selectHelper: true,
                events: [
                    <?php foreach ($borrowings as $index => $borrowing): ?>
                    {
                        title: '<?php echo $borrowing['borrower_name']; ?> - <?php echo $borrowing['asset_no']; ?> (<?php echo $borrowing['duration']; ?> hours)',
                        start: '<?php echo $borrowing['borrow_date']; ?>T<?php echo $borrowing['start_time']; ?>',
                        end: '<?php echo $borrowing['return_date']; ?>T<?php echo $borrowing['end_time']; ?>',
                        overlap: true 
                    }<?php if ($index < count($borrowings) - 1) echo ','; ?>
                    <?php endforeach; ?>
                ],
                select: function(info) {
                    $('#borrow_date').val(info.startStr.split('T')[0]);
                    $('#return_date').val(info.endStr.split('T')[0]);
                }
            });
            calendar.render();
        });
    </script>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">Borrow or Reserve Asset</h1>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="borrow_asset.php">
                            <div class="mb-3">
                                <label for="asset_no" class="form-label">Asset Number</label>
                                <select class="form-control" id="asset_no" name="asset_no" required>
                                    <option value="">Select an asset</option>
                                    <?php foreach ($assets as $asset): ?>
                                        <?php if ($asset['status'] === 'available'): ?>
                                            <option value="<?php echo $asset['asset_no']; ?>"><?php echo $asset['asset_no']; ?> - <?php echo $asset['name']; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="borrower_name" class="form-label">Borrower Name</label>
                                <input type="text" class="form-control" id="borrower_name" name="borrower_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="student_no" class="form-label">Student Number</label>
                                <input type="text" class="form-control" id="student_no" name="student_no" required>
                            </div>
                            <div class="mb-3">
                                <label for="borrow_date" class="form-label">Borrow Date</label>
                                <input type="date" class="form-control" id="borrow_date" name="borrow_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="return_date" class="form-label">Return Date</label>
                                <input type="date" class="form-control" id="return_date" name="return_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                            <button type="submit" name="action" value="borrow" class="btn btn-primary">Borrow Asset</button>
                            <button type="submit" name="action" value="reserve" class="btn btn-secondary">Reserve Asset</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">Currently Borrowed Items</h1>
                        <ul id="borrowed-items" class="list-group">
                            <?php foreach ($borrowings as $borrowing): ?>
                                <li class="list-group-item">
                                    <?php echo $borrowing['asset_no']; ?> - <?php echo $borrowing['asset_name']; ?> - <?php echo $borrowing['borrower_name']; ?> (<?php echo $borrowing['student_no']; ?>) (<?php echo $borrowing['borrow_date']; ?> <?php echo $borrowing['start_time']; ?> to <?php echo $borrowing['return_date']; ?> <?php echo $borrowing['end_time']; ?>, Duration: <?php echo $borrowing['duration']; ?> hours)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">Reservation Calendar</h1>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 

    include '../includes/footer.php'; 
    ?>
</body>
</html>