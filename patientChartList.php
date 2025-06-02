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
    <link href="css/custom.css" rel="stylesheet">

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
                    <div class="row" id="patientCards">
                    </div>

                    <!-- Page Heading -->
                    <div class="card shadow mb-12">
                        <div class="card-header py-3 <?php echo $cards; ?>">
                            <h6 class="m-0 font-weight-bold">Patient Chart : <?php echo $_GET["clientname"]; ?></h6>
                            <input type="hidden" id="clientid" value="<?php echo $_GET["id"]; ?>">
                        </div>
                        <div class="card-body">
                            <!-- USE THIS SPACE FOR YOUR ADDITIONAL CODE SNIPPET -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table text-dark" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Dentist</th>
                                                <th>Treatment</th>
                                                <th>Diagnosis</th>
                                                <th>Remarks</th>
                                                <th>Details</th>
                                                <th>HMO</th>
                                                <th>Fee</th>
                                                <th>Payment</th>
                                                <th>Payment Type</th>
                                                <th>Payment Date</th>
                                                <th>Balance</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>

                                        <tbody id="resultResponsez">



                                        </tbody>
                                    </table>
                                </div>
                            </div>



                            <!-- Edit Modal -->

                            <div class="modal fade" id="editModal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">

                                <div class="modal-dialog modal-xl " role="document">
                                    <div class="modal-content ">
                                        <form id="editForm">
                                            <div class="modal-header <?php echo $cards; ?>">
                                                <h5 class="modal-title" id="editModalLabel">Edit Treatment</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="soaid" id="edit-soaid">
                                                <input type="hidden" name="tsubid" id="edit-tsubid">

                                                <div class="form-group">
                                                    <label>Treatment</label>
                                                    <input type="text" class="form-control" name="treatment"
                                                        id="edit-treatment">
                                                </div>
                                                <div class="form-group">
                                                    <label>Diagnosis</label>

                                                    <textarea class="form-control" name="diagnosis" id="edit-diagnosis"
                                                        rows="4"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Remarks</label>
                                                    <input type="text" class="form-control" name="remarks"
                                                        id="edit-remarks">
                                                </div>
                                                <div class="form-group">
                                                    <label>Details</label>
                                                    <textarea class="form-control" name="details" id="edit-details"
                                                        rows="4"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="number" step="0.01" class="form-control" name="price"
                                                        id="edit-price">
                                                </div>
                                                HMO:
                                                <select id="edit-hmo" name="hmo" class="form-control mb-2">
                                                    <option value=""></option>
                                                    <option value="Flexicare">Flexicare</option>
                                                    <option value="Intellicare">Intellicare</option>
                                                    <option value="Avega">Avega</option>
                                                    <option value="Medicard">Medicard</option>
                                                    <option value="Health Partners Dental Access, Inc.">Health Partners
                                                        Dental
                                                        Access, Inc.</option>
                                                    <option value="Dental Network Company">Dental Network Company
                                                    </option>
                                                    <option value="Cocolife">Cocolife</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"
                                                    onclick="updateTreatment()">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>



                            <!-- END OF YOUR ADDITIONAL CODE SNIPPET -->
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include_once('bars/footer.php'); ?>

            <!-- Bootstrap core JavaScript-->
            <script src="vendor/jquery/jquery.min.js"></script>
            <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

            <!-- Core plugin JavaScript-->
            <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

            <!-- Page level plugins -->
            <script src="vendor/datatables/jquery.dataTables.min.js"></script>
            <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

            <!-- Page level custom scripts -->
            <script src="js/demo/datatables-demo.js"></script>

            <!-- Custom scripts for all pages-->
            <script src="js/sb-admin-2.min.js"></script>
            <script src="js/custom-v1.js"></script>
            <script src="controllers/logOutConroller.js"></script>
            <script src="controllers/sessionController.js"></script>
            <script src="controllers/patientChartListController-v1.js"></script>



</body>

</html>