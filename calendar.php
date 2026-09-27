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
        .fc-event {
            border: 1px solid #e2e8f0 !important;
            border-left: 4px solid var(--event-accent, #3a87ad) !important;
            border-radius: 8px;
            background: #fff !important;
            color: #243447 !important;
            box-shadow: 0 2px 7px rgba(35, 45, 55, 0.12);
            transition: box-shadow 0.15s ease, transform 0.15s ease;
        }

        .fc-event:hover {
            color: #243447 !important;
            box-shadow: 0 5px 12px rgba(35, 45, 55, 0.2);
            transform: translateY(-1px);
        }

        .fc-day-grid-event {
            min-height: 0;
            margin: 4px 3px 0;
            padding: 0;
        }

        .fc-day-grid-event .fc-content {
            display: block;
            overflow: visible !important;
            padding: 0;
            white-space: normal !important;
        }

        .calendar-event-card__header {
            align-items: center;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            min-height: 32px;
            padding: 6px 34px 6px 9px;
        }

        .calendar-event-card__notice {
            align-items: center;
            background: #fff1f2;
            border-radius: 7px;
            color: #9f1239;
            display: flex;
            font-size: 0.75rem;
            font-weight: 800;
            gap: 8px;
            justify-content: center;
            letter-spacing: 0.04em;
            min-height: 42px;
            padding: 8px 34px 8px 10px;
            text-align: center;
            text-transform: uppercase;
        }

        .calendar-event-card__notice i {
            font-size: 0.95rem;
        }

        .calendar-event-card__details {
            color: #243447;
            font-size: 0.78rem;
            font-weight: 400;
            line-height: 1.35;
            overflow-wrap: anywhere;
            padding: 8px 9px;
            white-space: pre-wrap;
        }

        .calendar-event-card__detail-line strong {
            font-weight: 700;
        }

        .calendar-event-card__time {
            align-items: center;
            color: #64748b;
            display: inline-flex;
            font-size: 0.72rem;
            font-weight: 700;
            gap: 5px;
            line-height: 1.2;
        }

        .calendar-event-status {
            position: absolute;
            top: 4px;
            right: 4px;
            z-index: 3;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #fff;
            text-align: center;
            line-height: 22px;
            font-size: 15px;
            pointer-events: none;
        }

        .calendar-event-status--pending {
            color: #d39e00;
        }

        .calendar-event-status--completed {
            color: #218838;
        }

        .calendar-event-status--cancelled {
            color: #c82333;
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
                    <div class="card shadow">
                        <div class="card-header py-3 <?php echo $cards; ?>">
                            <strong>Clinic Calendar</strong>

                        </div>
                        <div class="card-body">
                            <!-- USE THIS SPACE FOR YOUR ADDITIONAL CODE SNIPPET -->

                            <div id="calendar"></div>




                            <!-- END OF YOUR ADDITIONAL CODE SNIPPET -->
                        </div>
                    </div>

                </div>

                <!-- /.container-fluid -->
                <!-- Start popup dialog box -->
                <div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">Appointment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="event_id" value="">
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
                                                <label for="event_name">Event Name</label>
                                                <select id="event_name" class="form-control">
                                                    <option value="Closed">Closed</option>
                                                    <option value="Fully Booked">Fully Booked</option>
                                                    <option value="Holiday-Closed">Holiday-Closed</option>
                                                </select>
                                            </div>

                                            <!-- Custom Textarea -->
                                            <div class="form-group" id="customEventContainer">

                                                <label for="appointmentTime">Appointment Time</label>
                                                <input type="time" class="form-control" id="appointmentTime"
                                                    name="appointmentTime" step="1800" value="08:00">
                                                <label for="custom_event_name">Details</label>
                                                <textarea id="custom_event_name" class="form-control" rows="6">Patient:
Contact:
Procedure:
Dentist:</textarea>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="event_start_date">Event start</label>
                                                <input type="date" name="event_start_date" id="event_start_date"
                                                    class="form-control onlydatepicker" placeholder="Event start date">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
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
                                                value="Pending" checked>
                                            <label class="form-check-label" for="Pending">Pending</label>
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
            <script src="controllers/calendarController-v4.js"></script>




</body>

</html>