<?php
require_once(__DIR__ . '/databaseService.php');
session_start();

$clientId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$clientId || $clientId < 1) {
    http_response_code(400);
    exit('Invalid client ID.');
}

$database = new Database();
$connection = $database->dbConnection();
if (!$connection) {
    http_response_code(500);
    exit('Unable to connect to the database.');
}

try {
    $query = "SELECT tooth, image, remarks FROM toothremarks
              WHERE clientid = :clientid
                AND NOT EXISTS (
                    SELECT 1 FROM dental_chart_regions
                    WHERE dental_chart_regions.clientid = toothremarks.clientid
                      AND dental_chart_regions.tooth = toothremarks.tooth
                )";
    $statement = $connection->prepare($query);
    $statement->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $statement->execute();
    $legacyRows = $statement->fetchAll(PDO::FETCH_ASSOC);

    $query = "SELECT tooth, remarks, top_color, bottom_color, left_color, right_color, center_color
              FROM dental_chart_regions WHERE clientid = :clientid";
    $statement = $connection->prepare($query);
    $statement->bindValue(':clientid', $clientId, PDO::PARAM_INT);
    $statement->execute();
    $toothData = [];
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $toothData[(int) $row['tooth']] = [
            'remarks' => $row['remarks'],
            'regions' => [
                'top' => $row['top_color'],
                'bottom' => $row['bottom_color'],
                'left' => $row['left_color'],
                'right' => $row['right_color'],
                'center' => $row['center_color']
            ],
            'legacyImage' => null
        ];
    }

    foreach ($legacyRows as $row) {
        $toothData[(int) $row['tooth']] = [
            'remarks' => $row['remarks'],
            'regions' => [],
            'legacyImage' => 'data:image/png;base64,' . base64_encode($row['image'])
        ];
    }
} catch (PDOException $exception) {
    error_log('Dental chart migration page load failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Unable to load the dental chart.');
}

echo '<div class="text-center"><strong>UPPER</strong></div><div class="tooth-arch">';
renderColumn(range(18, 11), $toothData, $clientId);
renderColumn(range(21, 28), $toothData, $clientId);
echo '</div><div class="tooth-arch" style="margin-top:40px;">';
renderColumn(range(55, 51), $toothData, $clientId);
renderColumn(range(61, 65), $toothData, $clientId);
echo '</div><div class="row"><div class="col-lg-6 text-left"><strong>RIGHT</strong></div>
      <div class="col-lg-6 text-right"><strong>LEFT</strong></div></div>
      <div class="tooth-arch" style="margin-top:40px;">';
renderColumn([85, 84, 83, 82, 81], $toothData, $clientId);
renderColumn([71, 72, 73, 74, 75], $toothData, $clientId);
echo '</div><div class="tooth-arch">';
renderColumn([48, 47, 46, 45, 44, 43, 42, 41], $toothData, $clientId);
renderColumn([31, 32, 33, 34, 35, 36, 37, 38], $toothData, $clientId);
echo '</div><div class="text-center"><strong>LOWER</strong></div><hr>
      <div class="text-center"><strong>Recommendations:</strong></div>
      <div id="dentalChartNoteField"></div><hr>';

function renderColumn($teeth, $toothData, $clientId)
{
    echo '<div class="tooth-column">';
    foreach ($teeth as $toothNumber) {
        $data = isset($toothData[$toothNumber]) ? $toothData[$toothNumber] : null;
        $remarks = $data ? $data['remarks'] : '-';
        $regionColors = $data ? $data['regions'] : [];
        $image = $data && $data['legacyImage']
            ? $data['legacyImage']
            : 'dentalcharts/tooth_1.png';
        $safeRemarks = htmlspecialchars((string) $remarks, ENT_QUOTES, 'UTF-8');
        $safeRegionColors = htmlspecialchars(
            (string) json_encode($regionColors),
            ENT_QUOTES,
            'UTF-8'
        );
        $isLegacy = $data && $data['legacyImage'] ? 'true' : 'false';

        echo '<div class="tooth" data-tooth="' . (int) $toothNumber . '"
            data-clientid="' . (int) $clientId . '"
            data-remarks="' . $safeRemarks . '"
            data-region-colors="' . $safeRegionColors . '"
            data-legacy="' . $isLegacy . '">
            <div class="remark-display">' . $safeRemarks . '</div>
            <div class="tooth-image-wrapper">
                <img src="' . htmlspecialchars($image, ENT_QUOTES, 'UTF-8') . '"
                    alt="Tooth ' . (int) $toothNumber . '">
                <svg class="tooth-region-overlay" viewBox="0 0 300 300" aria-hidden="true">
                    <circle data-region="center" cx="150" cy="150" r="40" fill="' . regionColor($regionColors, 'center') . '"></circle>
                    <rect data-region="top" x="110" y="10" width="80" height="40" fill="' . regionColor($regionColors, 'top') . '"></rect>
                    <rect data-region="bottom" x="110" y="250" width="80" height="40" fill="' . regionColor($regionColors, 'bottom') . '"></rect>
                    <rect data-region="left" x="10" y="110" width="40" height="80" fill="' . regionColor($regionColors, 'left') . '"></rect>
                    <rect data-region="right" x="250" y="110" width="40" height="80" fill="' . regionColor($regionColors, 'right') . '"></rect>
                </svg>
            </div>
            <label>' . (int) $toothNumber . '</label>
        </div>';
    }
    echo '</div>';
}

function regionColor($regionColors, $region)
{
    $color = isset($regionColors[$region]) ? $regionColors[$region] : null;
    return in_array($color, ['red', 'blue'], true) ? $color : 'transparent';
}
