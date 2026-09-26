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

        $padjid = $data['modal-deleteid'];

        $query = "delete from payroll_adjustments where padjid=:padjid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':padjid', $padjid, PDO::PARAM_INT);

        $stmt->execute();
        echo 'success';



    }

}










?>