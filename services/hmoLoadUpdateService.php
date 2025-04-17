<?php
require_once('databaseService.php');
$service = new ServiceClass();
$id = urldecode($_POST['id']);
$result = $service->process($id);

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
    public function process($id)
    {



        $query = "select * from hmo where status='Active' and id=:a";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':a', $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo '
                 <label for="treatment">Name</label>
                            <input type="Text" name="treatment" id="name" placeholder="Name" class="form-control"
                                value="' . $row["name"] . '">
                            <label for="HMO">HMO</label>
                            <select id="hmo" name="hmo" class="form-control mb-2">';
                $hmos = ['Flexicare', 'Intellicare', 'Avega', 'Medicard', 'Health Partners Dental Access, Inc.', 'Dental Network Company', 'Cocolife'];
                foreach ($hmos as $hmo) {
                    $selected = ($row["hmo"] ?? '') == $hmo ? 'selected' : '';
                    echo "<option value=\"$hmo\" $selected>$hmo</option>";
                }
                echo '
                            </select>
                            <label for="treatment">Account Number</label>
                            <input type="Text" name="treatment" id="accountnumber" placeholder="Account Number"
                                class="form-control" value="' . $row["accountnumber"] . '">
                            <label for="treatment">Birthdate</label>
                            <input type="date" name="treatment" id="birthdate" 
                                class="form-control" value="' . $row['dob'] . '">
                            <label for="treatment">Company</label>
                            <input type="Text" name="company" id="company" placeholder="Company" class="form-control"
                                value="' . $row["company"] . '">
                            <label for="treatment">Contact Number</label>
                            <input type="Text" name="treatment" id="contact" placeholder="Contact Number"
                                class="form-control" value="' . $row["contact"] . '">

                            <div id="formResult"></div>
                            <br>
                            <button class="btn btn-success" onclick="update()">Submit</button>
                            <button class="btn btn-danger" onclick="window.location.href=\'hmoList.php\'">Cancel</button>

           ';
            }

        } else {
            echo 'No Result Found for Treatment Id: ' . $id;

        }
    }

}







?>