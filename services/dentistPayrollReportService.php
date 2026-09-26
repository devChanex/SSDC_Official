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
        $parameters = [];
        $conditions = [];

        if (!empty($dentist)) {
            $conditions[] = 'tsoa.dentist = :dentist';
            $parameters[':dentist'] = $dentist;
        }

        if (!empty($fromdate) && !empty($todate)) {
            $conditions[] = 'tsoa.date BETWEEN :fromdate AND :todate';
            $parameters[':fromdate'] = $fromdate;
            $parameters[':todate'] = $todate;
        } elseif (!empty($fromdate)) {
            $conditions[] = 'tsoa.date >= :fromdate';
            $parameters[':fromdate'] = $fromdate;
        } elseif (!empty($todate)) {
            $conditions[] = 'tsoa.date <= :todate';
            $parameters[':todate'] = $todate;
        }

        $whereClause = '';
        if (count($conditions) > 0) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        $query = "SELECT tsoa.soaid,ts.treatment,ts.price,ts.commision_rate,ts.commision,ts.hmo,tsoa.date,(select concat(lname,', ',fname,' ',mdname) from clientprofile where clientid=tsoa.clientid) as fullname  FROM treatmentsub ts inner join treatmentsoa tsoa ON  ts.soaid = tsoa.soaid $whereClause";

        $stmt = $this->conn->prepare($query);
        foreach ($parameters as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            echo '<div class="alert alert-info">No payment records found for the selected dentist and date range.</div>';
            return;
        }

        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered text-dark" width="100%" cellspacing="0" id="commissionTable">';
        echo '<thead><tr>';
        echo '<th>SOAID</th>';
        echo '<th>Date</th>';
        echo '<th>Patient</th>';
        echo '<th>HMO</th>';
        echo '<th>Treatment</th>';
        echo '<th>Treatment Fee</th>';
        echo '<th>Commision %</th>';
        echo '<th>Commission Amount</th>';
        echo '</tr></thead><tbody>';
        $count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $amount = number_format($row['price'], 2, '.', '');
            echo '<tr data-amount="' . $amount . '">';
            echo '<td>' . htmlspecialchars($row['soaid']) . '</td>';
            echo '<td>' . htmlspecialchars(date('Y/m/d', strtotime($row['date']))) . '</td>';

            echo '<td>' . htmlspecialchars($row['fullname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['hmo']) . '</td>';
            echo '<td>' . htmlspecialchars($row['treatment']) . '</td>';
            echo '<td class="text-right price">' . number_format($row['price'], 2) . '</td>';
            // commission amount starts at 0.00; only checked rows will get a commission value

            echo '<td>';
            echo '<select name="commision_rate" class="form-select"  style="width:100%; box-sizing:border-box;border:0px;font-size:inherit; padding:0px; background-color:transparent;" onchange="updateComissionAmount(this);">';

            for ($rate = 0; $rate <= 50; $rate += 5) {
                $selected = ($row['commision_rate'] == $rate) ? 'selected' : '';

                echo '<option value="' . $rate . '" ' . $selected . '>' . $rate . '%</option>';
            }
            echo '<option value="Other">Other</option>';
            echo '</select>';
            echo '</td>';
            echo '<td class="text-right commission">' . number_format($row['commision'], 2) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>
        
        
        ';

        echo '<tfoot>';
        echo '<tr>';
        echo '<th colspan="7" class="text-right">Total Commision:</th>';
        echo '<th id="totalCommission" class="text-right">0.00</th>';
        echo '</tr>';

        echo '<tr>';
        echo '<th colspan="7" class="text-right">less 10% (Tax and Material Costs):</th>';
        echo '<th id="totalGross" class="text-right">0.00</th>';
        echo '</tr>';
        echo '</tfoot>';
        echo '
        </table></div>';

    }
}
