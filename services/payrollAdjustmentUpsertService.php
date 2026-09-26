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

        $dataid = $data['modal-padjid'];
        $dentist = $data['modal-dentist'];
        $particular = $data['modal-particular'];
        $type = $data['modal-type'];
        $amount = $data['modal-amount'];
        $date = $data['modal-date'];
        $query = "";

        if ($dataid != '') {
            $query = "update payroll_adjustments set particular=:particular,type=:type,amount=:amount,date=:date,dentist=:dentist where padjid=:padjid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':padjid', $dataid, PDO::PARAM_INT);
        } else {
            $query = "INSERT INTO payroll_adjustments (particular,type,amount,date,dentist) VALUES (:particular,:type,:amount,:date,:dentist)";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->bindParam(':particular', $particular);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':dentist', $dentist);
        $stmt->execute();
        echo 'success';



    }

}










?>