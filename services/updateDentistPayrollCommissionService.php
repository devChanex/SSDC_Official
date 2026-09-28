<?php
require_once('databaseService.php');

header('Content-Type: application/json');

$tsubid = filter_input(INPUT_POST, 'tsubid', FILTER_VALIDATE_INT);
$rawMaterial = $_POST['raw_material'] ?? null;
$commissionRate = $_POST['commision_rate'] ?? null;
$commissionInput = $_POST['commision'] ?? null;

$allowedRates = ['0', '5', '10', '15', '20', '25', '30', '35', '40', '45', '50', 'Other'];
if (!$tsubid || !is_numeric($rawMaterial) || !in_array((string) $commissionRate, $allowedRates, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid commission data.']);
    exit;
}

$database = new Database();
$conn = $database->dbConnection();

$query = 'SELECT price FROM treatmentsub WHERE tsubid = :tsubid';
$stmt = $conn->prepare($query);
$stmt->bindValue(':tsubid', $tsubid, PDO::PARAM_INT);
$stmt->execute();
$price = $stmt->fetchColumn();

if ($price === false) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Treatment record not found.']);
    exit;
}

if ($commissionRate === 'Other') {
    if (!is_numeric($commissionInput) || (float) $commissionInput < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid commission amount.']);
        exit;
    }
    $commission = (float) $commissionInput;
} else {
    $commission = ((float) $price - (float) $rawMaterial) * ((float) $commissionRate / 100);
}

$query = 'UPDATE treatmentsub SET raw_material = :raw_material, commision_rate = :commision_rate, commision = :commision WHERE tsubid = :tsubid';
$stmt = $conn->prepare($query);
$stmt->bindValue(':raw_material', (float) $rawMaterial);
$stmt->bindValue(':commision_rate', (string) $commissionRate);
$stmt->bindValue(':commision', $commission);
$stmt->bindValue(':tsubid', $tsubid, PDO::PARAM_INT);
$stmt->execute();

echo json_encode(['success' => true, 'commision' => $commission]);