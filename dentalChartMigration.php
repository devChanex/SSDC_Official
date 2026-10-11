<?php
$clientId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$clientId || $clientId < 1) {
    http_response_code(400);
    exit('Invalid client ID.');
}
$clientName = trim((string) ($_GET['clientname'] ?? ''));
$isEmbedded = isset($_GET['embedded']) && $_GET['embedded'] === '1';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SSDC - Dental Chart - <?php echo htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/dentalchart.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php if (!$isEmbedded)
            include_once('bars/sidebar.php'); ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php if (!$isEmbedded)
                    include_once('bars/topbar.php'); ?>

                <div class="container-fluid" id="content-table">
                    <input type="hidden" id="clientid"
                        value="<?php echo htmlspecialchars((string) $clientId, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="card shadow mb-12">
                        <div class="card-header py-3 <?php echo $cards; ?>">
                            <h6 class="m-0 font-weight-bold">Dental Chart :
                                <span
                                    id="chartPatientName"><?php echo htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8'); ?></span>
                            </h6>
                            <div class="float-right d-flex align-items-center">
                                <span id="dental-chart-migration-status" class="badge badge-warning mr-2" role="status"
                                    aria-live="polite">Loading Chart</span>
                                <button class="btn btn-warning btn-circle" data-toggle="modal"
                                    data-target="#printDentalModal" aria-label="Print dental chart">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" id="dental-chart-region">
                            <div class="d-flex flex-column align-items-center justify-content-center py-5"
                                role="status">
                                <div class="spinner-border text-primary mb-3" aria-hidden="true"></div>
                                <div class="font-weight-bold text-primary">Loading dental chart...</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="drawingModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tooth Region</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body d-flex flex-column align-items-center justify-content-center">
                                    <div class="mb-3">
                                        <label class="me-3"><input type="radio" name="penColor" value="red" checked>
                                            Red</label>
                                        <label><input type="radio" name="penColor" value="blue"> Blue</label>
                                    </div>
                                    <div id="svg-wrapper"
                                        style="position:relative; width:300px; height:300px; overflow:hidden;">
                                        <img id="toothImage" src=""
                                            style="position:absolute; top:0; left:0; width:100%; height:100%;"
                                            alt="Selected tooth">
                                        <svg id="svgOverlay" viewBox="0 0 300 300"
                                            style="position:absolute; top:0; left:0; width:100%; height:100%;">
                                            <circle id="center" data-region="center" cx="150" cy="150" r="40"
                                                fill="transparent"></circle>
                                            <rect id="top" data-region="top" x="110" y="10" width="80" height="40"
                                                fill="transparent"></rect>
                                            <rect id="bottom" data-region="bottom" x="110" y="250" width="80"
                                                height="40" fill="transparent"></rect>
                                            <rect id="left" data-region="left" x="10" y="110" width="40" height="80"
                                                fill="transparent"></rect>
                                            <rect id="right" data-region="right" x="250" y="110" width="40" height="80"
                                                fill="transparent"></rect>
                                        </svg>
                                    </div>
                                    <div class="mb-3">
                                        <label for="remarkSelect" class="me-2">Remark:</label>
                                        <select id="remarkSelect" class="form-select">
                                            <option value="-">-- Select Remark --</option>
                                            <option value="✔">✔- Present</option>
                                            <option value="C">C - Caries</option>
                                            <option value="GI">GI - Glass Ionomer 9</option>
                                            <option value="PC">PC - Plastic Crown</option>
                                            <option value="IC">IC - Incipient Caries</option>
                                            <option value="X">X - Missing</option>
                                            <option value="Ex">Ex - For Extraction</option>
                                            <option value="Fr">Fr - Fracture</option>
                                            <option value="Abr">Abr - Abrasion</option>
                                            <option value="IM">IM - Impacted Tooth</option>
                                            <option value="SP">Sp - Supernumerary Tooth</option>
                                            <option value="Rf">Rf - Root Fragment</option>
                                            <option value="Un">Un - Unerupted</option>
                                            <option value="Am">Am - Amalgam Filling</option>
                                            <option value="Co">Co - Composite Filling</option>
                                            <option value="JC">JC - Jacket Crown</option>
                                            <option value="Ab">Ab - Abutment</option>
                                            <option value="Att">Att - Attachment</option>
                                            <option value="In">In - Inlay</option>
                                            <option value="Imp">Imp - Implant</option>
                                            <option value="PFS">PFS - Pit &amp; Fissure Sealant</option>
                                            <option value="Rm">Rm - Removable Denture</option>
                                            <option value="RCT">RCT - Root Canal Treated</option>
                                            <option value="FxB">FxB - Fixed Bridge</option>
                                            <option value="Cm">Cm - Congenitally missing</option>
                                            <option value="TF">TF - (IRM)</option>
                                            <option value="V">V - Veneers</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" onclick="resetDrawingModal()">Reset</button>
                                    <button class="btn btn-primary" onclick="saveRegion()">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="printDentalModal" tabindex="-1" role="dialog"
                        aria-labelledby="printModal" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <form id="editForm">
                                    <div class="modal-header <?php echo $cards; ?>">
                                        <h5 class="modal-title" id="editModalLabel">Dental Chart Note:</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="dentalNote">Note:</label>
                                            <textarea id="dentalNote" style="width:100%;" class="form-control" rows="4"
                                                placeholder="Enter your dental chart note..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            onclick="printDental(document.getElementById('chartPatientName').textContent)">Print</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$isEmbedded)
                include_once('bars/footer.php'); ?>
            <script src="vendor/jquery/jquery.min.js"></script>
            <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
            <script src="js/sb-admin-2.min.js"></script>
            <script src="js/custom-v2.js"></script>
            <script src="controllers/logOutConroller.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="controllers/dentalchartController-v9.js?v=20261011-8"></script>
            <script src="controllers/dentalChartMigrationController-v3.js?v=20261011-4"></script>
            <script src="controllers/divDentalChartPrinterController-v1.js"></script>
</body>

</html>