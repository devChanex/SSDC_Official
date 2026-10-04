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

function describeInvalidParameter($name, $value, $expectation)
{
    return $name . ' (received: ' . json_encode($value, JSON_INVALID_UTF8_SUBSTITUTE) . '; ' . $expectation . ')';
}

$eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
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

$invalidFields = array();
if (!$eventId) {
    $invalidFields[] = describeInvalidParameter('event_id', $_POST['event_id'] ?? null, 'must be a valid integer');
}
if ($eventName === '') {
    $invalidFields[] = describeInvalidParameter('event_name', $eventName, 'is required');
}
if (!isCalendarDate($eventStartDate)) {
    $invalidFields[] = describeInvalidParameter('event_start_date', $eventStartDate, 'must use YYYY-MM-DD format');
}

if (!isCalendarTime($eventTime)) {
    $invalidFields[] = describeInvalidParameter('event_time', $eventTime, 'must use HH:MM format');
}
if (!isCalendarTime($eventTimeTo)) {
    $invalidFields[] = describeInvalidParameter('event_time_to', $eventTimeTo, 'must use HH:MM format');
}
if (!in_array($eventStatus, array('Confirmed', 'Completed', 'Cancelled'), true)) {
    $invalidFields[] = describeInvalidParameter('event_status', $eventStatus, 'must be Confirmed, Completed, or Cancelled');
}

if ($invalidFields) {
    respondWithError('Invalid appointment parameter(s): ' . implode('; ', $invalidFields) . '.');
}

$updateQuery = mysqli_prepare(
    $con,
    'UPDATE calendar_event_master
     SET event_name = ?, event_start_date = ?, event_end_date = ?, event_time = ?, event_status = ?, patient = ?, dentist = ?, contact = ?, hmo = ?, event_timeto = ?, treatment = ?
     WHERE event_id = ?'
);

if (!$updateQuery) {
    respondWithError('The appointment could not be updated. Please try again.', 500);
}

mysqli_stmt_bind_param(
    $updateQuery,
    'sssssssssssi',
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
    $treatment,
    $eventId
);

if (!mysqli_stmt_execute($updateQuery)) {
    mysqli_stmt_close($updateQuery);
    respondWithError('The appointment could not be updated. Please try again.', 500);
}

mysqli_stmt_close($updateQuery);
echo json_encode(array(
    'status' => true,
    'msg' => 'Appointment updated successfully!'
));
