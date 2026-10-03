<?php
require 'database_connection.php';
require_once 'bars/properties.php';

header('Content-Type: application/json; charset=utf-8');

$displayQuery = '
    SELECT
        event_id,
        event_name,
        event_start_date,
        event_end_date,
        event_time,
        event_status,
        patient,
        dentist,
        contact,
        hmo,
        event_timeto,
        treatment
    FROM calendar_event_master
    ORDER BY event_start_date ASC, event_time ASC
';

$results = mysqli_query($con, $displayQuery);
if (!$results) {
    http_response_code(500);
    echo json_encode(array(
        'status' => false,
        'msg' => 'Unable to load calendar events.',
        'data' => array()
    ));
    exit;
}

$events = array();
$specialEventNames = array('Closed', 'Fully Booked', 'Holiday-Closed');
$dentistColors = array();
foreach ($dentistColorCodeCalendar as $dentistColorEntry) {
    $colorParts = explode('|', $dentistColorEntry, 2);
    if (count($colorParts) === 2 && preg_match('/^#[0-9a-fA-F]{6}$/', $colorParts[1])) {
        $dentistColors[strtolower(trim($colorParts[0]))] = $colorParts[1];
    }
}

while ($row = mysqli_fetch_assoc($results)) {
    $eventName = trim($row['event_name'] ?? '');
    $eventStartDate = date('Y-m-d', strtotime($row['event_start_date']));
    $eventEndDate = date('Y-m-d', strtotime($row['event_end_date']));
    $eventTime = $row['event_time'] ?: '00:00:00';
    $eventTimeTo = $row['event_timeto'] ?: '';
    $isSpecialEvent = in_array(strtolower($eventName), array_map('strtolower', $specialEventNames), true);

    $start = new DateTime($eventStartDate . ' ' . $eventTime);
    if ($isSpecialEvent) {
        $end = new DateTime($eventEndDate);
        $end->modify('+1 day');
    } else {
        $end = $eventTimeTo !== ''
            ? new DateTime($eventStartDate . ' ' . $eventTimeTo)
            : (clone $start)->modify('+30 minutes');
    }

    $event = array(
        'event_id' => $row['event_id'],
        'event_name' => $eventName,
        'event_start_date' => $eventStartDate,
        'event_end_date' => $eventEndDate,
        'event_time' => $row['event_time'],
        'event_status' => trim($row['event_status'] ?? ''),
        'patient' => $row['patient'],
        'dentist' => $row['dentist'],
        'contact' => $row['contact'],
        'hmo' => $row['hmo'],
        'event_timeto' => $eventTimeTo,
        'treatment' => $row['treatment'],
        'title' => $eventName,
        'start' => $isSpecialEvent
            ? $eventStartDate
            : $start->format('Y-m-d\TH:i:s'),
        'end' => $isSpecialEvent
            ? $end->format('Y-m-d')
            : $end->format('Y-m-d\TH:i:s'),
        'allDay' => $isSpecialEvent,
        'url' => '#'
    );

    if ($isSpecialEvent) {
        $event['color'] = '#dc3545';
    } else {
        $dentistName = trim($row['dentist'] ?? '');
        if ($dentistName === '' && preg_match('/Dentist:\s*([^\r\n]+)/i', $eventName, $matches)) {
            $dentistName = trim($matches[1]);
        }
        $event['color'] = $dentistColors[strtolower($dentistName)] ?? '#64748b';
    }

    $events[] = $event;
}

echo json_encode(array(
    'status' => true,
    'msg' => 'success',
    'data' => $events
));
