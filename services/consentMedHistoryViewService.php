<?php
//Service for Registration

require_once('databaseService.php');

$clientId = urldecode($_POST['clientId']);

$consentId = urldecode($_POST['consentId']);
//echo'<script>alert("tesT");</script>';
//INHERITANCE -- CREATING NEW INSTANCE OF A CLASS (INSTANTIATE)
$service = new ServiceClass();
$result = $service->loadMedHistory($clientId, $consentId);
//USE THIS AS YOUR BASIS
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
    public function loadMedHistory($clientId, $consentId)
    {
        //:a,:b parameter
        try {



            $query = "select * from medhistory where clientId=:clientid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':clientid', $clientId);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '
<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%;">1. Are you in good health?</div>
    <div style="width: 50%;"><strong>' . ($row["goodhealth"] === "yes" ? "Yes" : ($row["goodhealth"] === "no" ? "No" : "<em>Not specified</em>")) . '</strong></div>
</div>

<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%;">2. Are you under medical treatment now?</div>
    <div style="width: 50%;"><strong>' . ($row["treatment"] === "yes" ? "Yes" : ($row["treatment"] === "no" ? "No" : "<em>Not specified</em>")) . '</strong></div>
</div>

<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%;">- If so, what is the condition being treated?</div>
    <div style="width: 50%;"><strong>' . ($row["treatmentCondition"] === "null" || empty($row["treatmentCondition"]) ? "<em>None specified</em>" : htmlspecialchars($row["treatmentCondition"])) . '</strong></div>
</div>

<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%;">3. Are you taking any prescription/non-prescription medication?</div>
    <div style="width: 50%;"><strong>' . ($row["medication"] === "yes" ? "Yes" : ($row["medication"] === "no" ? "No" : "<em>Not specified</em>")) . '</strong></div>
</div>

<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%;">- If so, please specify:</div>
    <div style="width: 50%;"><strong>' . ($row["medicationCondition"] === "null" || empty($row["medicationCondition"]) ? "<em>None specified</em>" : htmlspecialchars($row["medicationCondition"])) . '</strong></div>
</div>';

                    $allergyValues = isset($row["allergiesCondition"]) ? explode(",", $row["allergiesCondition"]) : [];
                    echo '
                                       
<!-- 8. Are you allergic to any of the following -->
<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%; display: flex; align-items: center;">
        4. Are you allergic to any of the following:
    </div>
    <div style="width: 50%; display: flex; align-items: center;">
        <strong>' . (
                        $row["allergies"] === "yes" ? "Yes" :
                        ($row["allergies"] === "no" ? "No" : "<em>Not specified</em>")
                    ) . '</strong>
    </div>
</div>

' . (
                        $row["allergies"] === "yes"
                        ? '
<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%; display: flex; align-items: center;">
        - If yes, specify:
    </div>
    <div style="width: 50%; display: flex; align-items: center;">
        <strong>' . (
                            empty($row["allergiesCondition"]) || $row["allergiesCondition"] === "null"
                            ? "<em>None specified</em>"
                            : htmlspecialchars(implode(", ", array_map("trim", explode(",", $row["allergiesCondition"]))))
                        ) . '</strong>
    </div>
</div>' .
                        (in_array("Others", $allergyValues) && !empty($row["allergiesOther"]) && $row["allergiesOther"] !== "null"
                            ? '
<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%; display: flex; align-items: center;">
        - Other allergies:
    </div>
    <div style="width: 50%; display: flex; align-items: center;">
        <strong>' . htmlspecialchars($row["allergiesOther"]) . '</strong>
    </div>
</div>'
                            : '')
                        : ''
                    ) . '



<div style="display: flex; margin: 10px 30px;">
    <div style="width: 50%;">5. Bleeding Time :</div>
    <div style="width: 50%;"><strong>' . $row["bleeding"] . '</strong></div>
</div>

