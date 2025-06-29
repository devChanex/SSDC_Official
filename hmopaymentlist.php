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
    <link href="css/sortable.css" rel="stylesheet">
    <link href="css/custom-v1.css" rel="stylesheet">

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
                    <div class="card shadow mb-12">
                        <div class="card-header py-3 d-flex justify-content-between <?php echo $cards; ?>">
                            <h6 class="m-0 font-weight-bold">HMO Payment List</h6>


                            <button class="btn btn-success btn-circle edit-btn" data-toggle="modal"
                                data-target="#editExpenseModal">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="card-header py-3 d-flex justify-content-between">
                                <h6 class="m-0 font-weight-bold"></h6>
                                <div class="d-flex align-items-center gap-2 ms-auto">
                                    <strong>Search: </strong><input type="search" id="tableSearch"
                                        class="form-control form-control-sm" placeholder="" style="width: 300px;"
                                        oninput="search();">

                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-dark" id="sortableTable" width="100%"
                                    cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th onclick="sortTable(this)">HMO <span class="sort-icon"></span>
                                            </th>
                                            <th onclick="sortTable(this)">SOA Date <span class="sort-icon"></span></th>

                                            <th onclick="sortTable(this)">Date Submitted <span class="sort-icon"></span>
                                            </th>
                                            <th onclick="sortTable(this)">Payment <span class="sort-icon"></span>
                                            </th>
                                            </th>
                                            <th onclick="sortTable(this)">Date Paid<span class="sort-icon"></span>
                                            </th>

                                            <th>Action</th>
                                            <!-- No onclick, since actions typically aren' t sortable -->



                                        </tr>
                                    </thead>

                                    <tbody id="resultResponseBody">



                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" id="currentPage" value="1">
                            <div id="pagination"></div>






                            <!-- END OF YOUR ADDITIONAL CODE SNIPPET -->
                        </div>

                    </div>
                    <div class="modal fade" id="editExpenseModal" tabindex="-1" role="dialog"
                        aria-labelledby="editExpenseModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">

                            <div class="modal-content">
                                <div class="modal-header <?php echo $cards; ?>">
                                    <h5 class="modal-title" id="editExpenseModalLabel">HMO Payment</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="editExpenseForm">
                                        <input type="hidden" name="expenseid" id="modal-hmopaymentid">
                                        <div class="form-group">
                                            <label>HMO</label>
                                            <select class="form-control" name="mop" id="modal-hmo">
                                                <option value="">-- Select HMO --</option>
                                                <?php
                                                $hmos = ['Flexicare', 'Intellicare', 'Avega', 'Eastwest', 'ValuCare', 'Medicard', 'Health Partners Dental Access, Inc.', 'Dental Network Company', 'Cocolife'];
                                                foreach ($hmos as $hmo) {
                                                    $selected = ($_GET['hmo'] ?? '') == $hmo ? 'selected' : '';
                                                    echo "<option value=\"$hmo\" $selected>$hmo</option>";
                                                }
                                                ?>
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>SOA Date</label>
                                            <input type="text" class="form-control" name="date" id="modal-soadate">
                                        </div>

                                        <div class="form-group">
                                            <label>Date Submitted</label>
                                            <input type="text" class="form-control" name="particular"
                                                id="modal-datesubmitted">
                                        </div>
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="number" step="0.01" class="form-control" name="amount"
                                                id="modal-amount">
                                        </div>
                                        <div class="form-group">
                                            <label>Payment Date</label>
                                            <input type="date" class="form-control" name="description"
                                                id="modal-paymentdate">
                                        </div>



                                    </form>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" onclick="submitCart();">Save
                                        changes</button>
                                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal fade" id="deleteExpenseModal" tabindex="-1" role="dialog"
                        aria-labelledby="editExpenseModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">

                            <div class="modal-content">
                                <div class="modal-header <?php echo $cards; ?>">
                                    <h5 class="modal-title" id="editExpenseModalLabel">HMO Payment Deletion</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="deleteExpenseForm">
                                        <input type="text" name="expenseid" id="modal-delete-hmopaymentid">
                                        Are you sure you want to delete this HMO payment?
                                    </form>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" onclick="deleteCart();">Yes</button>
                                    <button class="btn btn-danger" data-dismiss="modal">No</button>
                                </div>
                            </div>

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
            <script src="js/custom-v2.js"></script>
            <script src="controllers/logOutConroller.js"></script>
            <script src="controllers/sessionController.js"></script>
            <script src="controllers/hmopaymentController.js"></script>

            <script src="js/sortable.js"></script>



</body>

</html>