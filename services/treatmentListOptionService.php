<?php
require_once('databaseService.php');
session_start();
$service = new ServiceClass();

$result = $service->process();

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
    public function process()
    {
        $superuser = "ssdc_admin2020";


        $query = "select * from dctreatmentlist order by id desc";
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        echo '<option value="">Select Treatment</option>';
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                
         
                $treatment = htmlspecialchars($row["treatment"]);
                $toothnum  = htmlspecialchars($row["toothnum"]);

                // Combine them in both value and label if needed
                echo '<option value="' . $treatment . '- Tooth # '.$toothnum.'">' . $treatment;

                // Only show tooth number if it’s not empty
                if (!empty($toothnum)) {
                    echo ' - Tooth ' . $toothnum;
                }

                echo '</option>';



            }
        }
    }
}
