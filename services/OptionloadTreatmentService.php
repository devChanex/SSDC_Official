<?php
require_once('databaseService.php');
$service = new ServiceClass();
$result = $service->loadTreatmentList();

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
    public function loadTreatmentList(){
       


        $query = "select treatment from treatmentlist where status='Active' order by treatment";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
               
                $treatment = htmlspecialchars($row["treatment"], ENT_QUOTES, 'UTF-8');
                echo '<option value="' . $treatment . '">' . $treatment . '</option>';
                
            }
       
		} else {
              echo'<option>No Treatment Available</option>';




		}
    }

}

	





?>