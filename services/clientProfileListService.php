<?php
require_once('databaseService.php');
$service = new ServiceClass();
$result = $service->loadClientProfile();

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
    public function loadClientProfile()
    {



        $query = "select * from clientProfile";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $fullname = $row["lname"] . ', ' . $row["fname"] . ' ' . $row["mdname"];
                echo '
<tr>
                <td>' . $row["clientid"] . '</td>
                <td>' . $fullname . '</td>
                <td>' . $row["nickname"] . '</td>
                <td>' . $row["age"] . '</td>
                <td>' . $row["sex"] . '</td>
                 <td>' . $row["religion"] . '</td>
                  <td>' . $row["civilstatus"] . '</td>
                <td>' . $row["occupation"] . '</td>
                <td>' . $row["birthDate"] . '</td>
                <td>' . $row["mobileNumber"] . '</td>
                <td>' . $row["homeAddress"] . '</td>
                <td>' . $row["guardianName"] . '</td>
                <td>' . $row["gOccupation"] . '</td>
                <td>' . $row["refferedBy"] . '</td>
               
                <td>';

                //medhistory checker

                $query2 = "select * from medhistory where clientid=:a";
                $stmt2 = $this->conn->prepare($query2);
                $stmt2->bindParam(':a', $row["clientid"]);
                $stmt2->execute();
                if ($stmt2->rowCount() > 0) {

                    echo '
                       <a href="medHistoryView.php?clientid=' . $row["clientid"] . '&clientname=' . $fullname . '" title="View Medical History"  class="btn btn-primary  btn-circle"><i class="fas fa-history"></i></a>
                       ';

                } else {
                    echo ' <a href="medHistory.php?clientid=' . $row["clientid"] . '&clientname=' . $fullname . '" title="Add Medical History" class="btn btn-success btn-circle"><i class="fas fa-history"></i></a>
                    ';

                }

                $imgBase64 = base64_encode($row["photo"]);
                echo '
                <a href="updateClient.php?clientid=' . $row["clientid"] . '&lname=' . $row["lname"] . '&fname=' . $row["fname"] . '
                &mname=' . $row["mdname"] . '&nick=' . $row["nickname"] . '&age=' . $row["age"] . '&sex=' . $row["sex"] . '&occupation=' . $row["occupation"] . '
                &birthDate=' . $row["birthDate"] . '&mobileNumber=' . $row["mobileNumber"] . '&homeAddress=' . $row["homeAddress"] . '
                &guardianName=' . $row["guardianName"] . '&gOccupation=' . $row["gOccupation"] . '&refferedBy=' . $row["refferedBy"] . '
                " class="btn btn-warning btn-circle" title="Update Client Profile"><i class="fas fa-edit"></i></a>
                <a href="#" class="btn btn-danger btn-circle" onclick="deleteClient(\'' . $row["clientid"] . '\')" title="Delete Client Profile"><i class="fas fa-trash"></i></a>

                ';


                if ($row["photo"] != null) {
                    echo '<a href="#" class="btn btn-primary btn-circle" 
   data-toggle="modal" 
   data-target="#ViewModal" 
   data-img="data:image/jpeg;base64,' . $imgBase64 . '" 
   onclick="showProfilePhoto(this)" 
   title="View Client Profile">
   <i class="fas fa-eye"></i>
</a>';
                }
                echo '
                
                
                </td>
            </tr>';
            }

        }
    }

}







?>