
<?php
//Service for login

require_once('databaseService.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
	http_response_code(400);
	echo 'failed';
	exit;
}

$service = new ServiceClass();
if ($service->deleteEvent($id)) {
	echo 'success';
} else {
	echo 'failed';
}

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
	public function deleteEvent($id)
	{
		$query = "DELETE FROM calendar_event_master WHERE event_id = :event_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindValue(':event_id', (int) $id, PDO::PARAM_INT);
		return $stmt->execute() && $stmt->rowCount() > 0;
	}
	//UNTIL THIS CODE

}
//UNTIL HERE COPY



?>