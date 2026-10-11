<?php
require_once __DIR__ . '/databaseService.php';

function transactionSummaryEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function transactionSummaryMoney($value)
{
    return number_format((float) $value, 2);
}

try {
    ob_start();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        throw new InvalidArgumentException('This report endpoint accepts POST requests only.');
    }

    $fromdate = isset($_POST['from']) ? trim($_POST['from']) : '';
    $group = isset($_POST['group']) ? $_POST['group'] : '';
    $page = isset($_POST['page']) ? filter_var($_POST['page'], FILTER_VALIDATE_INT) : 1;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromdate)) {
        throw new InvalidArgumentException('Choose a valid report date.');
    }
    $parsedDate = DateTime::createFromFormat('!Y-m-d', $fromdate);
    if (!$parsedDate || $parsedDate->format('Y-m-d') !== $fromdate) {
        throw new InvalidArgumentException('Choose a valid report date.');
    }
    if (!in_array($group, array('Dentist', 'Patient', 'SOAID'), true)) {
        throw new InvalidArgumentException('Choose a valid grouping option.');
    }
    if ($page === false || $page < 1) {
        throw new InvalidArgumentException('The requested page number is invalid.');
    }

    $database = new Database();
    $conn = $database->dbConnection();
    if (!$conn instanceof PDO) {
        throw new RuntimeException('The report database connection could not be established.');
    }

    $groupExpression = 'tsoa.soaid';
    if ($group === 'Dentist') {
        $groupExpression = 'tsoa.dentist';
    } elseif ($group === 'Patient') {
        $groupExpression = 'CONCAT(cp.lname, ", ", cp.fname, " ", cp.mdname)';
    }

    $pageSize = 100;
    $baseJoins = ' FROM clientprofile cp
        INNER JOIN treatmentsub tsub ON tsub.clientid = cp.clientid
        INNER JOIN treatmentsoa tsoa ON tsoa.soaid = tsub.soaid ';

    $countSql = 'SELECT COUNT(*) FROM (
        SELECT tsub.tsubid' . $baseJoins . 'WHERE tsoa.date = ?
        UNION ALL
        SELECT tsub.tsubid' . $baseJoins . '
        WHERE tsoa.date < ? AND EXISTS (
            SELECT 1 FROM treatmentsubpayment tsp
            WHERE tsp.tsubid = tsub.tsubid AND tsp.paymentDate = ?
        )
    ) report_records';
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute(array($fromdate, $fromdate, $fromdate));
    $totalRecords = (int) $countStmt->fetchColumn();
    $totalPages = max(1, (int) ceil($totalRecords / $pageSize));
    $page = min((int) $page, $totalPages);
    $offset = ($page - 1) * $pageSize;

    $recordsSql = 'SELECT activity.*, tsp.amount AS payment_amount,
            tsp.paymenttype AS payment_type, tsp.paymentDate AS payment_date
        FROM (
            SELECT * FROM (
                SELECT ' . $groupExpression . ' AS group_label, tsoa.soaid, cp.clientid,
                    tsub.hmo, cp.lname, cp.fname, cp.mdname, tsoa.dentist, tsub.tsubid,
                    tsub.treatment, tsub.price, tsoa.date AS soa_date, 1 AS is_new
                ' . $baseJoins . 'WHERE tsoa.date = ?
                UNION ALL
                SELECT ' . $groupExpression . ' AS group_label, tsoa.soaid, cp.clientid,
                    tsub.hmo, cp.lname, cp.fname, cp.mdname, tsoa.dentist, tsub.tsubid,
                    tsub.treatment, tsub.price, tsoa.date AS soa_date, 0 AS is_new
                ' . $baseJoins . '
                WHERE tsoa.date < ? AND EXISTS (
                    SELECT 1 FROM treatmentsubpayment day_payment
                    WHERE day_payment.tsubid = tsub.tsubid AND day_payment.paymentDate = ?
                )
            ) all_activities
            ORDER BY group_label, soaid, tsubid
            LIMIT ? OFFSET ?
        ) activity
        LEFT JOIN treatmentsubpayment tsp ON tsp.tsubid = activity.tsubid
            AND tsp.paymentDate <= ?
            AND (activity.is_new = 1 OR tsp.paymentDate = ?)
        ORDER BY activity.group_label, activity.soaid, activity.tsubid, tsp.paymentDate';
    $recordsStmt = $conn->prepare($recordsSql);
    $recordsStmt->bindValue(1, $fromdate, PDO::PARAM_STR);
    $recordsStmt->bindValue(2, $fromdate, PDO::PARAM_STR);
    $recordsStmt->bindValue(3, $fromdate, PDO::PARAM_STR);
    $recordsStmt->bindValue(4, $pageSize, PDO::PARAM_INT);
    $recordsStmt->bindValue(5, $offset, PDO::PARAM_INT);
    $recordsStmt->bindValue(6, $fromdate, PDO::PARAM_STR);
    $recordsStmt->bindValue(7, $fromdate, PDO::PARAM_STR);
    $recordsStmt->execute();

    $records = array();
    while ($row = $recordsStmt->fetch(PDO::FETCH_ASSOC)) {
        $recordKey = (string) $row['tsubid'];
        if (!isset($records[$recordKey])) {
            $records[$recordKey] = array(
                'group_label' => $row['group_label'],
                'soaid' => $row['soaid'],
                'clientid' => $row['clientid'],
                'hmo' => $row['hmo'],
                'lname' => $row['lname'],
                'fname' => $row['fname'],
                'mdname' => $row['mdname'],
                'dentist' => $row['dentist'],
                'tsubid' => $row['tsubid'],
                'treatment' => $row['treatment'],
                'price' => $row['price'],
                'soa_date' => $row['soa_date'],
                'is_new' => (int) $row['is_new'],
                'payments' => array()
            );
        }
        if ($row['payment_date'] !== null) {
            $records[$recordKey]['payments'][] = array(
                'amount' => $row['payment_amount'],
                'type' => $row['payment_type'],
                'date' => $row['payment_date']
            );
        }
    }

    $paymentsStmt = $conn->prepare('SELECT paymenttype, SUM(amount) AS total
        FROM treatmentsubpayment WHERE paymentDate = ? GROUP BY paymenttype ORDER BY paymenttype');
    $paymentsStmt->execute(array($fromdate));
    $paymentTypes = $paymentsStmt->fetchAll(PDO::FETCH_ASSOC);

    $expensesStmt = $conn->prepare('SELECT particular, description, mop, amount
        FROM expenses WHERE date = ?');
    $expensesStmt->execute(array($fromdate));
    $expenses = $expensesStmt->fetchAll(PDO::FETCH_ASSOC);

    $currentGroup = null;
    $groupFeeTotal = 0;
    $groupPaymentTotal = 0;
    $groupOtherPayments = 0;

    echo '<div class="table-responsive"><table class="table text-dark" width="100%" cellspacing="0">
        <thead><tr>
            <th>SOA ID</th><th>Full Name</th><th>HMO</th><th>Dentist</th>
            <th>Treatment</th><th>Total Fee</th><th>SOA Date</th><th>Payment</th>
            <th>Payment Type</th><th>Payment Date</th><th>Balance</th>
        </tr></thead><tbody>';

    foreach ($records as $row) {
        $groupLabel = (string) $row['group_label'];
        if ($currentGroup !== $groupLabel) {
            if ($currentGroup !== null) {
                echo '<tr><td colspan="5"><strong>Page subtotal</strong></td><td class="text-right"><strong>'
                    . transactionSummaryMoney($groupFeeTotal) . '</strong></td><td></td><td class="text-right"><strong>'
                    . transactionSummaryMoney($groupPaymentTotal) . '</strong></td><td></td><td></td><td class="text-right"><strong>'
                    . transactionSummaryMoney($groupFeeTotal - ($groupPaymentTotal - $groupOtherPayments)) . '</strong></td></tr>';
            }
            $currentGroup = $groupLabel;
            $groupFeeTotal = 0;
            $groupPaymentTotal = 0;
            $groupOtherPayments = 0;
            echo '<tr><td colspan="11"><h4>' . transactionSummaryEscape($group) . ': '
                . transactionSummaryEscape($groupLabel) . '</h4></td></tr>';
        }

        $paymentAmounts = array();
        $paymentTypesForRow = array();
        $paymentDates = array();
        $rowPayments = 0;
        foreach ($row['payments'] as $payment) {
            $paymentAmounts[] = transactionSummaryMoney($payment['amount']);
            $paymentTypesForRow[] = transactionSummaryEscape($payment['type']);
            $paymentDates[] = transactionSummaryEscape($payment['date']);
            $rowPayments += (float) $payment['amount'];
        }

        $isNew = $row['is_new'] === 1;
        $fee = $isNew ? (float) $row['price'] : 0;
        if (!$isNew) {
            $groupOtherPayments += $rowPayments;
        }
        $groupFeeTotal += $fee;
        $groupPaymentTotal += $rowPayments;

        $fullName = trim((string) $row['lname'] . ', ' . (string) $row['fname'] . ' ' . (string) $row['mdname']);
        $soaDate = date('Y/m/d', strtotime($row['soa_date']));
        echo '<tr style="color:black;">
            <td>' . transactionSummaryEscape($row['soaid']) . '</td>
            <td>' . transactionSummaryEscape($fullName) . '</td>
            <td>' . (empty($row['hmo']) ? '-' : transactionSummaryEscape($row['hmo'])) . '</td>
            <td>' . transactionSummaryEscape($row['dentist']) . '</td>
            <td>' . transactionSummaryEscape($row['treatment']) . '</td>
            <td class="text-right">' . ($isNew ? transactionSummaryMoney($row['price']) : '-') . '</td>
            <td>' . transactionSummaryEscape($soaDate) . '</td>
            <td class="text-right">' . ($paymentAmounts ? implode('<br>', $paymentAmounts) : '0.00') . '</td>
            <td class="text-center">' . ($paymentTypesForRow ? implode('<br>', $paymentTypesForRow) : '-') . '</td>
            <td class="text-right">' . ($paymentDates ? implode('<br>', $paymentDates) : '-') . '</td>
            <td class="text-right">' . ($isNew ? transactionSummaryMoney((float) $row['price'] - $rowPayments) : '-') . '</td>
        </tr>';
    }
    if ($currentGroup !== null) {
        echo '<tr><td colspan="5"><strong>Page subtotal</strong></td><td class="text-right"><strong>'
            . transactionSummaryMoney($groupFeeTotal) . '</strong></td><td></td><td class="text-right"><strong>'
            . transactionSummaryMoney($groupPaymentTotal) . '</strong></td><td></td><td></td><td class="text-right"><strong>'
            . transactionSummaryMoney($groupFeeTotal - ($groupPaymentTotal - $groupOtherPayments)) . '</strong></td></tr>';
    }
    if (!$records) {
        echo '<tr><td colspan="11" class="text-center">No transactions found for this date.</td></tr>';
    }
    echo '</tbody></table></div>';

    $totalPages = max(1, $totalPages);
    echo '<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">'
        . '<span>Showing ' . ($totalRecords ? ($offset + 1) : 0) . '–' . min($offset + $pageSize, $totalRecords)
        . ' of ' . $totalRecords . ' transactions</span><nav aria-label="Report pages"><ul class="pagination mb-0">';
    if ($page > 1) {
        echo '<li class="page-item"><button class="page-link" type="button" onclick="getclientdata(' . ($page - 1) . ')">Previous</button></li>';
    } else {
        echo '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    echo '<li class="page-item disabled"><span class="page-link">Page ' . $page . ' of ' . $totalPages . '</span></li>';
    if ($page < $totalPages) {
        echo '<li class="page-item"><button class="page-link" type="button" onclick="getclientdata(' . ($page + 1) . ')">Next</button></li>';
    } else {
        echo '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    echo '</ul></nav></div>';

    $grandTotal = 0;
    $grandPayments = 0;
    $grandOtherPayments = 0;
    $aggregateSql = 'SELECT totals.group_label, SUM(totals.fee_total) AS fee_total,
            SUM(totals.payment_total) AS payment_total,
            SUM(totals.other_payment_total) AS other_payment_total
        FROM (
            SELECT ' . $groupExpression . ' AS group_label, SUM(tsub.price) AS fee_total,
                0 AS payment_total, 0 AS other_payment_total
            ' . $baseJoins . 'WHERE tsoa.date = ?
            GROUP BY ' . $groupExpression . '
            UNION ALL
            SELECT ' . $groupExpression . ' AS group_label, 0 AS fee_total,
                SUM(tsp.amount) AS payment_total,
                SUM(CASE WHEN tsoa.date < ? THEN tsp.amount ELSE 0 END) AS other_payment_total
            ' . $baseJoins . '
            INNER JOIN treatmentsubpayment tsp ON tsp.tsubid = tsub.tsubid
            WHERE (tsoa.date = ? AND tsp.paymentDate <= ?)
                OR (tsoa.date < ? AND tsp.paymentDate = ?)
            GROUP BY ' . $groupExpression . '
        ) totals GROUP BY totals.group_label';
    $aggregateStmt = $conn->prepare($aggregateSql);
    $aggregateStmt->execute(array($fromdate, $fromdate, $fromdate, $fromdate, $fromdate, $fromdate));
    while ($aggregate = $aggregateStmt->fetch(PDO::FETCH_ASSOC)) {
        $grandTotal += (float) $aggregate['fee_total'];
        $grandPayments += (float) $aggregate['payment_total'];
        $grandOtherPayments += (float) $aggregate['other_payment_total'];
    }

    echo '<div style="margin-top:20px; padding:15px; border:1px solid #ccc; background:#f9f9f9; border-radius:8px;">
        <h5 class="text-center"><strong>Summary Report</strong></h5>
        <div class="d-flex justify-content-between"><strong>Grand Total:</strong><span>₱'
        . transactionSummaryMoney($grandTotal) . '</span></div><strong>Mode of Payment:</strong>';
    foreach ($paymentTypes as $paymentType) {
        echo '<div class="d-flex justify-content-between"><strong>'
            . transactionSummaryEscape($paymentType['paymenttype']) . ':</strong><span>₱'
            . transactionSummaryMoney($paymentType['total']) . '</span></div>';
    }
    echo '<hr><div class="d-flex justify-content-between"><strong>Total Payments:</strong><span>₱'
        . transactionSummaryMoney($grandPayments) . '</span></div>
        <div class="d-flex justify-content-between"><strong>Outstanding Balance:</strong><span>₱'
        . transactionSummaryMoney($grandTotal - $grandPayments + $grandOtherPayments) . '</span></div>
        <h5 class="text-center mt-4"><strong>Expense Summary</strong></h5>
        <div class="d-flex justify-content-between"><strong>Particular</strong><strong>Description</strong>
            <strong>Mode of Payment</strong><strong>Amount</strong></div>';

    $totalExpenses = 0;
    foreach ($expenses as $expense) {
        $totalExpenses += (float) $expense['amount'];
        echo '<div class="d-flex justify-content-between"><span>' . transactionSummaryEscape($expense['particular'])
            . '</span><span>' . transactionSummaryEscape($expense['description'])
            . '</span><span>' . transactionSummaryEscape($expense['mop'])
            . '</span><span>₱' . transactionSummaryMoney($expense['amount']) . '</span></div>';
    }
    echo '<hr><div class="d-flex justify-content-between"><strong>Total Expense:</strong><strong>₱'
        . transactionSummaryMoney($totalExpenses) . '</strong></div></div>';
    ob_end_flush();
} catch (InvalidArgumentException $exception) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    if (http_response_code() < 400) {
        http_response_code(400);
    }
    echo transactionSummaryEscape($exception->getMessage());
} catch (Throwable $exception) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('Daily transaction summary v2 failed: ' . $exception->getMessage());
    http_response_code(500);
    echo 'Unable to load the daily transaction summary. Please try again later.';
}
