<?php
require_once(__DIR__ . '/databaseService.php');

$limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT);
if (!in_array($limit, [5, 10, 20], true)) {
    http_response_code(400);
    exit('Invalid report limit.');
}

$database = new Database();
$connection = $database->dbConnection();
if (!$connection) {
    http_response_code(500);
    exit('Unable to connect to the database.');
}

$query = "SELECT cp.clientid, cp.birthDate, cp.homeAddress,
                 CONCAT(cp.lname, ', ', cp.fname, ' ', cp.mdname) AS fullname,
                 unmigrated.record_count, unmigrated.image_bytes
          FROM clientprofile cp
          INNER JOIN (
              SELECT legacy.clientid,
                     COUNT(*) AS record_count,
                     COALESCE(SUM(OCTET_LENGTH(legacy.image)), 0) AS image_bytes
              FROM toothremarks legacy
              WHERE NOT EXISTS (
                  SELECT 1
                  FROM dental_chart_regions migrated
                  WHERE migrated.clientid = legacy.clientid
                    AND migrated.tooth = legacy.tooth
              )
              GROUP BY legacy.clientid
              ORDER BY legacy.clientid ASC
              LIMIT :limit
          ) unmigrated ON unmigrated.clientid = cp.clientid
          ORDER BY cp.lname ASC";

try {
    $statement = $connection->prepare($query);
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();
    $patients = $statement->fetchAll(PDO::FETCH_ASSOC);

    $countStatement = $connection->query(
        'SELECT
            (SELECT COUNT(DISTINCT clientid) FROM dental_chart_regions) AS migrated_count,
            (SELECT COUNT(clientid) FROM clientprofile) AS patient_count'
    );
    $patientCounts = $countStatement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log('Unmigrated tooth remarks report failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Unable to load the unmigrated tooth remarks report.');
}

echo '<p class="font-weight-bold text-center">Total number of migrated '
    . number_format((int) $patientCounts['migrated_count']) . ' out of '
    . number_format((int) $patientCounts['patient_count']) . ' patients as of '
    . date('Y-m-d h:i:s A T') . '</p>';

if (!$patients) {
    echo '<p class="text-center">No patients with unmigrated tooth remarks were found.</p>';
    exit;
}

echo '<div class="table-responsive">
    <table class="table table-bordered text-dark" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Client ID</th>
                <th>Full Name</th>
                <th>Remarks</th>
                <th>Unmigrated Records</th>
                <th>Image Data Size</th>
            </tr>
        </thead>
        <tbody>';

$totalRecords = 0;
$totalImageBytes = 0;

foreach ($patients as $patient) {
    $recordCount = (int) $patient['record_count'];
    $imageBytes = (int) $patient['image_bytes'];
    $totalRecords += $recordCount;
    $totalImageBytes += $imageBytes;
    $birthDate = (string) $patient['birthDate'];
    $dateOfBirth = new DateTime($birthDate);
    $age = (new DateTime())->diff($dateOfBirth)->y;
    $chartUrl = 'dentalChartMigration.php?' . http_build_query([
        'id' => $patient['clientid'],
        'clientname' => $patient['fullname'],
        'birthDate' => $birthDate,
        'age' => $age,
        'address' => $patient['homeAddress']
    ]);
    $patientChartUrl = 'patientChartList.php?' . http_build_query([
        'id' => $patient['clientid'],
        'clientname' => $patient['fullname'],
        'birthDate' => $birthDate,
        'age' => $age,
        'address' => $patient['homeAddress']
    ]);

    echo '<tr>
        <td>' . htmlspecialchars((string) $patient['clientid'], ENT_QUOTES, 'UTF-8') . '</td>
        <td><a class="patient-chart-link" data-clientid="' . (int) $patient['clientid']
            . '" data-patient-chart-url="' . htmlspecialchars($patientChartUrl, ENT_QUOTES, 'UTF-8')
            . '" href="' . htmlspecialchars($chartUrl, ENT_QUOTES, 'UTF-8')
            . '" style="color: black;">'
            . htmlspecialchars((string) $patient['fullname'], ENT_QUOTES, 'UTF-8') . '</a></td>
        <td class="migration-remarks" data-patient-chart-url="' . htmlspecialchars($patientChartUrl, ENT_QUOTES, 'UTF-8') . '"></td>
        <td>' . number_format($recordCount) . '</td>
        <td>' . htmlspecialchars(formatImageSize($imageBytes), ENT_QUOTES, 'UTF-8') . '</td>
    </tr>';
}

echo '</tbody>
    <tfoot>
        <tr class="font-weight-bold">
            <td colspan="3">Total</td>
            <td>' . number_format($totalRecords) . '</td>
            <td>' . htmlspecialchars(formatImageSize($totalImageBytes), ENT_QUOTES, 'UTF-8') . '</td>
        </tr>
    </tfoot>
    </table>
</div>';

function formatImageSize($bytes)
{
    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }

    return $bytes . ' B';
}
