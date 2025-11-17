<?php
require_once('databaseService.php');
$service = new ServiceClass();
$service->process($_POST);

class ServiceClass
{
    private $conn;
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->conn = $db;
    }

    public function process($data)
    {
        // Match JS FormData keys
        $medid = $data['medid'] ?? '';
        $genericname = $data['genericname'] ?? '';
        $dispense = $data['dispense'] ?? '';
      

        if ($medid != '') {
            // Update existing record
            $query = "UPDATE dctreatmentlist 
                      SET treatment = :a, toothnum = :b
                      WHERE id = :x";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':x', $medid);
        } else {
            // Insert new record with date
            $query = "INSERT INTO dctreatmentlist 
                      (treatment, toothnum, date_added) 
                      VALUES (:a, :b, :d)";
            $stmt = $this->conn->prepare($query);
            $dateAdded = date('Y-m-d H:i:s');
            $stmt->bindParam(':d', $dateAdded);
        }

        $stmt->bindParam(':a', $genericname);
        $stmt->bindParam(':b', $dispense);
        // $stmt->bindParam(':c', $signetur);

        try {
            $stmt->execute();
            echo 'success';
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
?>
