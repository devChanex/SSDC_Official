<?php
session_start();
require_once('databaseService.php');
$service = new ServiceClass();

$search = urldecode($_POST['search']);
$searchParam = '%' . $search . '%';
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$itemPerPage = isset($_POST['item']) ? (int) $_POST['item'] : 10;
$result = $service->process($searchParam, $page, $itemPerPage);

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
    public function process($search, $page, $itemPerPage)
    {
        $offset = ($page - 1) * $itemPerPage;
        $searchFields = ['soadate', 'datesubmitted', 'amount', 'paymentdate', 'hmo'];
        $where = '';

        if (!empty($search)) {
            $orConditions = [];
            foreach ($searchFields as $field) {
                $orConditions[] = "$field LIKE :search";
            }
            $where = 'WHERE (' . implode(' OR ', $orConditions) . ')';
        }

        $query = "SELECT * FROM hmopayment $where ORDER BY paymentdate ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $itemPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '
        <tr style="color: black;">
              <td>' . $row["hmo"] . '</td>
            <td>' . ucwords(strtolower($row["soadate"])) . '</td>
           
            <td>' . ucwords(strtolower($row["datesubmitted"])) . '</td>
            <td style="text-align:right">' . number_format($row["amount"], 2) . '</td>
            <td>' . ucwords(strtolower($row["paymentdate"])) . '</td>
        
          
            <td align="center">
               <button class="btn btn-primary btn-circle edit-btn" data-toggle="modal" data-target="#editExpenseModal"
                data-hmopaymentid="' . htmlspecialchars($row["hmopaymentid"]) . '"
                data-soadate="' . htmlspecialchars($row["soadate"]) . '"
                data-datesubmitted="' . htmlspecialchars($row["datesubmitted"]) . '"
                data-amount="' . htmlspecialchars($row["amount"]) . '"
                data-paymentdate="' . htmlspecialchars($row["paymentdate"]) . '"
                data-hmo="' . htmlspecialchars($row["hmo"]) . '">
                <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-circle edit-btn" data-toggle="modal" data-target="#deleteExpenseModal"
                    data-hmopaymentid="' . htmlspecialchars($row["hmopaymentid"]) . '">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>';
        }
    }


}







?>