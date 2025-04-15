<?php
//Service for Registration

require_once('databaseService.php');

$dentistbase64 = $_POST['dentistSignature'];
// Remove the metadata from base64
$dentistbase64 = str_replace('data:image/png;base64,', '', $dentistbase64);
$dentistbase64 = str_replace(' ', '+', $dentistbase64);

$dentistSignature = base64_decode($dentistbase64);


$patientbase64 = $_POST['patientSignature'];
// Remove the metadata from base64
$patientbase64 = str_replace('data:image/png;base64,', '', $patientbase64);
$patientbase64 = str_replace(' ', '+', $patientbase64);

$patientSignature = base64_decode($patientbase64);
$dateSigned = urldecode($_POST['dateSigned']);
$dentistName = urldecode($_POST['dentistName']);
$clientId = urldecode($_POST['clientId']);
//echo'<script>alert("tesT");</script>';
//INHERITANCE -- CREATING NEW INSTANCE OF A CLASS (INSTANTIATE)
$service = new ServiceClass();
$result = $service->addTreatment($dentistSignature, $patientSignature, $dateSigned, $dentistName, $clientId);
echo $result;
//USE THIS AS YOUR BASIS
class ServiceClass
{

    private $conn;
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->conn = $db;
    }

    public function runQuery($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
    public function addTreatment($dentistSignature, $patientSignature, $dateSigned, $dentistName, $clientId)
    {
        //:a,:b parameter
        try {

            $query = "Insert into consent (clientId,clientSignature,dentistSignature,date,dentist,status) values (:a,:b,:c,:d,:e,'Active')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':a', $clientId);
            $stmt->bindParam(':b', $patientSignature);
            $stmt->bindParam(':c', $dentistSignature);
            $stmt->bindParam(':d', $dateSigned);
            $stmt->bindParam(':e', $dentistName);

            $stmt->execute();
            return "success";
        } catch (Exception $e) {
            return "Error:" . $e->getMessage();
        }



    }
    //UNTIL THIS CODE

}
//UNTIL HERE COPY



?>