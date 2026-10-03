<?php
require 'database_connection.php';

header('Content-Type: application/json; charset=utf-8');

function respondWithError($message, $statusCode = 400)
{
    http_response_code($statusCode);
    echo json_encode(array('status' => false, 'msg' => $message));
    exit;
}

function isCalendarDate($value)
{
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
}

function isCalendarTime($value)
{
    return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $value) === 1;
}

$eventName = trim($_POST['event_name'] ?? '');
$eventStartDate = trim($_POST['event_start_date'] ?? '');
$eventEndDate = trim($_POST['event_end_date'] ?? '');
$eventTime = trim($_POST['event_time'] ?? '');
$eventTimeTo = trim($_POST['event_time_to'] ?? '');
$eventStatus = trim($_POST['event_status'] ?? '');
$patient = trim($_POST['patientName'] ?? '');
$dentist = trim($_POST['dentistName'] ?? '');
$contact = trim($_POST['contactNumber'] ?? '');
$hmo = trim($_POST['hmo'] ?? '');
$treatment = trim($_POST['treatment'] ?? '');

if (
    $eventName === '' ||
    !isCalendarDate($eventStartDate) ||
    !isCalendarDate($eventEndDate) ||
    $eventEndDate < $eventStartDate ||
    !isCalendarTime($eventTime) ||
    !isCalendarTime($eventTimeTo) ||
    !in_array($eventStatus, array('Confirmed', 'Completed', 'Cancelled'), true)
) {
    respondWithError('Please provide valid appointment details.');
}

$insertQuery = mysqli_prepare(
    $con,
    'INSERT INTO calendar_event_master
        (event_name, event_start_date, event_end_date, event_time, event_status, patient, dentist, contact, hmo, event_timeto, treatment)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

if (!$insertQuery) {
    respondWithError('The appointment could not be saved. Please try again.', 500);
}

mysqli_stmt_bind_param(
    $insertQuery,
    'sssssssssss',
    $eventName,
    $eventStartDate,
    $eventEndDate,
    $eventTime,
    $eventStatus,
    $patient,
    $dentist,
    $contact,
    $hmo,
    $eventTimeTo,
    $treatment
);

if (!mysqli_stmt_execute($insertQuery)) {
    mysqli_stmt_close($insertQuery);
    respondWithError('The appointment could not be saved. Please try again.', 500);
}

mysqli_stmt_close($insertQuery);
echo json_encode(array(
    'status' => true,
    'msg' => 'Appointment added successfully!'
));
