<?php
require 'database_connection.php';

$event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
$event_name = trim($_POST['event_name'] ?? '');
$event_start_date = date('Y-m-d', strtotime($_POST['event_start_date'] ?? ''));
$event_end_date = date('Y-m-d', strtotime($_POST['event_end_date'] ?? ''));
$event_time = $_POST['event_time'] ?? '';
$event_status = $_POST['event_status'] ?? '';

if (!$event_id || $event_name === '' || $event_start_date === '1970-01-01' || $event_end_date === '1970-01-01' || $event_time === '') {
    echo json_encode(array(
        'status' => false,
        'msg' => 'Invalid event details.'
    ));
    exit;
}

$update_query = mysqli_prepare(
    $con,
    'UPDATE calendar_event_master
     SET event_name = ?, event_start_date = ?, event_end_date = ?, event_time = ?, event_status = ?
     WHERE event_id = ?'
);
mysqli_stmt_bind_param($update_query, 'sssssi', $event_name, $event_start_date, $event_end_date, $event_time, $event_status, $event_id);

if (mysqli_stmt_execute($update_query)) {
    echo json_encode(array(
        'status' => true,
        'msg' => 'Event updated successfully!'
    ));
} else {
    echo json_encode(array(
        'status' => false,
        'msg' => 'Sorry, Event not updated.'
    ));
}
?>