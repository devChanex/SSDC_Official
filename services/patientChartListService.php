<?php
require_once('databaseService.php');
$service = new ServiceClass();

$clientid = urldecode($_POST['id']);
$result = $service->process($clientid);

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
    public function process($clientid)
    {



        $query = "select a.soaid,tsubid,hmoaccredited,price,date,dentist,treatment,remarks,details,diagnosis from treatmentsoa a inner join treatmentsub b on a.soaid=b.soaid where a.clientid=:a order by Date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':a', $clientid);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo '
                <tr>
                <td>' . date("Y/m/d", strtotime($row["date"])) . '</td>
                <td>' . $row["dentist"] . '</td>
                <td>' . $row["treatment"] . '</td>
                 <td>' . $row["diagnosis"] . '</td>
                <td>' . $row["remarks"] . '</td>
                <td>' . $row["details"] . '</td>';
                $hmo = $row["hmoaccredited"];
                $hmoDisplay = '';

                if (!empty($hmo)) {
                    $parts = explode('|', $hmo);
                    $hmoDisplay = trim($parts[0]);
                }

                echo '  <td>' . $hmoDisplay . '</td>';


                echo '
          <td>' . number_format($row["price"], 2) . '</td>
          
          <td align="center">
  <button class="btn btn-success edit-btn"
    data-soaid="' . $row["soaid"] . '"
     data-tsubid="' . $row["tsubid"] . '"
    data-treatment="' . htmlspecialchars($row["treatment"], ENT_QUOTES) . '"
    data-diagnosis="' . htmlspecialchars($row["diagnosis"], ENT_QUOTES) . '"
    data-remarks="' . htmlspecialchars($row["remarks"], ENT_QUOTES) . '"
    data-details="' . htmlspecialchars($row["details"], ENT_QUOTES) . '"
    data-price="' . $row["price"] . '"
    data-toggle="modal" data-target="#editModal">
    <i class="fas fa-edit"></i>
  </button>

   <button class="btn btn-danger" onclick="deleteTreatment(' . $row["soaid"] . ',' . $row["tsubid"] . ')">
    <i class="fas fa-trash"></i>
  </button>
</td>

                
            </tr>';
            }
        }
    }
}