<div style="margin: 10px 30px;">
    <div style="font-weight: bold; margin-bottom: 8px;">
        6. For women only:
    </div>

    <!-- Are you pregnant? -->
    <div style="display: flex; margin-bottom: 6px;">
        <div style="width: 50%; display: flex; align-items: center;">
            Are you pregnant?
        </div>
        <div style="width: 50%; display: flex; align-items: center;">
            <strong>' . (
                        $row["pregnant"] === "yes" ? "Yes" :
                        ($row["pregnant"] === "no" ? "No" : "<em>Not specified</em>")
                    ) . '</strong>
        </div>
    </div>

    <!-- Are you nursing? -->
    <div style="display: flex; margin-bottom: 6px;">
        <div style="width: 50%; display: flex; align-items: center;">
            Are you nursing?
        </div>
        <div style="width: 50%; display: flex; align-items: center;">
            <strong>' . (
                        $row["nursing"] === "yes" ? "Yes" :
                        ($row["nursing"] === "no" ? "No" : "<em>Not specified</em>")
                    ) . '</strong>
        </div>
    </div>

    <!-- Are you taking birth control pills? -->
    <div style="display: flex; margin-bottom: 6px;">
        <div style="width: 50%; display: flex; align-items: center;">
            Are you taking birth control pills?
        </div>
        <div style="width: 50%; display: flex; align-items: center;">
            <strong>' . (
                        $row["pills"] === "yes" ? "Yes" :
                        ($row["pills"] === "no" ? "No" : "<em>Not specified</em>")
                    ) . '</strong>
        </div>
    </div>
</div>



                                    Please CHECK if you have had or any of the following:
                         

';




                    $questions = [
                        "q1" => "High Blood Pressure",
                        "q2" => "Low Blood Pressure",
                        "q3" => "Epilepsy/Convulsions",
                        "q4" => "AIDS/HIV Infection",
                        "q5" => "Sexually Transmitted Disease (STD)",
                        "q6" => "Stomach Troubles/Ulcers",
                        "q7" => "Fainting Seizures",
                        "q8" => "Rapid Weight Loss",
                        "q9" => "Heart Problems",
                        "q10" => "Heart Murmur",
                        "q11" => "Pacemaker",
                        "q12" => "Hepatitis",
                        "q13" => "Rheumatic Fever",
                        "q14" => "Hay Fever/Allergies",
                        "q15" => "Respiratory Problems",
                        "q16" => "Tuberculosis",
                        "q17" => "Diabetes",
                        "q18" => "Anemia",
                        "q19" => "Asthma",
                        "q20" => "Cancer",
                        "q21" => "Liver Disease",
                        "q22" => "Kidney Disease",
                        "q23" => "Blood Diseases",
                        "q24" => "Stroke",
                        "q25" => "Thyroid Problem",
                        "q26" => "Emphysema",
                        "q28" => "Radiation Therapy",
                        "q29" => "Swollen ankles",
                        "q30" => "Joint Replacement / Implant",
                        "q31" => "Kidney disease",
                        "q32" => "Heart Surgery",
                        "q33" => "Heart Attack",
                        "q34" => "Chest pain",
                        "q35" => "Angina",
                        "q36" => "Bleeding Problems",
                        "q37" => "Head Injuries",
                        "q38" => "Arthritis / Rheumatism",
                        "q39" => "Rapid Weight Loss"

                    ];

                    // Split into 3 chunks
                    $chunks = array_chunk($questions, ceil(count($questions) / 3), true);

                    echo '<div style="display: flex; flex-wrap: wrap; width: 100%;">';
                    foreach ($chunks as $chunk) {
                        echo '<div style="width: 100%; max-width: 33.33%; padding: 10px; box-sizing: border-box;">';
                        foreach ($chunk as $key => $label) {
                            $checked = $row[$key] == "true";
                            $icon = $checked ? 'fa-check-square' : 'fa-square';
                            $color = $checked ? 'style="color: green;"' : '';
                            echo '<div style="line-height: 1.4;"><i class="fas ' . $icon . '" ' . $color . '></i> ' . $label . '</div>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';


                    // Display q27 (Others)
                    echo '<div class="row mt-2"><div class="col-md-12"><strong>Others, Please specify:</strong> ' . htmlspecialchars($row["q27"]) . '</div></div>';



                }
            }

        } catch (Exception $e) {
            return "Error:" . $e->getMessage();
        }

    }

}
//UNTIL THIS CODE


//UNTIL HERE COPY



?>