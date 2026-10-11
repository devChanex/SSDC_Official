<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SSDC - Daily Transaction Summary Report (Paged)</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
                            <div class="row float-right">
                                Group By:
                                <select id="group" name="group" onchange="getclientdata(1);">
                                    <option value="Dentist">Dentist</option>
                                    <option value="Patient">Patient</option>
                                    <option value="SOAID">SOAID</option>
                                </select>
                                Date:
                                <input type="date" id="from" onchange="getclientdata(1);">
                                <input type="button" class="btn btn-primary" onclick="printDiv('daterange')" value="Print">
                            </div>
                            <h6 class="m-0 font-weight-bold">RECORDS OF SOA AS OF:
                                <?php echo date('Y-m-d'); ?>
                            </h6>
                        </div>

                        <div class="card-body" id="daterange">
                            <div>
                                <div style="text-align:left;">
                                    <h2 style="margin:0; font-weight:bold; font-size:1.5rem;">Smile Save Dental Care</h2>
                                    <div style="font-size:14px;">L22-24 B2 2/F Mondo Bambini Commercial Strip Bldg. Brgy. Zapote, Binan City, Laguna</div>
                                    <div style="font-size:14px;">Contact: 0919 009 3099 / (049) 539 0277</div>
                                </div>
                                <hr>
                                <div style="text-align:center;">
                                    <h2 style="margin:0; font-weight:bold;">DAILY TRANSACTION SUMMARY</h2>
                                    <h5 id="h3id" style="margin:0;"></h5>
                                </div>
                            </div>

                            <div id="loading" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); backdrop-filter:blur(3px); z-index:9999;">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="height:100%;">
                                    <div class="spinner-grow text-primary mb-3" role="status" style="width:3rem; height:3rem;">
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
            <script src="controllers/divPrinterController-v1.js"></script>
            <script src="controllers/dailytransactionsummaryController-v2.js"></script>
        </div>
    </div>
</body>

</html>
