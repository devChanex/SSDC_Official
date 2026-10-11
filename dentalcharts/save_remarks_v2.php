<?php
header('Content-Type: application/json; charset=utf-8');

function respondToBulkRemarkSave($statusCode, $payload)
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respondToBulkRemarkSave(405, ['status' => 'error', 'message' => 'POST requests are required.']);
}

$clientId = filter_input(INPUT_POST, 'clientid', FILTER_VALIDATE_INT);
$recordsJson = isset($_POST['records']) ? $_POST['records'] : '';
if (!$clientId || $clientId < 1 || $recordsJson === '' || strlen($recordsJson) > 1048576) {
    respondToBulkRemarkSave(400, ['status' => 'error', 'message' => 'Invalid patient chart data.']);
}

$records = json_decode($recordsJson, true);
if (!is_array($records) || !$records || count($records) > 55) {
    respondToBulkRemarkSave(400, ['status' => 'error', 'message' => 'Invalid patient chart records.']);
}

$validTeeth = [
    11, 12, 13, 14, 15, 16, 17, 18,
    21, 22, 23, 24, 25, 26, 27, 28,
    31, 32, 33, 34, 35, 36, 37, 38,
    41, 42, 43, 44, 45, 46, 47, 48,
    51, 52, 53, 54, 55, 61, 62, 63, 64, 65,
    71, 72, 73, 74, 75, 81, 82, 83, 84, 85
];
$regionNames = ['top', 'bottom', 'left', 'right', 'center'];
$validatedRecords = [];
$seenTeeth = [];

foreach ($records as $record) {
    if (!is_array($record) ||
        !isset($record['tooth'], $record['remarks'], $record['regions']) ||
        !is_string($record['remarks'])) {
        respondToBulkRemarkSave(400, ['status' => 'error', 'message' => 'Invalid tooth chart record.']);
    }

    $tooth = filter_var($record['tooth'], FILTER_VALIDATE_INT);
    $remarks = trim($record['remarks']);
    if ($tooth === false ||
        !in_array($tooth, $validTeeth, true) ||
        isset($seenTeeth[$tooth]) ||
        strlen($remarks) > 60 ||
        !is_array($record['regions'])) {
        respondToBulkRemarkSave(400, ['status' => 'error', 'message' => 'Invalid tooth chart data.']);
    }
    $seenTeeth[$tooth] = true;

    $colors = [];
    foreach ($regionNames as $region) {
        $color = isset($record['regions'][$region]) ? $record['regions'][$region] : null;
        if (!in_array($color, ['red', 'blue', 'transparent'], true)) {
            respondToBulkRemarkSave(400, ['status' => 'error', 'message' => 'Invalid tooth region color.']);
        }
        $colors[$region] = $color === 'transparent' ? null : $color;
    }

    $validatedRecords[] = [
        'tooth' => $tooth,
        'remarks' => $remarks,
        'colors' => $colors
    ];
}

require_once(__DIR__ . '/../services/databaseService.php');

try {
    $database = new Database();
    $connection = $database->dbConnection();
    if (!$connection) {
        respondToBulkRemarkSave(500, ['status' => 'error', 'message' => 'Unable to connect to the database.']);
    }

    $connection->beginTransaction();
    $rowPlaceholders = [];
    $parameters = [];
    foreach ($validatedRecords as $record) {
        $rowPlaceholders[] = '(?, ?, ?, ?, ?, ?, ?, ?)';
        $parameters[] = $clientId;
        $parameters[] = $record['tooth'];
        $parameters[] = $record['remarks'];
        foreach ($regionNames as $region) {
            $parameters[] = $record['colors'][$region];
        }
    }

    $insertSql = 'INSERT INTO dental_chart_regions
        (clientid, tooth, remarks, top_color, bottom_color, left_color, right_color, center_color)
        VALUES ' . implode(', ', $rowPlaceholders) . '
        ON DUPLICATE KEY UPDATE
            remarks = VALUES(remarks),
            top_color = VALUES(top_color),
            bottom_color = VALUES(bottom_color),
            left_color = VALUES(left_color),
            right_color = VALUES(right_color),
            center_color = VALUES(center_color),
            updated_at = CURRENT_TIMESTAMP';
    $insertStatement = $connection->prepare($insertSql);
    $insertStatement->execute($parameters);

    $deletePlaceholders = implode(', ', array_fill(0, count($validatedRecords), '?'));
    $deleteParameters = [$clientId];
    foreach ($validatedRecords as $record) {
        $deleteParameters[] = $record['tooth'];
    }
    $deleteStatement = $connection->prepare(
        'DELETE FROM toothremarks WHERE clientid = ? AND tooth IN (' . $deletePlaceholders . ')'
    );
    $deleteStatement->execute($deleteParameters);

    $connection->commit();
    respondToBulkRemarkSave(200, [
        'status' => 'success',
        'saved_count' => count($validatedRecords)
    ]);
} catch (Throwable $exception) {
    if (isset($connection) && $connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    error_log('Bulk dental chart save failed: ' . $exception->getMessage());
    respondToBulkRemarkSave(500, ['status' => 'error', 'message' => 'The patient chart could not be saved.']);
}
