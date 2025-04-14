<?php
//Service for Registration

require_once('databaseService.php');
$dentist = urldecode($_POST['dentist']);
$date = urldecode($_POST['date']);
$time = urldecode($_POST['time']);
$clientid = urldecode($_POST['clientid']);
$total = urldecode($_POST['total']);
$service = new ServiceClass();
$result = $service->submitEsoa($dentist,$date,$time,$clientid,$total);
echo $result;
//USE THIS AS YOUR BASIS
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
	public function submitEsoa($dentist,$date,$time,$clientid,$total)
	{
		try{
		$query = "Insert into treatmentsoa(date,time,clientid,dentist,total) values (:a,:b,:c,:d,:e)";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':a', $date);
		$stmt->bindParam(':b', $time);
		$stmt->bindParam(':c', $clientid);
		$stmt->bindParam(':d', $dentist);
		$stmt->bindParam(':e', $total);
		$stmt->execute();
		

		$query = "select max(soaid) as lastsoaid from treatmentsoa";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				return $row["lastsoaid"];
			}
		
		}
		

		

		
		}catch(Exception $e){
		return "Error:".$e->getMessage();
		}



	}
	//UNTIL THIS CODE

}
//UNTIL HERE COPY



?>