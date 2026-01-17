<?php
require_once('databaseService.php');
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



$query = "SELECT * FROM dctreatmentlist ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo '
                <tr style="color: black;">
              
                <td>' . $row["treatment"] . '</td>
            
                 <td>' . $row["date_added"] . '</td>
               
                <td align="center">

                <a href="#" class="btn btn-warning btn-circle"
   data-toggle="modal"
   data-target="#editTreatmentModal"
   data-treatmentid="' . $row["id"] . '"
   data-genericname="' . htmlspecialchars($row["treatment"]) . '"
   title="Update treatment">
    <i class="fas fa-edit"></i>
</a>

 
                <a href="#" class="btn btn-danger btn-circle" onclick="deleteMedicine(\'' . $row["id"] . '\')" title="Delete Medicine"><i class="fas fa-trash"></i></a>
                
                
                </td>
            </tr>';
            }



        }
    }

}







?>