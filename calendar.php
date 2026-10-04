<?php
session_start();
error_reporting(0);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Smile Save Dental Care</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">



    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- CSS for full calender -->
    <link href="css/calendar.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="img/vadc_icon.ico" />
    <style>
        body {
            background: #f4f7fb;
        }

        .calendar-card {
            border: 1px solid #e5eaf1;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(31, 45, 61, .06);
            overflow: hidden;
        }

        .calendar-card>.card-header {
            background: #fff;
            border-bottom: 1px solid #edf0f5;
            padding: 20px 24px;
        }

        .calendar-card>.card-body {
            padding: 20px 24px 24px;
        }

        .calendar-heading {
            color: #243447;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        .calendar-subheading {
            color: #7b8794;
            font-size: .875rem;
            margin: 4px 0 0;
        }

        .calendar-toolbar {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .calendar-toolbar__navigation,
        .calendar-toolbar__actions {
            align-items: center;
            display: flex;
            gap: 8px;
        }

        .calendar-toolbar__date {
            color: #26374a;
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 8px;
            min-width: 210px;
        }

        .calendar-toolbar .btn {
            border-radius: 8px;
            font-weight: 600;
            transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .calendar-toolbar .btn-light {
            background: #fff;
            border-color: #dce3ec;
            color: #405166;
        }

        .calendar-toolbar .btn-light:hover {
            background: #f4f7fb;
            border-color: #cbd5e1;
        }

        .calendar-toolbar .btn-primary {
            background: #4169e1;
            border-color: #4169e1;
        }

        .calendar-view-select {
            background-color: #fff;
            border: 1px solid #dce3ec;
            border-radius: 8px;
            color: #405166;
            font-weight: 600;
            height: 38px;
            padding: 0 30px 0 12px;
        }

        #calendar {
            color: #35465a;
            font-family: inherit;
        }

        @media (min-width: 768px) and (max-width: 1399.98px) {
            #calendar {
                zoom: .6;
            }
        }

        @media (min-width: 1400px) {
            #calendar {
                zoom: 1;
            }
        }

        @media (min-width: 768px) and (pointer: coarse) {
            #calendar {
                zoom: .6;
            }

            #calendar .fc-day-grid .fc-row {
                min-height: 76px;
            }
        }

        #calendar .fc-toolbar {
            display: none;
        }

        #calendar .fc-view-container {
            background: #fff;
            border: 1px solid #e6ebf2;
            border-radius: 10px;
            overflow: visible;
        }

        #calendar .fc-more-popover {
            display: flex;
            flex-direction: column;
            max-height: min(70vh, 560px);
            overflow: hidden;
            z-index: 20;
        }

        #calendar .fc-more-popover .fc-header {
            flex: 0 0 auto;
        }

        #calendar .fc-more-popover .fc-event-container {
            max-height: calc(min(70vh, 560px) - 36px);
            overflow-x: hidden;
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        #calendar .fc th {
            background: #f8fafc;
            border-color: #e6ebf2;
            color: #64748b;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            padding: 11px 4px;
            text-transform: uppercase;
        }

        #calendar .fc td,
        #calendar .fc-unthemed .fc-divider,
        #calendar .fc-unthemed .fc-list-heading td,
        #calendar .fc-unthemed .fc-popover {
            border-color: #e8edf3;
        }

        #calendar .fc-day-number {
            color: #526174;
            padding: 8px 10px;
        }

        #calendar .fc-unthemed td.fc-today {
            background: #eff6ff;
            border-color: #c8dcff;
        }

        #calendar .fc-day-top.fc-today .fc-day-number {
            background: #4169e1;
            border-radius: 50%;
            color: #fff;
            margin: 5px;
            min-width: 28px;
            padding: 5px;
            text-align: center;
        }

        #calendar .fc-day-top.calendar-day-closed .fc-day-number,
        #calendar .fc-day-top.fc-today.calendar-day-closed .fc-day-number {
            background: #dc3545;
            border-radius: 50%;
            color: #fff;
            margin: 5px;
            min-width: 28px;
            padding: 5px;
            text-align: center;
        }

        #calendar .fc-day-top.calendar-day-fully-booked .fc-day-number,
        #calendar .fc-day-top.fc-today.calendar-day-fully-booked .fc-day-number {
            background: #e69500;
            border-radius: 50%;
            color: #fff;
            margin: 5px;
            min-width: 28px;
            padding: 5px;
            text-align: center;
        }

        #calendar .fc-day-top.calendar-day-holiday-closed .fc-day-number,
        #calendar .fc-day-top.fc-today.calendar-day-holiday-closed .fc-day-number {
            background: #7952b3;
            border-radius: 50%;
            color: #fff;
            margin: 5px;
            min-width: 28px;
            padding: 5px;
            text-align: center;
        }

        #calendar .fc-bg td.fc-day.calendar-day-closed {
            background-color: #fff0f0 !important;
            box-shadow: inset 0 0 0 2px rgba(220, 53, 69, .55);
        }

        #calendar .fc-bg td.fc-day.calendar-day-fully-booked {
            background-color: #fff2d3 !important;
            box-shadow: inset 0 0 0 2px rgba(230, 149, 0, .6);
        }

        #calendar .fc-bg td.fc-day.calendar-day-holiday-closed {
            background-color: #eee4ff !important;
            box-shadow: inset 0 0 0 2px rgba(121, 82, 179, .55);
        }

        .calendar-closure-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 16px;
            margin-top: 12px;
        }

        .calendar-closure-legend__item {
            align-items: center;
            color: #526174;
            display: inline-flex;
            font-size: .8rem;
            gap: 6px;
        }

        .calendar-closure-legend__swatch {
            border-radius: 3px;
            height: 12px;
            width: 12px;
        }

        .calendar-closure-legend__swatch--closed {
            background: #dc3545;
        }

        .calendar-closure-legend__swatch--fully-booked {
            background: #e69500;
        }

        .calendar-closure-legend__swatch--holiday-closed {
            background: #7952b3;
        }

        #calendar .fc-day-grid .fc-row {
            min-height: 112px;
        }

        #calendar .fc-event {
            background: var(--event-bg, #fff) !important;
            border: 1px solid #e1e8f0 !important;
            border-left: 4px solid var(--event-accent, #4169e1) !important;
            border-radius: 7px;
            box-shadow: 0 1px 3px rgba(35, 45, 55, .06);
            color: var(--event-text, #26374a) !important;
            cursor: pointer;
            margin: 3px 4px 0;
            padding: 0;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        #calendar .fc-event:hover {
            box-shadow: 0 4px 10px rgba(35, 45, 55, .12);
            color: #26374a !important;
            transform: translateY(-1px);
        }

        #calendar .fc-event.fc-event-clinic-closed {
            background: #b42332 !important;
            border: 2px solid #8f1723 !important;
            border-left: 7px solid #72111b !important;
            box-shadow: 0 2px 6px rgba(143, 23, 35, .3);
            color: #fff !important;
        }

        #calendar .fc-event.fc-event-holiday-closed {
            background: #63369a !important;
            border: 2px dashed #43206f !important;
            border-left: 7px solid #43206f !important;
            box-shadow: 0 2px 6px rgba(67, 32, 111, .3);
            color: #fff !important;
        }

        #calendar .fc-event-clinic-closed .fc-event-summary__title,
        #calendar .fc-event-holiday-closed .fc-event-summary__title {
            color: #fff !important;
            font-weight: 800;
            letter-spacing: .03em;
        }

        #calendar .fc-day-grid-event.fc-event-clinic-closed .fc-content,
        #calendar .fc-day-grid-event.fc-event-holiday-closed .fc-content {
            align-items: center;
            display: flex;
            min-height: 42px;
            padding: 8px 10px;
        }

        #calendar .fc-event-clinic-closed .fc-event-summary__title,
        #calendar .fc-event-holiday-closed .fc-event-summary__title {
            font-size: .9rem;
            line-height: 1.4;
        }

        #calendar .fc-event-clinic-closed .fc-event-summary__title .fas,
        #calendar .fc-event-holiday-closed .fc-event-summary__title .fas {
            font-size: 1.1em;
            margin-right: 4px;
        }

        #calendar .fc-day-grid-event .fc-content {
            overflow: hidden;
            padding: 5px 8px;
            white-space: normal;
        }

        #calendar .fc-time-grid-event .fc-content {
            padding: 4px 6px;
        }

        #calendar .fc-event-summary {
            display: block;
            font-size: .75rem;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        #calendar .fc-event-summary__title {
            color: var(--event-text, #26374a);
            display: block;
            font-weight: 700;
        }

        #calendar .fc-event-summary__meta {
            color: var(--event-meta, #526174);
            display: block;
            font-size: .7rem;
            margin-top: 2px;
        }

        @media (min-width: 1400px) {
            #calendar .fc th {
                font-size: .85rem;
            }

            #calendar .fc-day-number {
                font-size: .95rem;
            }

            #calendar .fc-event-summary {
                font-size: .85rem;
            }

            #calendar .fc-event-summary__meta {
                font-size: .78rem;
            }

            #calendar .fc-event-clinic-closed .fc-event-summary__title,
            #calendar .fc-event-holiday-closed .fc-event-summary__title {
                font-size: 1rem;
            }
        }

        #calendar .calendar-event-status {
            background: #fff;
            border-radius: 50%;
            font-size: .75rem;
            line-height: 1;
            padding: 2px;
            position: absolute;
            right: 4px;
            top: 4px;
        }

        #calendar .calendar-event-status--confirmed {
            color: #d39e00;
        }

        #calendar .calendar-event-status--completed {
            color: #218838;
        }

        #calendar .calendar-event-status--cancelled {
            color: #c82333;
        }

        #calendar .fc-time-grid .fc-slats td {
            height: 6em;
        }

        #calendar .fc-time-grid .fc-axis,
        #calendar .fc-time-grid .fc-axis span {
            font-size: .95rem;
        }

        #calendar .fc-time-grid .fc-event-summary {
            font-size: 1rem;
            line-height: 1.5;
        }

        #calendar .fc-time-grid .fc-event-summary__meta {
            font-size: .9rem;
        }

        @media (min-width: 768px) and (max-width: 1399.98px) {
            #calendar .fc-time-grid .fc-slats td {
                height: 10em;
            }

            #calendar .fc-time-grid .fc-axis,
            #calendar .fc-time-grid .fc-axis span {
                font-size: 1.4rem;
            }

            #calendar .fc-time-grid .fc-event-summary {
                font-size: 1.45rem;
            }

            #calendar .fc-time-grid .fc-event-summary__meta {
                font-size: 1.2rem;
            }
        }

        @media (min-width: 768px) and (pointer: coarse) {
            #calendar .fc-time-grid .fc-slats td {
                height: 10em;
            }

            #calendar .fc-time-grid .fc-axis,
            #calendar .fc-time-grid .fc-axis span {
                font-size: 1.4rem;
            }

            #calendar .fc-time-grid .fc-event-summary {
                font-size: 1.45rem;
            }

            #calendar .fc-time-grid .fc-event-summary__meta {
                font-size: 1.2rem;
            }
        }

        #calendar .fc-now-indicator-line,
        #calendar .fc-now-indicator-arrow {
            border-color: #ef5350;
        }

        #calendar .fc-now-indicator-arrow {
            border-top-color: transparent;
            border-bottom-color: transparent;
        }

        #calendar a.fc-more {
            color: #4169e1;
            font-weight: 700;
        }

        #calendar a.fc-more.calendar-more-link {
            display: block;
            font-size: .72rem;
            line-height: 1.3;
            padding: 5px 4px;
            text-align: center;
            white-space: normal;
        }

        .calendar-appointments-list {
            max-height: min(65vh, 520px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .calendar-appointment-option {
            background: #fff;
            border: 1px solid #e6ebf2;
            border-left: 4px solid var(--event-accent, #64748b);
            border-radius: 8px;
            color: #26374a;
            display: block;
            margin-bottom: 10px;
            padding: 12px 14px;
            text-align: left;
            transition: background-color .15s ease, box-shadow .15s ease;
            width: 100%;
        }

        .calendar-appointment-option:hover,
        .calendar-appointment-option:focus {
            background: #f8fafc;
            box-shadow: 0 3px 10px rgba(35, 45, 55, .08);
            outline: none;
        }

        .calendar-appointment-option__title {
            display: block;
            font-weight: 700;
        }

        .calendar-appointment-option__meta {
            color: #64748b;
            display: block;
            font-size: .85rem;
            margin-top: 4px;
        }

        .calendar-empty-hint {
            color: #8491a2;
            font-size: .875rem;
            margin: 15px 0 0;
            text-align: center;
        }

        #calendar-feedback {
            display: none;
        }

        .calendar-modal .modal-content {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 16px 48px rgba(20, 34, 54, .2);
            overflow: hidden;
        }

        .calendar-modal .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #e8edf3;
            padding: 18px 22px;
        }

        .calendar-modal .modal-title {
            color: #26374a;
            font-weight: 700;
        }

        .calendar-modal .modal-body {
            padding: 22px;
        }

        .calendar-modal .modal-footer {
            background: #fbfcfe;
            border-top: 1px solid #e8edf3;
            padding: 14px 22px;
        }

        .calendar-modal .form-control {
            border-color: #dce3ec;
            border-radius: 7px;
        }

        .calendar-modal .form-control:focus {
            border-color: #8aa6f5;
            box-shadow: 0 0 0 .18rem rgba(65, 105, 225, .12);
        }

        @media (max-width: 767.98px) {

            .calendar-card>.card-header,
            .calendar-card>.card-body {
                padding: 16px;
            }

            .calendar-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .calendar-toolbar__navigation,
            .calendar-toolbar__actions {
                justify-content: space-between;
            }

            .calendar-toolbar__date {
                flex: 1;
                font-size: .95rem;
                min-width: 0;
                text-align: center;
            }

            #calendar .fc-day-grid .fc-row {
                min-height: 76px;
            }

            #calendar .fc-day-number {
                padding: 5px;
            }

            #calendar .fc-more-popover {
                max-height: 65vh;
                max-width: calc(100vw - 32px);
            }

            #calendar .fc-more-popover .fc-event-container {
                max-height: calc(65vh - 36px);
            }
        }

        .fc-event {
            --event-accent: #4169e1;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include_once('bars/sidebar.php'); ?>


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <?php include_once('bars/topbar.php'); ?>


                <!-- Begin Page Content -->
                <div class="container-fluid" id="content-table">

                    <!-- Page Heading -->
                    <div class="card calendar-card">
                        <div class="card-header">
                            <h1 class="calendar-heading">Clinic Calendar</h1>
                            <p class="calendar-subheading">Manage appointments and clinic availability.</p>
                        </div>
                        <div class="card-body">
                            <div class="calendar-toolbar" aria-label="Calendar navigation">
                                <div class="calendar-toolbar__navigation">
                                    <button type="button" class="btn btn-light" id="calendar-prev"
                                        aria-label="Previous date range"><i class="fas fa-chevron-left"
                                            aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-light" id="calendar-next"
                                        aria-label="Next date range"><i class="fas fa-chevron-right"
                                            aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-light" id="calendar-today">Today</button>
                                    <h2 class="calendar-toolbar__date" id="calendar-title" aria-live="polite"></h2>
                                </div>
                                <div class="calendar-toolbar__actions">
                                    <select class="calendar-view-select" id="calendar-view" aria-label="Calendar view">
                                        <option value="month">Month</option>
                                        <option value="agendaWeek">Week</option>
                                        <option value="agendaDay">Day</option>
                                    </select>
                                    <button type="button" class="btn btn-primary" id="calendar-create-event">
                                        <i class="fas fa-plus mr-1" aria-hidden="true"></i> New appointment
                                    </button>
                                </div>
                            </div>
                            <div class="alert alert-danger" id="calendar-feedback" role="alert"></div>
                            <div id="calendar" aria-label="Clinic appointment calendar"></div>
                            <div class="calendar-closure-legend" aria-label="Calendar date highlights">
                                <span class="calendar-closure-legend__item">
                                    <span class="calendar-closure-legend__swatch calendar-closure-legend__swatch--closed"
                                        aria-hidden="true"></span>Clinic closed
                                </span>
                                <span class="calendar-closure-legend__item">
                                    <span class="calendar-closure-legend__swatch calendar-closure-legend__swatch--fully-booked"
                                        aria-hidden="true"></span>Fully booked
                                </span>
                                <span class="calendar-closure-legend__item">
                                    <span class="calendar-closure-legend__swatch calendar-closure-legend__swatch--holiday-closed"
                                        aria-hidden="true"></span>Holiday closed
                                </span>
                            </div>
                            <p class="calendar-empty-hint">Select or drag across a date or time to schedule an
                                appointment.</p>
                        </div>
                    </div>

                </div>

                <!-- /.container-fluid -->
                <!-- Start popup dialog box -->
                <div class="modal fade calendar-modal" id="event_entry_modal" tabindex="-1" role="dialog"
                    aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">Appointment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="event_id" value="">
                                <div class="alert alert-danger" id="event-modal-feedback" role="alert"
                                    style="display:none;"></div>
                                <div class="img-container">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <div class="form-group">
                                                <label>Event Name Type</label><br>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="eventType"
                                                        id="defaultEvent" value="default">
                                                    <label class="form-check-label" for="defaultEvent">Default</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="eventType"
                                                        id="customEvent" value="custom" checked>
                                                    <label class="form-check-label" for="customEvent">Custom</label>
                                                </div>
                                            </div>

                                            <!-- Default Select -->
                                            <div class="form-group" id="defaultEventContainer" style="display:none;">
                                                <label for="event_name">Remarks</label>
                                                <select id="event_name" class="form-control">
                                                    <option value="Closed">Closed</option>
                                                    <option value="Fully Booked">Fully Booked</option>
                                                    <option value="Holiday-Closed">Holiday-Closed</option>
                                                </select>
                                            </div>

                                            <!-- Custom Textarea -->
                                            <div class="form-group" id="customEventContainer">

                                                <label for="appointmentTime">Appointment Time From</label>
                                                <input type="time" class="form-control" id="appointmentTime"
                                                    name="appointmentTime" step="1800" value="08:00">
                                                <label for="appointmentTime">Appointment Time To:</label>
                                                <input type="time" class="form-control" id="appointmentTimeto"
                                                    name="appointmentTimeto" step="1800" value="09:00">



                                                <label for="lastName">Dentist</label>


                                                <select name="dentist" id="dentist" class="form-control">
                                                    <?php
                                                    foreach ($dentist as $d) {
                                                        echo '<option value="' . htmlspecialchars($d) . '">' . htmlspecialchars($d) . '</option>';
                                                    }
                                                    ?>
                                                </select>


                                                <label for="appointmentTime">Patient Name:</label>
                                                <input type="text" class="form-control" id="patientName"
                                                    name="patientName" placeholder="Enter patient name">

                                                <label for="appointmentTime">Contact Number:</label>
                                                <input type="text" class="form-control" id="contactNumber"
                                                    name="contactNumber" placeholder="Enter contact number">

                                                <label for="Address">HMO Accredited:</label>

                                                <select id="hmo" name="hmo" class="form-control mb-2">
                                                    <option value="">-- Select HMO --</option>
                                                    <?php
                                                    $hmos = ['Flexicare', 'Intellicare', 'Avega', 'Eastwest', 'ValuCare', 'Medicard', 'Health Partners Dental Access, Inc.', 'Dental Network Company', 'Cocolife'];
                                                    foreach ($hmos as $hmo) {
                                                        $selected = ($_REQUEST['hmo'] ?? '') == $hmo ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($hmo, ENT_QUOTES, 'UTF-8') . '" ' . $selected . '>' . htmlspecialchars($hmo, ENT_QUOTES, 'UTF-8') . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <label for="treatment">Treatment</label>
                                                <select id="treatment" name="treatment" class="form-control">

                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="event_start_date">Date</label>
                                                <input type="date" name="event_start_date" id="event_start_date"
                                                    class="form-control onlydatepicker" placeholder="Event start date">
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="display: none;">
                                            <div class="form-group">
                                                <label for="event_end_date">Event end</label>
                                                <input type="date" name="event_end_date" id="event_end_date"
                                                    class="form-control" placeholder="Event end date">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label>Status</label><br>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="eventStatus" id="Pending"
                                                value="Confirmed" checked>
                                            <label class="form-check-label" for="Pending">Confirmed</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="eventStatus"
                                                id="Completed" value="Completed">
                                            <label class="form-check-label" for="Completed">Completed</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="eventStatus"
                                                id="Cancelled" value="Cancelled">
                                            <label class="form-check-label" for="Cancelled">Cancelled</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
                                <button type="button" id="delete_event_button" class="btn btn-danger"
                                    onclick="eventDeletion($('#event_id').val())" disabled>Delete Event</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End popup dialog box -->
                <div class="modal fade calendar-modal" id="day-appointments-modal" tabindex="-1" role="dialog"
                    aria-labelledby="day-appointments-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="day-appointments-title">Appointments</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body calendar-appointments-list" id="day-appointments-list"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Main Content -->

            <?php include_once('bars/footer.php'); ?>




            <!-- CSS for full calender -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css"
                rel="stylesheet" />
            <!-- JS for jQuery -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <!-- JS for full calender -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
            <!-- bootstrap css and js -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

            <!-- Custom scripts for all pages-->
            <script src="js/sb-admin-2.min.js"></script>
            <script src="controllers/logOutConroller.js"></script>
            <script src="controllers/sessionController.js"></script>
            <script src="controllers/calendarController-v8.js?v=20261004-13"></script>




</body>

</html>