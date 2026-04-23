<?php
require_once('databaseService.php');
$soaid = urldecode($_POST['soaid']);
$service = new ServiceClass();
$result = $service->deleteSoa($soaid);
echo $result;

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
	public function deleteSoa($soaid)
	{

		try {
			$query = "delete from treatmentsoa where soaid =:a";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':a', $soaid);
			$stmt->execute();
			return 'success';
		} catch (Exception $e) {
			return 'error';
		}

	}




}


?>