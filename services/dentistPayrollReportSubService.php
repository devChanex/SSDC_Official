<?php
require_once('databaseService.php');
$service = new ServiceClass();
$fromdate = urldecode($_POST['from']);
$todate = urldecode($_POST['to']);
$dentist = urldecode($_POST['dentist']);
$result = $service->loadDentistPayroll($fromdate, $todate, $dentist);

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

    public function loadDentistPayroll($fromdate, $todate, $dentist)
    {


        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered text-dark" width="100%" cellspacing="0" id="commissionTable">';
        echo '<thead><tr>';
        echo '<th>Type</th>';
        echo '<th>Particulars</th>';
        echo '<th>Amount</th>';
        echo '</tr></thead><tbody>';

        echo '<tr onclick="openBasicSalaryModal()" style="cursor:pointer;" 
        onmouseover="this.style.backgroundColor=\'#f5f5f5\'" 
        onmouseout="this.style.backgroundColor=\'\'">';
        echo '<td>Basic Salary</td>';
        echo '<td id="basicSalaryDetails">Days Rendered: 0, Rate Per Day: 0.00</td>';
        echo '<td class="text-right" id="basicSalaryAmount">' . number_format(0, 2) . '</td>';
        echo '</tr>';

        $parameters = [];
        $conditions = [];

        if (!empty($dentist)) {
            $conditions[] = 'adj.dentist = :dentist';
            $parameters[':dentist'] = $dentist;
        }

        if (!empty($fromdate) && !empty($todate)) {
            $conditions[] = 'adj.date BETWEEN :fromdate AND :todate';
            $parameters[':fromdate'] = $fromdate;
            $parameters[':todate'] = $todate;
        } elseif (!empty($fromdate)) {
            $conditions[] = 'adj.date >= :fromdate';
            $parameters[':fromdate'] = $fromdate;
        } elseif (!empty($todate)) {
            $conditions[] = 'adj.date <= :todate';
            $parameters[':todate'] = $todate;
        }

        $whereClause = '';
        if (count($conditions) > 0) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        $query = "SELECT adj.type,adj.particular, adj.amount FROM payroll_adjustments adj $whereClause ORDER BY adj.type, adj.date ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($parameters as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $deductions = 0.00;
        $additional = 0.00;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['type'] === 'Deduction') {
                $deductions += $row['amount'];
            } elseif ($row['type'] === 'Additional') {
                $additional += $row['amount'];
            }
            $amount = number_format($row['amount'], 2, '.', '');
            echo '<tr data-amount="' . $amount . '">';
            echo '<td>' . htmlspecialchars($row['type']) . '</td>';
            echo '<td>' . htmlspecialchars($row['particular']) . '</td>';
            echo '<td class="text-right price">' . number_format($row['amount'], 2) . '</td>';
            // commission amount starts at 0.00; only checked rows will get a commission value


            echo '</tr>';
        }

        echo '</tbody>
        
        
        ';

        echo '<tfoot>';
        echo '<tr>';
        echo '<th colspan="2" class="text-right">Basic Salary:</th>';
        echo '<th id="totalBasicSalary" class="text-right">0.00</th>';
        echo '</tr>';

        echo '<tr>';
        echo '<th colspan="2" class="text-right">Total Additional:</th>';
        echo '<th id="totalAdditional" class="text-right">' . number_format($additional, 2) . '</th>';
        echo '</tr>';


        echo '<tr>';
        echo '<th colspan="2" class="text-right">Total Deductions:</th>';
        echo '<th id="totalDeductions" class="text-right">' . number_format($deductions, 2) . '</th>';
        echo '</tr>';

        echo '<tr>';
        echo '<th colspan="2" class="text-right">NetPay:</th>';
        echo '<th id="netpay" class="text-right">0.00</th>';
        echo '</tr>';

        echo '</tfoot>';
        echo '
        </table></div>';

    }
}
