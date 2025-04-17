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
                <div class="container-fluid" style="padding-left:20%;padding-right:20%;">

                    <!-- Page Heading -->
                    <div class="card shadow mb-12">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">HMO Add</h6>
                        </div>
                        <div class="card-body" id="bodyResult">
                            <!-- USE THIS SPACE FOR YOUR ADDITIONAL CODE SNIPPET -->


                            <label for="treatment">Name</label>
                            <input type="Text" name="treatment" id="name" placeholder="Name" class="form-control"
                                value="">
                            <label for="HMO">HMO</label>
                            <select id="hmo" name="hmo" class="form-control mb-2">
                                <option value="">-- Select HMO --</option>
                                <option value="Flexicare">Flexicare</option>
                                <option value="Intellicare">Intellicare</option>
                                <option value="Avega">Avega</option>
                                <option value="Medicard">Medicard</option>
                                <option value="Health Partners Dental Access, Inc.">Health Partners Dental
                                    Access, Inc.</option>
                                <option value="Dental Network Company">Dental Network Company</option>
                                <option value="Cocolife">Cocolife</option>
                            </select>
                            <label for="treatment">Account Number</label>
                            <input type="Text" name="treatment" id="accountnumber" placeholder="Account Number"
                                class="form-control" value="">
                            <label for="treatment">Birthdate</label>
                            <input type="date" name="treatment" id="birthdate" placeholder="Account Number"
                                class="form-control" value="">
                            <label for="treatment">Company</label>
                            <input type="Text" name="company" id="company" placeholder="Company" class="form-control"
                                value="">
                            <label for="treatment">Contact Number</label>
                            <input type="Text" name="treatment" id="contact" placeholder="Contact Number"
                                class="form-control" value="">
                            <label for="validity">Validity:</label>
                            <input type="date" name="treatment" id="validity" placeholder="Account Number"
                                class="form-control" value="">

                            <label for="benefit">Dental Benefits:</label>
                            <textarea id="benefit" style="width: 100%;" class="form-control" rows="4"
                                placeholder="Enter your dental benefits here..."></textarea>

                            <label for="remarks">Remarks:</label>
                            <textarea id="remarks" style="width: 100%;" class="form-control" rows="4"
                                placeholder="Enter remarks here..."></textarea>

                            <div id="formResult"></div>
                            <br>
                            <button class="btn btn-success" onclick="add()">Submit</button>
                            <button class="btn btn-danger" onclick="window.location.href='hmoList.php'">Cancel</button>


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

            <!-- Custom scripts for all pages-->
            <script src="js/sb-admin-2.min.js"></script>
            <script src="controllers/logOutConroller.js"></script>
            <script src="controllers/sessionController.js"></script>
            <script src="controllers/hmoAddController.js"></script>
            <script src="js/custom.js"></script>
</body>

</html>