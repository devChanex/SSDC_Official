<?php
require_once('databaseService.php');
session_start();
$service = new ServiceClass();

$clientid = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$clientid || $clientid < 1) {
    http_response_code(400);
    exit('Invalid client ID.');
}

$service->process($clientid);

class ServiceClass
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function process($clientid)
    {
        $toothData = [];
        $query = "SELECT tooth, remarks, top_color, bottom_color, left_color, right_color, center_color
                  FROM dental_chart_regions WHERE clientid = :clientid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':clientid', $clientid, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

        // Keep charts saved before structured chart data was introduced visible until edited.
        $legacyQuery = "SELECT tooth, image, remarks FROM toothremarks
                        WHERE clientid = :clientid
                        AND tooth NOT IN (
                            SELECT tooth FROM dental_chart_regions WHERE clientid = :legacy_clientid
                        )";
        $legacyStmt = $this->conn->prepare($legacyQuery);
        $legacyStmt->bindValue(':clientid', $clientid, PDO::PARAM_INT);
        $legacyStmt->bindValue(':legacy_clientid', $clientid, PDO::PARAM_INT);
        $legacyStmt->execute();
        while ($row = $legacyStmt->fetch(PDO::FETCH_ASSOC)) {
            $toothData[(int) $row['tooth']] = [
                'remarks' => $row['remarks'],
                'regions' => [],
                'legacyImage' => 'data:image/png;base64,' . base64_encode($row['image'])
            ];
        }

        echo '<div class="text-center"><strong>UPPER</strong></div>';
        echo '<div class="tooth-arch">';
        $this->renderColumn(range(18, 11), $toothData, $clientid);
        $this->renderColumn(range(21, 28), $toothData, $clientid);
        echo '</div>';

        echo '<div class="tooth-arch" style="margin-top: 40px;">';
        $this->renderColumn(range(55, 51), $toothData, $clientid);
        $this->renderColumn(range(61, 65), $toothData, $clientid);
        echo '</div>';

        echo '
<div class="row">
    <div class="col-lg-6 text-left">
        <strong>RIGHT</strong>
    </div>
    <div class="col-lg-6 text-right">
        <strong>LEFT</strong>
    </div>
</div>';

        echo '<div class="tooth-arch" style="margin-top: 40px;">';
        $this->renderColumn([85, 84, 83, 82, 81], $toothData, $clientid);
        $this->renderColumn([71, 72, 73, 74, 75], $toothData, $clientid);
        echo '</div>';

        echo '<div class="tooth-arch">';
        $this->renderColumn([48, 47, 46, 45, 44, 43, 42, 41], $toothData, $clientid);
        $this->renderColumn([31, 32, 33, 34, 35, 36, 37, 38], $toothData, $clientid);
        echo '</div>';

        echo '<div class="text-center"><strong>LOWER</strong></div>';
        echo '<hr>';
        echo '<div class="text-center"><strong>Recommendations:</strong></div>';
        echo '<div id="dentalChartNoteField"></div>';
        echo '<hr>';
    }

    private function renderColumn($teeth, $toothData, $clientid)
    {
        echo '<div class="tooth-column">';
        foreach ($teeth as $toothNumber) {
            $data = isset($toothData[$toothNumber]) ? $toothData[$toothNumber] : null;
            $remarks = $data ? $data['remarks'] : '-';
            $safeRemarks = htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8');
            $regionColors = $data ? $data['regions'] : [];
            $safeRegionColors = htmlspecialchars(
                json_encode($regionColors),
                ENT_QUOTES,
                'UTF-8'
            );
            $image = $data && $data['legacyImage']
                ? $data['legacyImage']
                : 'dentalcharts/tooth_1.png';
            $isLegacy = $data && $data['legacyImage'] ? 'true' : 'false';

            echo '
    <div class="tooth" data-tooth="' . $toothNumber . '"
        data-clientid="' . (int) $clientid . '"
        data-remarks="' . $safeRemarks . '"
        data-region-colors="' . $safeRegionColors . '"
        data-legacy="' . $isLegacy . '">
        <div class="remark-display">' . $safeRemarks . '</div>
        <div class="tooth-image-wrapper">
            <img src="' . $image . '" alt="Tooth ' . $toothNumber . '">
            <svg class="tooth-region-overlay" viewBox="0 0 300 300" aria-hidden="true">
                <circle data-region="center" cx="150" cy="150" r="40" fill="' . $this->regionColor($regionColors, 'center') . '"></circle>
                <rect data-region="top" x="110" y="10" width="80" height="40" fill="' . $this->regionColor($regionColors, 'top') . '"></rect>
                <rect data-region="bottom" x="110" y="250" width="80" height="40" fill="' . $this->regionColor($regionColors, 'bottom') . '"></rect>
                <rect data-region="left" x="10" y="110" width="40" height="80" fill="' . $this->regionColor($regionColors, 'left') . '"></rect>
                <rect data-region="right" x="250" y="110" width="40" height="80" fill="' . $this->regionColor($regionColors, 'right') . '"></rect>
            </svg>
        </div>
        <label>' . $toothNumber . '</label>
    </div>';
        }
        echo '</div>';
    }

    private function regionColor($regionColors, $region)
    {
        $color = isset($regionColors[$region]) ? $regionColors[$region] : null;
        return in_array($color, ['red', 'blue'], true) ? $color : 'transparent';
    }
}
