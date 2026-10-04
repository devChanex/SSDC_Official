<?php
header('Content-Type: application/json');

function respond($statusCode, $payload)
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

$tooth = filter_input(INPUT_POST, 'tooth', FILTER_VALIDATE_INT);
$clientId = filter_input(INPUT_POST, 'clientid', FILTER_VALIDATE_INT);
$remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
$regionsJson = isset($_POST['regions']) ? $_POST['regions'] : '';

$validTeeth = [
    11, 12, 13, 14, 15, 16, 17, 18,
    21, 22, 23, 24, 25, 26, 27, 28,
    31, 32, 33, 34, 35, 36, 37, 38,
    41, 42, 43, 44, 45, 46, 47, 48,
    51, 52, 53, 54, 55, 61, 62, 63, 64, 65,
    71, 72, 73, 74, 75, 81, 82, 83, 84, 85
];
$regionNames = ['top', 'bottom', 'left', 'right', 'center'];

if (!$clientId || $clientId < 1 || !$tooth || !in_array($tooth, $validTeeth, true)) {
    respond(400, ['status' => 'error', 'message' => 'Invalid client or tooth.']);
}
if (strlen($remarks) > 60 || $regionsJson === '') {
    respond(400, ['status' => 'error', 'message' => 'Invalid tooth chart data.']);
}

$regions = json_decode($regionsJson, true);
if (!is_array($regions)) {
    respond(400, ['status' => 'error', 'message' => 'Invalid tooth region data.']);
}

$colors = [];
foreach ($regionNames as $region) {
    if (!array_key_exists($region, $regions) || !in_array($regions[$region], ['red', 'blue', 'transparent'], true)) {
        respond(400, ['status' => 'error', 'message' => 'Invalid tooth region color.']);
    }
    $colors[$region] = $regions[$region] === 'transparent' ? null : $regions[$region];
}

require_once(__DIR__ . '/../services/databaseService.php');

try {
    $database = new Database();
    $connection = $database->dbConnection();
    if (!$connection) {
        respond(500, ['status' => 'error', 'message' => 'Unable to connect to the database.']);
    }

    $connection->beginTransaction();
    $query = "INSERT INTO dental_chart_regions
                (clientid, tooth, remarks, top_color, bottom_color, left_color, right_color, center_color)
              VALUES
                (:clientid, :tooth, :remarks, :top_color, :bottom_color, :left_color, :right_color, :center_color)
              ON DUPLICATE KEY UPDATE
                remarks = VALUES(remarks),
                top_color = VALUES(top_color),
                bottom_color = VALUES(bottom_color),
                left_color = VALUES(left_color),
                right_color = VALUES(right_color),
                center_color = VALUES(center_color),
                updated_at = CURRENT_TIMESTAMP";
    $stmt = $connection->prepare($query);
    $stmt->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $stmt->bindValue(':tooth', $tooth, PDO::PARAM_INT);
    $stmt->bindValue(':remarks', $remarks, PDO::PARAM_STR);
    foreach ($regionNames as $region) {
        $stmt->bindValue(':' . $region . '_color', $colors[$region], $colors[$region] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    }
    $stmt->execute();

    $legacyStmt = $connection->prepare(
        'DELETE FROM toothremarks WHERE clientid = :clientid AND tooth = :tooth'
    );
    $legacyStmt->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $legacyStmt->bindValue(':tooth', $tooth, PDO::PARAM_INT);
    $legacyStmt->execute();

    $connection->commit();
    respond(200, ['status' => 'success']);
} catch (PDOException $exception) {
    if (isset($connection) && $connection && $connection->inTransaction()) {
        $connection->rollBack();
    }
    error_log('Dental chart save failed: ' . $exception->getMessage());
    respond(500, ['status' => 'error', 'message' => 'The tooth chart could not be saved.']);
}
