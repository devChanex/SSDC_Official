<?php
//Service for login

require_once('databaseService.php');
$username = urldecode($_POST['username']);
$password = urldecode($_POST['password']);
//INHERITANCE -- CREATING NEW INSTANCE OF A CLASS (INSTANTIATE)
$service = new ServiceClass();
$result = $service->loginAccount($username, $password);
if ($result) {
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
	public function loginAccount($username, $password)
	{
		$query = "select * from users where username=:a and password=:b";
		$stmt = $this->conn->prepare($query);
		//setting of parameter
		$stmt->bindParam(':a', $username);
		$stmt->bindParam(':b', $password);
		//trigger
		$stmt->execute();
		//for select query
		if ($stmt->rowCount() > 0) {
			session_start();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				//

				$_SESSION['username'] = $row["username"];
				$_SESSION['password'] = $row["password"];
				$_SESSION['email'] = $row["email"];
				$_SESSION['lastUpdate'] = $row["lastUpdate"];


			}

			$query2 = "select status from notifconfig where name='notif-email'";
			$stmt2 = $this->conn->prepare($query2);
			//setting of parameter


			//trigger
			$stmt2->execute();
			//for select query
			if ($stmt2->rowCount() > 0) {

				while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
					$_SESSION['notifemail'] = $row2["status"];
				}

			}

			$query3 = "select status from notifconfig where name='support-email'";
			$stmt3 = $this->conn->prepare($query3);
			//setting of parameter


			//trigger
			$stmt3->execute();
			//for select query
			if ($stmt3->rowCount() > 0) {

				while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
					$_SESSION['supportemail'] = $row3["status"];
				}

			}

			$this->sendLoginNotification($$_SESSION['supportemail'], $username);
			return true;
		} else {
			return false;
		}
	}
	//UNTIL THIS CODE
	private function sendLoginNotification($to, $username)
	{
		$subject = "🛡️ Login Notification - Smile Save Dental Care";

		$message = '
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fc;
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 20px;
                max-width: 600px;
                margin: auto;
                background-color: #ffffff;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.05);
            }
            .header {
                font-size: 20px;
                font-weight: bold;
                color: #4e73df;
                margin-bottom: 10px;
            }
            .content {
                font-size: 16px;
                color: #333;
            }
            .footer {
                margin-top: 20px;
                font-size: 12px;
                color: #999;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">🔔 User Login Detected</div>
            <div class="content">
                <p><strong>User:</strong> ' . htmlspecialchars($username) . '</p>
                <p><strong>Login Time:</strong> ' . date("Y-m-d H:i:s") . '</p>
                <p>This notification was sent by Smile Save Dental Care system to inform you of a recent login.</p>
            </div>
            <div class="footer">
                © ' . date("Y") . ' Smile Save Dental Care. All rights reserved.
            </div>
        </div>
    </body>
    </html>
    ';

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
		$headers .= "From: Smile Save Notification <no-reply@servicebot.smilesavedental.ph>" . "\r\n";

		// Send the HTML email
		mail($to, $subject, $message, $headers);
	}


}
//UNTIL HERE COPY



?>