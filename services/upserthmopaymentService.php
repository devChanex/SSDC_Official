<?php
session_start();
require_once('databaseService.php');
$service = new ServiceClass();

$result = $service->process($_POST);

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
    //DO NOT INCLUDE THIS CODE
    public function process($data)
    {

        $hmopaymentid = $data['modal-hmopaymentid'];
        $soadate = $data['modal-soadate'];
        $datesubmitted = $data['modal-datesubmitted'];
        $amount = $data['modal-amount'];
        $paymentdate = $data['modal-paymentdate'];
        $hmo = $data['modal-hmo'];
        $query = "";

        if ($hmopaymentid != '') {
            $query = "update hmopayment set soadate=:soadate,datesubmitted=:datesubmitted,amount=:amount,paymentdate=:paymentdate,hmo=:hmo where hmopaymentid=:hmopaymentid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hmopaymentid', $hmopaymentid, PDO::PARAM_INT);
        } else {
            $query = "INSERT INTO hmopayment (soadate,datesubmitted,amount,paymentdate,hmo) VALUES (:soadate,:datesubmitted,:amount,:paymentdate,:hmo)";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->bindParam(':soadate', $soadate);
        $stmt->bindParam(':datesubmitted', $datesubmitted);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':paymentdate', $paymentdate);
        $stmt->bindParam(':hmo', $hmo);
        $stmt->execute();
        echo 'success';



    }

}










?>