<?php
require 'database_connection.php';

$display_query = "
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
";

$results = mysqli_query($con, $display_query);

$data_arr = array();

if (mysqli_num_rows($results) > 0) {

	while ($row = mysqli_fetch_assoc($results)) {

		// Build the start datetime
		$start = new DateTime(
			date('Y-m-d', strtotime($row['event_start_date'])) .
			' ' .
			$row['event_time']
		);

		// Appointment duration = 30 minutes
		$end = clone $start;
		$end->modify('+30 minutes');

		$eventName = trim($row['event_name']);
		$eventStatus = trim($row['event_status']);

		$event = array();
		$event['event_id'] = $row['event_id'];
		$event['event_name'] = $eventName;
		$event['event_start_date'] = date('Y-m-d', strtotime($row['event_start_date']));
		$event['event_end_date'] = date('Y-m-d', strtotime($row['event_end_date']));
		$event['event_time'] = $row['event_time'];
		$event['event_status'] = $eventStatus;
		$event['patient'] = $row['patient'];
		$event['dentist'] = $row['dentist'];
		$event['contact'] = $row['contact'];
		$event['hmo'] = $row['hmo'];
		$event['event_timeto'] = $row['event_timeto'];
		$event['treatment'] = $row['treatment'];
		$formattedTime = date("g:i A", strtotime($row['event_time']));



		$event['title'] = $eventName . "\nTime: " . $formattedTime;

		$event['start'] = $start->format('Y-m-d\TH:i:s');
		$event['end'] = $end->format('Y-m-d\TH:i:s');
		$event['url'] = '#';


		if (in_array($eventName, ['Closed', 'Fully Booked', 'Holiday-Closed'])) {

			$event['color'] = '#dc3545';

		} else {

			$dentist = '';

			if (preg_match('/Dentist:\s*(.+)/i', $eventName, $matches)) {
				$dentist = strtoupper(trim($matches[1]));
			}

			if (strpos($dentist, 'REG') !== false) {
				$event['color'] = 'blue';

			} else {
				$event['color'] = 'gray';
			}

		}

		$data_arr[] = $event;
	}

	echo json_encode(array(
		"status" => true,
		"msg" => "success",
		"data" => $data_arr
	));

} else {

	echo json_encode(array(
		"status" => false,
		"msg" => "No events found.",
		"data" => array()
	));

}