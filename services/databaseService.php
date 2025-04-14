<?php
header('Access-Control-Allow-Origin: *');
?>
<?php
date_default_timezone_set("Asia/Manila");
class Database
{
    //local
    private $host = "localhost";
    private $db_name = "ssdc_sysdb";
    private $username = "root";
    private $password = '';

    public $conn;

    public function dbConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
        //include_once 'class.crud.php';

        //$crud = new crud($conn);
    }
}

?>