<?php
session_start();
require_once('databaseService.php');
$service = new ServiceClass();

// Collect POST data
$search     = urldecode($_POST['search'] ?? '');
$searchBy   = $_POST['searchBy'] ?? 'name';   // default to "name" if not provided
$searchParam = '%' . $search . '%';
$page       = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$itemPerPage = isset($_POST['item']) ? (int) $_POST['item'] : 10;

// Process request
$result = $service->process($searchParam, $searchBy, $page, $itemPerPage);

class ServiceClass
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function runQuery($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }

    public function process($search, $searchBy, $page, $itemPerPage)
    {
        $offset = ($page - 1) * $itemPerPage;

        // Define fields for searching
        $searchFields = [
            'hmo',
            'nickname',
            'sex',
            'mobilenumber',
            "CONCAT(lname, ', ', fname, ' ', mdname)"
        ];

        $dynamics = '';

        // Build WHERE condition if search is not empty
        if (!empty(trim($search, '%'))) {
            $orConditions = [];
            foreach ($searchFields as $field) {
                $orConditions[] = "$field LIKE :search";
            }
            $dynamics = 'AND (' . implode(' OR ', $orConditions) . ')';
        }

        // Sorting logic based on searchBy
        if ($searchBy === "dateRegistered") {
            $orderBy = "ORDER BY clientid DESC";
        } else {
            $orderBy = "ORDER BY CONCAT(lname, ', ', fname, ' ', mdname) ASC";
        }

        // Final query
        $query = "
            SELECT * FROM clientprofile a 
            WHERE (status != 'Deleted' OR status IS NULL) $dynamics 
            $orderBy 
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($query);

        // Bind search if used
        if (!empty(trim($search, '%'))) {
            $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        }

        $stmt->bindParam(':limit', $itemPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dob = new DateTime($row["birthDate"]);
                $today = new DateTime();
                $age = $today->diff($dob)->y;
                $fullname = $row["lname"] . ', ' . $row["fname"] . ' ' . $row["mdname"];

                echo '
                <tr style="color: black;">
                    <td>' . ucwords(strtolower($fullname)) . '</td>
                    <td>' . ucwords(strtolower($row["nickname"])) . '</td>
                    <td>' . $age . '</td>
                    <td>' . ucwords(strtolower($row["sex"])) . '</td>
                    <td>' . $row["mobileNumber"] . '</td>
                    <td>' . ucwords($row["hmo"]) . '</td>
                    <td align="center">
                        <a href="updateClient.php?clientid=' . $row["clientid"] . '&lname=' . $row["lname"] . '&fname=' . $row["fname"] . '" 
                           class="btn btn-warning btn-circle" title="Update Client Profile">
                           <i class="fas fa-edit"></i>
                        </a>';

                // Add more action buttons here (same as your original code)...
                  echo '
                       <a href="medHistoryView.php?clientid=' . $row["clientid"] . '&clientname=' . $fullname . '" title="View Medical History"  class="btn btn-secondary  btn-circle"><i class="fas fa-history"></i></a>
                ';

                $query2 = "select b.*,concat(b.lname,', ',b.fname, ' ', b.mdname) as fullname,a.id,a.dentist,a.date from consent a inner join clientprofile b on a.clientid=b.clientid where a.status='Active' and a.clientid=:a order by a.id desc limit 1";
                $stmt2 = $this->conn->prepare($query2);
                $stmt2->bindParam(':a', $row["clientid"]);
                $stmt2->execute();
                if ($stmt2->rowCount() > 0) {

                    while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        echo '<a href="viewConsent.php?dentist=' . $row2["dentist"] . '&date=' . $row2["date"] . '&consentid=' . $row2["id"] . '&civilStatus=' . $row2["civilstatus"] . '&company=' . $row2["company"] . '&cardNumber=' . $row2["cardnumber"] . '&hmo=' . $row2["hmo"] . '&religion=' . $row2["religion"] . '&clientid=' . $row["clientid"] . '&lname=' . $row["lname"] . '&fname=' . $row2["fname"] . '&mname=' . $row2["mdname"] . '&nick=' . $row2["nickname"] . '&age=' . $row2["age"] . '&sex=' . $row["sex"] . '&occupation=' . $row["occupation"] . '&birthDate=' . $row2["birthDate"] . '&mobileNumber=' . $row2["mobileNumber"] . '&homeAddress=' . $row2["homeAddress"] . '&guardianName=' . $row2["guardianName"] . '&gOccupation=' . $row2["gOccupation"] . '&refferedBy=' . $row2["refferedBy"] . '&height=' . $row["height"] . '&weight=' . $row["weight"] . '" class="btn btn-primary btn-circle" title="View Client Consent"><i class="fas fa-file"></i></a>
                        ';
                    }

                }

                $query2 = "select clientId from orthowaiver where clientid=:a limit 1";
                $stmt2 = $this->conn->prepare($query2);
                $stmt2->bindParam(':a', $row["clientid"]);
                $stmt2->execute();
                if ($stmt2->rowCount() > 0) {

                    while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        echo '<a href="viewOrthoWaiver.php?clientId=' . $row2["clientId"] . '" class="btn bg-purple btn-circle" title="View Orthodontics Waiver"><i class="fas fa-sign"></i></a>
                        ';
                    }

                }


                echo '
                <a href="addTreatmentHistory.php?company=' . $row["company"] . '&cardnumber=' . $row["cardnumber"] . '&hmo=' . $row["hmo"] . '&clientid=' . $row["clientid"] . '&birthDate=' . $row["birthDate"] . '&clientname=' . $fullname . '&age=' . $age . '&address=' . $row["homeAddress"] . '" class="btn btn-warning btn-circle" title="Add Treatment"><i class="fas fa-plus"></i></a>


                  <a href="patientChartList.php?id=' . $row["clientid"] . '&clientname=' . $fullname . '"
                 class="btn btn-success btn-circle" title="View Patient Chart"><i class="fas fa-chart-bar"></i></a>

                      
                       ';

                if ($_SESSION["account_type"] == 0 || $_SESSION["account_type"] == 100) {
                    echo '
                        <a href="#" class="btn btn-danger btn-circle" 
                           onclick="deleteClient(\'' . $row["clientid"] . '\')" 
                           title="Delete Client Profile">
                           <i class="fas fa-trash"></i>
                        </a>';
                }

                echo '</td></tr>';
            }
        }
    }
}
?>
