<?php
require 'database_connection.php';
$event_name = $_POST['event_name'];
$event_start_date = date("y-m-d", strtotime($_POST['event_start_date']));
$event_end_date = date("y-m-d", strtotime($_POST['event_end_date']));
$event_time_to = $_POST['event_time_to'] ?? '';
$treatment = $_POST['treatment'] ?? '';

$insert_query = "insert into `calendar_event_master`(`event_name`,`event_start_date`,`event_end_date`,`event_time`,`event_status`,`patient`,`dentist`,`contact`,`hmo`,`event_timeto`,`treatment`) values ('" . $event_name . "','" . $event_start_date . "','" . $event_end_date . "','" . $_POST['event_time'] . "','" . $_POST['event_status'] . "','" . $_POST['patientName'] . "','" . $_POST['dentistName'] . "','" . $_POST['contactNumber'] . "','" . $_POST['hmo'] . "','" . $event_time_to . "','" . $treatment . "')";
if (mysqli_query($con, $insert_query)) {
    $data = array(
        'status' => true,
        'msg' => 'Event added successfully!'
    );
} else {
    $data = array(
        'status' => false,
        'msg' => 'Event not added. Database error: ' . mysqli_error($con)
    );
}
echo json_encode($data);
?>