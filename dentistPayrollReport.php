<?php
session_start();
error_reporting(0);
require_once('services/databaseService.php');
$database = new Database();
$db = $database->dbConnection();
$dentistList = [];
try {
    $stmt = $db->prepare("SELECT DISTINCT dentist FROM treatmentsoa WHERE dentist <> '' ORDER BY dentist");
    $stmt->execute();
    $dentistList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $dentistList = [];
}
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dentist Payroll Report | Victoria Advanced Dental Care</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .table th,
        .table td {
            font-size: 0.82rem;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #daterange,
            #daterange * {
                visibility: visible;
            }

            #daterange {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .no-print,
            .no-print * {
                display: none !important;
            }

            @page {
                margin: 8mm;
                size: auto;
            }

            body {
                margin: 0;
                padding: 0;
            }

            #daterange {
                padding: 4mm;
            }

            .card,
            .card-body {
                box-shadow: none !important;
            }

            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                border: 1px solid #dee2e6 !important;
                padding: 0.3rem !important;
                font-size: 0.78rem !important;
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
                        <div class="card-header py-3 <?php echo isset($cards) ? $cards : ''; ?>">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h6 class="m-0 font-weight-bold">DENTIST PAYROLL REPORT</h6>
                                    <small><?php echo date('Y-m-d'); ?></small>
                                </div>
                                <div class="form-inline no-print">
                                    <select id="dentist" class="form-control form-control-sm mr-2 mb-2"
                                        onchange="loadPayrollReport();">
                                        <option value="">Select dentist</option>
                                        <?php foreach ($dentistList as $row) {
                                            $dentist = htmlspecialchars($row['dentist']);
                                            echo '<option value="' . $dentist . '">' . $dentist . '</option>';
                                        } ?>
                                    </select>
                                    <input type="date" id="from" class="form-control form-control-sm mr-2 mb-2"
                                        value="<?php echo $today; ?>" onchange="loadPayrollReport();">
                                    <input type="date" id="to" class="form-control form-control-sm mr-2 mb-2"
                                        value="<?php echo $today; ?>" onchange="loadPayrollReport();">
                                    <button class="btn btn-primary btn-sm mr-2 mb-2"
                                        onclick="loadPayrollReport();">Load</button>
                                    <button class="btn btn-success btn-sm mr-2 mb-2"
                                        onclick="recalculateTotal();">Calculate</button>
                                    <button class="btn btn-secondary btn-sm mb-2"
                                        onclick="printDiv('daterange');">Print</button>
                                    <button class="btn btn-danger btn-sm mb-2"
                                        onclick="openPayslipModal();">Payslip</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="daterange">
                            <div class="border rounded p-3 mb-3" style="background:#f8f9f9;">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-start">
                                    <div>
                                        <h2 class="h5 font-weight-bold mb-1">Smile Save Dental Care</h2>
                                        <div class="small text-secondary mb-1">L22-24 B2 2/F Mondo Bambini Commercial
                                            Strip Bldg. Brgy. Zapote, Binan City, Laguna
                                        </div>

                                    </div>
                                    <div class="text-md-right mt-3 mt-md-0">
                                        <div class="small text-uppercase text-muted">Generated</div>
                                        <div class="font-weight-bold"><?php echo date('Y-m-d h:i A'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 mb-2">
                                    <div class="small text-uppercase text-muted mb-1">Dentist Payroll Payslip</div>

                                </div>
                                <div class="col-sm-4 mb-2">
                                    <div class="small text-uppercase text-muted mb-1">Dentist</div>
                                    <div class="font-weight-bold" id="payslipDentist">-</div>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <div class="small text-uppercase text-muted mb-1">Period</div>
                                    <div class="font-weight-bold" id="payslipPeriod">-</div>
                                </div>

                            </div>
                            <div id="loading"
                                style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); backdrop-filter: blur(3px); z-index:9999;">
                                <div class="d-flex flex-column align-items-center justify-content-center"
                                    style="height: 100%;">
                                    <div class="spinner-grow text-primary mb-3" role="status"
                                        style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div>
                                    <div class="h5 font-weight-bold text-primary">Loading, please wait...</div>
                                </div>
                            </div>
                            <div id="responseBody"></div>

                            <div id="responseBody-dentistPayrollAdjustments"></div>



                            <div class="modal fade" id="basicSalaryModal" tabindex="-1" role="dialog"
                                aria-labelledby="basicSalaryModalLabel" aria-hidden="true">

                                <div class="modal-dialog modal-dialog-centered" role="document">

                                    <div class="modal-content">

                                        <div class="modal-header <?php echo $cards; ?>">
                                            <h5 class="modal-title" id="basicSalaryModalLabel">
                                                Basic Salary
                                            </h5>

                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label for="daysRendered">Days Rendered</label>
                                                <input type="number" class="form-control" id="daysRendered" min="0"
                                                    step="0.01" value="0">
                                            </div>

                                            <div class="form-group">
                                                <label for="ratePerDay">Rate Per Day</label>
                                                <input type="number" class="form-control" id="ratePerDay" min="0"
                                                    step="0.01" value="0">
                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-success"
                                                onclick="updateBasicSalary();">
                                                Save
                                            </button>

                                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                Cancel
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="modal fade" id="payslipModal" tabindex="-1" role="dialog"
                            aria-labelledby="payslipModalLabel" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

                                <div class="modal-content">

                                    <div class="modal-header <?php echo $cards; ?>">
                                        <h5 class="modal-title" id="basicSalaryModalLabel">
                                            Basic Salary
                                        </h5>

                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div id="payslipDiv"
                                            style="width:700px; max-width:100%; margin:20px auto; padding:30px; background:#fff; border:1px solid #ccc; font-family:Arial, Helvetica, sans-serif; color:#222; box-sizing:border-box;">

                                            <!-- Header -->
                                            <div
                                                style="text-align:center; padding-bottom:15px; border-bottom:2px solid #222;">
                                                <div id="payslipCompany"
                                                    style="font-size:22px; font-weight:bold; letter-spacing:1px;">
                                                    Smile Save Dental Care
                                                </div>

                                                <div id="payslipAddress"
                                                    style="font-size:12px; color:#666; margin-top:4px;">
                                                    L22-24 B2 2/F Mondo Bambini Commercial Strip Bldg. Brgy. Zapote,
                                                    Binan City,
                                                    Laguna
                                                </div>

                                                <div
                                                    style="font-size:20px; font-weight:bold; margin-top:15px; letter-spacing:2px;">
                                                    PAYSLIP
                                                </div>
                                            </div>

                                            <!-- Employee Information -->
                                            <table
                                                style="width:100%; border-collapse:collapse; margin-top:20px; font-size:13px;">
                                                <tr>
                                                    <td style="width:50%; padding:5px 0;">
                                                        <strong>Dentist:</strong>
                                                        <span id="payslipDentists">-</span>
                                                    </td>


                                                </tr>

                                                <tr>
                                                    <td style="width:50%; padding:5px 0;">
                                                        <strong>Pay Period:</strong>
                                                        <span id="payslipPeriods">-</span>
                                                    </td>


                                                </tr>
                                            </table>

                                            <!-- Earnings -->
                                            <table
                                                style="width:100%; border-collapse:collapse; margin-top:20px; font-size:13px;">
                                                <tr>
                                                    <th colspan="2"
                                                        style="padding:9px 10px; text-align:left; background:#f1f1f1; border:1px solid #ccc;">
                                                        EARNINGS
                                                    </th>
                                                </tr>

                                                <tr>
                                                    <td style="padding:9px 10px; border:1px solid #ddd;">
                                                        Commission
                                                    </td>

                                                    <td id="payslipCommission"
                                                        style="padding:9px 10px; border:1px solid #ddd; text-align:right;">
                                                        0.00
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:9px 10px; border:1px solid #ddd;">
                                                        Basic Salary

                                                        <div style="font-size:10px; color:#777; margin-top:3px;">
                                                            <span id="payslipDaysRendered">Days Rendered: 0</span>
                                                            &nbsp; × &nbsp;
                                                            <span id="payslipRatePerDay">Rate Per Day: 0.00</span>
                                                        </div>
                                                    </td>

                                                    <td id="payslipBasicSalary"
                                                        style="padding:9px 10px; border:1px solid #ddd; text-align:right;">
                                                        0.00
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:9px 10px; border:1px solid #ddd;">
                                                        Total Additional
                                                    </td>

                                                    <td id="payslipAdditional"
                                                        style="padding:9px 10px; border:1px solid #ddd; text-align:right;">
                                                        0.00
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:10px; border:1px solid #ddd; font-weight:bold;">
                                                        Gross Pay
                                                    </td>

                                                    <td id="payslipGrossPay"
                                                        style="padding:10px; border:1px solid #ddd; text-align:right; font-weight:bold;">
                                                        0.00
                                                    </td>
                                                </tr>
                                            </table>

                                            <!-- Deductions -->
                                            <table
                                                style="width:100%; border-collapse:collapse; margin-top:15px; font-size:13px;">
                                                <tr>
                                                    <th colspan="2"
                                                        style="padding:9px 10px; text-align:left; background:#f1f1f1; border:1px solid #ccc;">
                                                        DEDUCTIONS
                                                    </th>
                                                </tr>

                                                <tr>
                                                    <td style="padding:9px 10px; border:1px solid #ddd;">
                                                        Tax and Material Costs (10%)
                                                    </td>

                                                    <td id="payslipTaxMaterial"
                                                        style="padding:9px 10px; border:1px solid #ddd; text-align:right;">
                                                        0.00
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:9px 10px; border:1px solid #ddd;">
                                                        Other Deductions
                                                    </td>

                                                    <td id="payslipOtherDeductions"
                                                        style="padding:9px 10px; border:1px solid #ddd; text-align:right;">
                                                        0.00
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:10px; border:1px solid #ddd; font-weight:bold;">
                                                        Total Deductions
                                                    </td>

                                                    <td id="payslipDeductions"
                                                        style="padding:10px; border:1px solid #ddd; text-align:right; font-weight:bold;">
                                                        0.00
                                                    </td>
                                                </tr>
                                            </table>

                                            <!-- Net Pay -->
                                            <table
                                                style="width:100%; border-collapse:collapse; margin-top:15px; font-size:13px;">
                                                <tr>
                                                    <td
                                                        style="padding:14px 10px; border-top:2px solid #222; border-bottom:2px solid #222; font-size:16px; font-weight:bold;">
                                                        NET PAY
                                                    </td>

                                                    <td id="payslipNetPay"
                                                        style="padding:14px 10px; border-top:2px solid #222; border-bottom:2px solid #222; text-align:right; font-size:18px; font-weight:bold;">
                                                        0.00
                                                    </td>
                                                </tr>
                                            </table>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button" class="btn btn-success" onclick="printDiv('payslipDiv');">
                                            Print
                                        </button>

                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            Cancel
                                        </button>

                                    </div>

                                </div>

                            </div>

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
            <script src="controllers/dentistPayrollReportController-v3.js"></script>
            <!-- <script src="controllers/divPrinterController-v1.js"></script> -->
            <script src="controllers/divPrinterController-v3.js"></script>
        </div>
    </div>
</body>

</html>