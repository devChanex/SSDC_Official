<?php
header('Content-Type: application/json');

$tooth = filter_input(INPUT_GET, 'tooth', FILTER_VALIDATE_INT);
$clientId = filter_input(INPUT_GET, 'clientid', FILTER_VALIDATE_INT);

if (!$tooth || !$clientId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid client or tooth.']);
    exit;
}

require_once(__DIR__ . '/../services/databaseService.php');

$database = new Database();
$connection = $database->dbConnection();
$stmt = $connection->prepare(
    'SELECT remarks FROM dental_chart_regions WHERE tooth = :tooth AND clientid = :clientid'
);
$stmt->bindValue(':tooth', $tooth, PDO::PARAM_INT);
$stmt->bindValue(':clientid', $clientId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $stmt = $connection->prepare(
        'SELECT remarks FROM toothremarks WHERE tooth = :tooth AND clientid = :clientid'
    );
    $stmt->bindValue(':tooth', $tooth, PDO::PARAM_INT);
    $stmt->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

echo json_encode(['remark' => $row ? $row['remarks'] : '']);
