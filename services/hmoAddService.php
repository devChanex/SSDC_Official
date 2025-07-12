<?php
//Service for Registration

require_once('databaseService.php');
$name = urldecode($_POST['name']);
$accountnumber = urldecode($_POST['accountnumber']);
$birthdate = urldecode($_POST['birthdate']);
$company = urldecode($_POST['company']);
$contact = urldecode($_POST['contact']);
$hmo = urldecode($_POST['hmo']);
$validity = urldecode($_POST['validity']);
$benefit = urldecode($_POST['benefit']);
$remarks = urldecode($_POST['remarks']);
$verification = urldecode($_POST['verification']);
$agent = urldecode($_POST['agent']);
$approvalCode = urldecode($_POST['approvalCode']);
$calledby = urldecode($_POST['calledby']);
$hmotype = urldecode($_POST['hmotype']);

//echo'<script>alert("tesT");</script>';
//INHERITANCE -- CREATING NEW INSTANCE OF A CLASS (INSTANTIATE)
$service = new ServiceClass();
$result = $service->process($name, $accountnumber, $birthdate, $company, $contact, $hmo, $validity, $benefit, $remarks, $verification, $agent, $hmotype, $approvalCode, $calledby);
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
    public function process($name, $accountnumber, $birthdate, $company, $contact, $hmo, $validity, $benefit, $remarks, $verification, $agent, $hmotype, $approvalCode, $calledby)
    {
        //:a,:b parameter
        try {

            $query = "Insert into hmo (accountnumber, hmo, name, dob, company, contact, status,validity,dentalbenefits,remarks,verificationStatus,agent,hmotype,approvalcode,calledby) values (:a,:b,:c,:d,:e,:f,'Active',:g,:h,:i,:j,:k,:l,:m,:n)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':a', $accountnumber);
            $stmt->bindParam(':b', $hmo);
            $stmt->bindParam(':c', $name);
            $stmt->bindParam(':d', $birthdate);
            $stmt->bindParam(':e', $company);
            $stmt->bindParam(':f', $contact);
            $stmt->bindParam(':g', $validity);
            $stmt->bindParam(':h', $benefit);
            $stmt->bindParam(':i', $remarks);
            $stmt->bindParam(':j', $verification);
            $stmt->bindParam(':k', $agent);
            $stmt->bindParam(':l', $hmotype);
            $stmt->bindParam(':m', $approvalCode);
            $stmt->bindParam(':n', $calledby);
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