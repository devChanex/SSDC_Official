<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SSDC - Unmigrated Tooth Remarks Report</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #dentalChartMigrationModal .modal-dialog {
            width: calc(100% - 2rem);
            max-width: 560px;
        }

        #dentalChartMigrationModal .modal-content {
            overflow: hidden;
            border: 0;
            border-radius: .85rem;
            box-shadow: 0 1rem 3rem rgba(24, 39, 75, .28);
        }

        #dentalChartMigrationModal .modal-header {
            padding: 1rem 1.25rem;
            color: #fff;
            background: linear-gradient(115deg, #234e70, #2f80a8);
            border-bottom: 0;
        }

        #dentalChartMigrationModal .modal-header .close {
            color: #fff;
            opacity: .85;
            text-shadow: none;
        }

        #dentalChartMigrationModal .modal-header .close:hover {
            opacity: 1;
        }

        #dentalChartMigrationModal .migration-modal-subtitle {
            margin-top: .2rem;
            color: rgba(255, 255, 255, .78);
            font-size: .78rem;
        }

        #dentalChartMigrationModal .modal-body {
            padding: 1rem 1.1rem 1.1rem;
            background: #f4f7fb;
        }

        #dentalChartMigrationModal .migration-progress-card {
            border: 1px solid #e5eaf1;
            border-radius: .65rem;
            background: #fff;
            box-shadow: 0 .25rem .75rem rgba(31, 45, 61, .05);
            padding: .85rem .9rem;
        }

        #dentalChartMigrationModal .migration-stat {
            height: 100%;
            padding: .55rem .75rem;
            border-left: 3px solid #4e73df;
            border-radius: .4rem;
            background: #f7f9ff;
        }

        #dentalChartMigrationModal .migration-stat-label {
            color: #718096;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        #dentalChartMigrationModal .migration-stat-value {
            margin-top: .1rem;
            color: #26364a;
            font-size: 1rem;
            font-weight: 700;
        }

        #dentalChartMigrationModal .migration-progress-track {
            height: 1.4rem;
            overflow: hidden;
            border-radius: 999px;
            background: #e9edf3;
        }

        #dentalChartMigrationModal .migration-progress-track .progress-bar {
            min-width: 0;
            font-size: .78rem;
            font-weight: 700;
            transition: width .35s ease;
        }

        #dentalChartMigrationModal #processedRecordsProgress {
            color: #68778a;
            font-size: .85rem;
        }

        #dentalChartMigrationModal #batchCountdownProgress {
            min-height: 1.1rem;
            color: #2f5f8f;
            font-size: .8rem;
        }

        #dentalChartMigrationModal .migration-patient-status {
            margin-top: .75rem;
            padding: .75rem .85rem;
            border: 1px solid #e5eaf1;
            border-radius: .65rem;
            background: #fff;
        }

        #dentalChartMigrationModal .migration-patient-list {
            max-height: 190px;
            overflow-y: auto;
            padding: 0;
            margin: .5rem 0 0;
            list-style: none;
        }

        #dentalChartMigrationModal .migration-patient-list:empty::after {
            display: block;
            padding: .45rem .55rem;
            color: #8a96a5;
            content: "No patients in this group.";
            font-size: .8rem;
        }

        #dentalChartMigrationModal .migration-patient-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .45rem .55rem;
            border-bottom: 1px solid #edf0f4;
            color: #3e4a59;
            font-size: .82rem;
        }

        #dentalChartMigrationModal .migration-patient-item:last-child {
            border-bottom: 0;
        }

        #dentalChartMigrationModal .migration-patient-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #dentalChartMigrationModal .migration-patient-badge {
            flex: 0 0 auto;
            font-size: .68rem;
        }

        @media (max-width: 767.98px) {
            #dentalChartMigrationModal .modal-dialog {
                margin: .5rem;
            }

            #dentalChartMigrationModal .migration-patient-list {
                max-height: 150px;
            }
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include_once('bars/sidebar.php'); ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once('bars/topbar.php'); ?>

                <div class="container-fluid" id="content-table">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3 <?php echo $cards; ?>">
                            <div class="row float-right align-items-center">
                                <label for="lastNameInitial" class="mb-0 mr-2">Last name:</label>
                                <select id="lastNameInitial" name="lastNameInitial" class="form-control mr-2"
                                    onchange="loadUnmigratedToothRemarks()" style="width: auto;">
                                    <option value="">All letters</option>
                                    <?php foreach (range('A', 'Z') as $letter) { ?>
                                        <option value="<?php echo $letter; ?>"><?php echo $letter; ?></option>
                                    <?php } ?>
                                </select>
                                <label for="limit" class="mb-0 mr-2">Limit:</label>
                                <select id="limit" name="limit" class="form-control mr-2"
                                    onchange="loadUnmigratedToothRemarks()" style="width: auto;">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                </select>
                                <button type="button" class="btn btn-primary mr-2"
                                    onclick="startMigrationBatch(true)">Start Migration</button>
                                <button type="button" class="btn btn-info mr-2"
                                    onclick="showMigrationModal()">Show</button>
                                <button type="button" class="btn btn-secondary mr-2"
                                    onclick="loadUnmigratedToothRemarks()">Refresh</button>
                                <input type="button" class="btn btn-primary" onclick="printDiv('report')" value="Print">
                            </div>
                            <h6 class="m-0 font-weight-bold">UNMIGRATED TOOTH REMARKS AS OF:
                                <?php echo date('Y-m-d'); ?>
                            </h6>
                            <div class="mt-2">
                                    <span id="migrationProgress" class="badge badge-secondary" role="status"
                                        aria-live="polite">Idle - click Start Migration to begin.</span>
                            </div>
                        </div>

                        <div class="card-body" id="report">
                            <div>
                                <div style="text-align:left;">
                                    <h2 style="margin:0; font-weight:bold; font-size:1.5rem;">Smile Save Dental Care
                                    </h2>
                                    <div style="font-size:14px;">L22-24 B2 2/F Mondo Bambini Commercial Strip Bldg.
                                        Brgy.
                                        Zapote, Binan City, Laguna</div>
                                    <div style="font-size:14px;">Contact: 0919 009 3099 / (049) 539 0277</div>
                                </div>
                                <hr>
                                <div style="text-align:center;">
                                    <h2 style="margin:0; font-weight:bold;">UNMIGRATED TOOTH REMARKS</h2>
                                    <h5 id="h3id" style="margin:0;"></h5>
                                </div>
                            </div>

                            <div id="loading" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
                                background:rgba(255,255,255,0.7); backdrop-filter:blur(3px); z-index:9999;">
                                <div class="d-flex flex-column align-items-center justify-content-center"
                                    style="height:100%;">
                                    <div class="spinner-grow text-primary mb-3" role="status"
                                        style="width:3rem; height:3rem;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div class="h5 font-weight-bold text-primary">Loading, please wait...</div>
                                </div>
                            </div>

                            <div class="card-body" id="responseBody"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="dentalChartMigrationModal" tabindex="-1" role="dialog"
                aria-labelledby="dentalChartMigrationModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title font-weight-bold" id="dentalChartMigrationModalTitle">
                                    <i class="fas fa-tasks mr-2" aria-hidden="true"></i>Migration Progress
                                </h5>
                                <div class="migration-modal-subtitle">
                                    Batch status updates in real time
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <section class="migration-progress-card" aria-label="Migration batch progress">
                                <div class="row mb-2">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <div class="migration-stat">
                                            <div class="migration-stat-label">Current batch</div>
                                            <div class="migration-stat-value" id="batchNumberProgress">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="migration-stat" style="border-left-color:#1cc88a;">
                                            <div class="migration-stat-label">Batches completed</div>
                                            <div class="migration-stat-value" id="completedBatchesProgress">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="font-weight-bold text-gray-700">Save progress</span>
                                    <span class="small text-muted">Successful and failed save calls</span>
                                </div>
                                <div class="progress migration-progress-track">
                                    <div id="successfulSaveProgress" class="progress-bar bg-success"
                                        role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                        aria-valuenow="0" style="width: 0%">Successful: 0</div>
                                    <div id="failedSaveProgress" class="progress-bar bg-danger"
                                        role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                        aria-valuenow="0" style="width: 0%">Failed: 0</div>
                                </div>
                                <div id="processedRecordsProgress" class="mt-2">
                                    Records processed: 0 / 0
                                </div>
                                <div id="batchCountdownProgress" class="mt-2 font-weight-bold"
                                    role="status" aria-live="polite"></div>
                            </section>
                            <section class="migration-patient-status" aria-label="Patients in the current batch">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center font-weight-bold small text-success">
                                            <i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
                                            In progress <span id="inProgressPatientCount" class="ml-1">(0)</span>
                                        </div>
                                        <ul id="inProgressPatientList" class="migration-patient-list"
                                            aria-live="polite"></ul>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center font-weight-bold small text-primary">
                                            <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                                            Completed <span id="completedPatientCount" class="ml-1">(0)</span>
                                        </div>
                                        <ul id="completedPatientList" class="migration-patient-list"
                                            aria-live="polite"></ul>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <div id="migrationFramePool" aria-hidden="true"
                style="position:fixed; left:-10000px; top:0; width:1px; height:1px; overflow:hidden;"></div>

            <?php include_once('bars/footer.php'); ?>

            <script src="vendor/jquery/jquery.min.js"></script>
            <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
            <script src="vendor/datatables/jquery.dataTables.min.js"></script>
            <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
            <script src="js/demo/datatables-demo.js"></script>
            <script src="js/sb-admin-2.min.js"></script>
            <script src="controllers/logOutConroller.js"></script>
            <script src="controllers/sessionController.js"></script>
            <script src="js/custom-v1.js"></script>
            <script src="controllers/unmigratedToothRemarksController-v1.js?v=20261011-6"></script>
            <script src="controllers/divPrinterController-v1.js"></script>
</body>

</html>