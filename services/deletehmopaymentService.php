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

        $hmopaymentid = $data['modal-delete-hmopaymentid'];
        $query = "delete from hmopayment where hmopaymentid=:hmopaymentid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hmopaymentid', $hmopaymentid, PDO::PARAM_INT);

        $stmt->execute();
        echo 'success';



    }

}










?>