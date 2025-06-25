<?php
//Service for Registration

require_once('databaseService.php');

$clientId = urldecode($_POST['clientId']);
//echo'<script>alert("tesT");</script>';
//INHERITANCE -- CREATING NEW INSTANCE OF A CLASS (INSTANTIATE)
$service = new ServiceClass();
$result = $service->loadMedHistory($clientId);
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
    public function loadMedHistory($clientId)
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
       <div class="form-group">
   <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label class="form-label mb-0">1. Are you in good
                                                        health?</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="goodHealth"
                                                        id="goodHealthYes" value="yes" ';
                    if ($row['goodhealth'] === 'yes')
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="goodHealthYes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="goodHealth"
                                                        id="goodHealthNo" value="no"';
                    if ($row['goodhealth'] === 'no')
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="goodHealthNo">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- 2 -->
                                        <div class="row">
                                            <!-- Question Label -->
                                            <div class="col-lg-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label mb-0">2. Are you under medical
                                                        treatment
                                                        now?</label>
                                                </div>
                                            </div>

                                            <!-- Yes/No Options -->
                                            <div class="col-lg-6 mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="underTreatment"
                                                        id="underTreatmentYes" value="yes"
                                                        onclick="toggleCondition(true,\'treatmentCondition\')" ';
                    if ($row['treatment'] === 'yes')
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="underTreatmentYes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="underTreatment"
                                                        id="underTreatmentNo" value="no"
                                                        onclick="toggleCondition(false,\'treatmentCondition\')"';
                    if ($row['treatment'] === 'no') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="underTreatmentNo">No</label>
                                                </div>
                                            </div>

                                            <!-- Conditional Input Field -->
                                            <div class="col-lg-12 mb-3">
                                                <label for="treatmentCondition" class="form-label">-If so, what is
                                                    the
                                                    condition being treated?</label>
                                                <input type="text" id="treatmentCondition" name="treatmentCondition"
                                                    class="form-control" placeholder="Describe the condition" value="';
                    if ($row['treatmentCondition'] === 'null' || empty($row['treatmentCondition'])) {
                        echo '"';
                    } else {
                        echo htmlspecialchars($row['treatmentCondition']) . '"';
                    }
                    if ($row['treatment'] === 'no') {
                        echo ' disabled';
                    }

                    echo '>
                                            </div>
                                        </div>
                                        <!-- 3 -->
                                        <div class="row">
                                            <!-- Question Label -->
                                            <div class="col-lg-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <label class="form-label mb-0">3. Are you taking any
                                                        prescription/non-prescription medication?</label>
                                                </div>
                                            </div>

                                            <!-- Yes/No Options -->
                                            <div class="col-lg-6 mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="medication"
                                                        id="medicationYes" value="yes"
                                                        onclick="toggleCondition(true, \'medicationCondition\')" ';
                    if ($row['medication'] === 'yes') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="medicationYes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="medication"
                                                        id="medicationNo" value="no"
                                                        onclick="toggleCondition(false, \'medicationCondition\')" ';
                    if ($row['medication'] === 'no') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="medicationNo">No</label>
                                                </div>
                                            </div>

                                            <!-- Conditional Input Field -->
                                            <div class="col-lg-12 mb-3">
                                                <label for="medicationCondition" class="form-label">- If so, please
                                                    specify:</label>
                                                <input type="text" id="medicationCondition" name="medicationCondition"
                                                    class="form-control" placeholder="List medications being taken"
                                                    value="';
                    if ($row['medicationCondition'] === 'null' || empty($row['medicationCondition'])) {
                        echo '"';
                    } else {
                        echo htmlspecialchars($row['medicationCondition']) . '"';
                    }
                    if ($row['medication'] === 'no') {
                        echo ' disabled';
                    }
                    echo '>
                                            </div>
                                        </div>
                                        <!-- 4 -->
                                        <div class="row">
                                            <!-- Question Label -->
                                            <div class="col-lg-6 mb-3">
                                                <label class="form-label mb-0">4. Are you allergic to any of the
                                                    following:</label>
                                            </div>

                                              <!-- Yes/No Radio Buttons -->
                                            <div class="col-lg-6 mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="allergicTo"
                                                        id="allergicToYes" value="yes"
                                                        onclick="toggleConditionCheck(true, \'allergyOptions\')" ';
                    if ($row['allergies'] === 'yes') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="allergicToYes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="allergicTo"
                                                        id="allergicToNo" value="no"
                                                        onclick="toggleConditionCheck(false, \'allergyOptions\')"
                                                        ';
                    if ($row['allergies'] === 'no') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="allergicToNo">No</label>
                                                </div>
                                            </div>

                                            <!-- Allergy Options -->
                                            <div class="col-lg-12 mb-3" id="allergyOptions" ';

                    if ($row['allergies'] === 'no') {
                        echo ' style="display: none;"';
                    } else {
                        echo ' style="display: block;"';
                    }
                    $allergyValues = isset($row["allergiesCondition"]) ? explode(",", $row["allergiesCondition"]) : [];

                    echo '>

                                                <div class="row">

                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="allergyLocalAnesthetic" name="allergies"
                                                                value="Local Anesthetic" ' . (in_array('Local Anesthetic', $allergyValues) ? 'checked' : '') . '>
                                                            <label class="form-check-label"
                                                                for="allergyLocalAnesthetic">Local Anesthetic (ex.
                                                                Lidocaine)</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="allergyAspirin" name="allergies" value="Aspirin" ' . (in_array('Aspirin', $allergyValues) ? 'checked' : '') . '>
                                                            <label class="form-check-label"
                                                                for="allergyAspirin">Aspirin</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="allergyPenicillin" name="allergies"
                                                                value="Penicillin" ' . (in_array('Penicillin', $allergyValues) ? 'checked' : '') . '>
                                                            <label class="form-check-label"
                                                                for="allergyPenicillin">Penicillin /
                                                                Antibiotics</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="allergyLatex" name="allergies" value="Latex" ' . (in_array('Latex', $allergyValues) ? 'checked' : '') . '>
                                                            <label class="form-check-label"
                                                                for="allergyLatex">Latex</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="allergySulfa" name="allergies" value="Sulfa" ' . (in_array('Sulfa', $allergyValues) ? 'checked' : '') . '>
                                                            <label class="form-check-label" for="allergySulfa">Sulfa
                                                                Drugs</label>
                                                        </div>
                                                        <!-- Others Checkbox -->
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="allergyOthers" name="allergies" value="Others"
                                                                onchange="toggleSpecifyInput(this, \'otherAllergySpecify\')" ' . (in_array('Others', $allergyValues) ? 'checked' : '') . '>
                                                            <label class="form-check-label"
                                                                for="allergyOthers">Others</label>
                                                        </div>

                                                        <!-- Input to specify other allergies -->
                                                        <div class="mt-2" id="otherAllergySpecify"
                                                            ';
                    if (!in_array('Others', $allergyValues)) {
                        echo 'style="display: none;"';
                    } else {
                        echo 'style="display: block;"';
                    }
                    echo '
                                                            >
                                                            <input type="text" class="form-control"
                                                                id="otherAllergyField" name="otherAllergyDetail"
                                                                placeholder="Please specify other allergy" value="';
                    if ($row['allergiesOther'] === 'null' || empty($row['allergiesOther'])) {
                        echo '';
                    } else {
                        echo htmlspecialchars($row['allergiesOther']);
                    }
                    echo '"


                                                                >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- 5 -->
                                    
                                                    <input class="form-control flex-grow-1" type="hidden"
                                                        id="bleedingTime" placeholder="Specify bleeding time" value="' . $row["bleeding"] . '">
                                             

                                        <div class="row">
                                            <div class="col-lg-12 mb-2">
                                                <label class="form-label fw-bold">5. For women only:</label>
                                            </div>

                                            <!-- Are you pregnant? -->
                                            <div class="col-lg-6 mb-3">
                                                <label class="form-label mb-0">Are you pregnant?</label>
                                            </div>
                                                     <div class="col-lg-6 mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="pregnant"
                                                        id="pregnantYes" value="yes"';
                    if ($row['pregnant'] === 'yes') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="pregnantYes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="pregnant"
                                                        id="pregnantNo" value="no"';
                    if ($row['pregnant'] === 'no') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="pregnantNo">No</label>
                                                </div>
                                            </div>

                                            <!-- Are you nursing? -->
                                            <div class="col-lg-6 mb-3">
                                                <label class="form-label mb-0">Are you nursing?</label>
                                            </div>
                                              <div class="col-lg-6 mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="nursing"
                                                        id="nursingYes" value="yes"';
                    if ($row['nursing'] === 'yes') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="nursingYes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="nursing"
                                                        id="nursingNo" value="no" ';
                    if ($row['nursing'] === 'no') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="nursingNo">No</label>
                                                </div>
                                            </div>

                                            <!-- Are you taking birth control pills? -->
                                            <div class="col-lg-6 mb-3">
                                                <label class="form-label mb-0">Are you taking birth control
                                                    pills?</label>
                                            </div>
                                                <div class="col-lg-6 mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="birthControl"
                                                        id="birthControlYes" value="yes" ';
                    if ($row['pills'] === 'yes') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="birthControlYes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="birthControl"
                                                        id="birthControlNo" value="no"';
                    if ($row['pills'] === 'no') {
                        echo 'checked';
                    }
                    echo '>
                                                    <label class="form-check-label" for="birthControlNo">No</label>
                                                </div>
                                            </div>
                                        </div>

                                <label class="form-label">Medical Conditions (Check all that apply):</label>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q1" name="q1"
                                                value="High Blood Pressure" ';
                    if ($row["q1"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q1">High Blood Pressure</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q2" name="q2"
                                                value="Low Blood Pressure" ';
                    if ($row["q2"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q2">Low Blood Pressure</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q3" name="q3"
                                                value="Epilepsy/Convulsions"';
                    if ($row["q3"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q3">Epilepsy/Convulsions</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q4" name="q4"
                                                value="AIDS/HIV Infection"';
                    if ($row["q4"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q4">AIDS/HIV Infection</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q5" name="q5"
                                                value="Sexually Transmitted Disease (STD)"';
                    if ($row["q5"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q5">Sexually Transmitted Disease
                                                (STD)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q6" name="q6"
                                                value="Stomach Troubles/Ulcers"';
                    if ($row["q6"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q6">Stomach Troubles/Ulcers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q7" name="q7"
                                                value="Fainting Seizures"';
                    if ($row["q7"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q7">Fainting Seizures</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q8" name="q8"
                                                value="Rapid Weight Loss"';
                    if ($row["q8"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q8">Rapid Weight Loss</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q9" name="q9"
                                                value="Heart Problems"';
                    if ($row["q9"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q9">Heart Problems</label>
                                        </div>
                                              <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q10" name="q10"
                                                value="Heart Murmur"';
                    if ($row["q10"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q10">Heart Murmur</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q11" name="q11"
                                                value="Pacemaker"';
                    if ($row["q11"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q11">Pacemaker</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q12" name="q12"
                                                value="Hepatitis"';
                    if ($row["q12"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q12">Hepatitis</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q13" name="q13"
                                                value="Rheumatic Fever"';
                    if ($row["q13"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q13">Rheumatic Fever</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">

                                  
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q14" name="q14"
                                                value="Hay Fever/Allergies"';
                    if ($row["q14"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q14">Hay Fever/Allergies</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q15" name="q15"
                                                value="Respiratory Problems"';
                    if ($row["q15"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q15">Respiratory Problems</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q16" name="q16"
                                                value="Tuberculosis"';
                    if ($row["q16"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q16">Tuberculosis</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q17" name="q17"
                                                value="Diabetes"';
                    if ($row["q17"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q17">Diabetes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q18" name="q18"
                                                value="Anemia"';
                    if ($row["q18"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q18">Anemia</label>
                                        </div>
                                         <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q19" name="q19"
                                                value="Asthma"';
                    if ($row["q19"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q19">Asthma</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q20" name="q20"
                                                value="Cancer"';
                    if ($row["q20"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q20">Cancer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q21" name="q21"
                                                value="Liver Disease"';
                    if ($row["q21"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q21">Liver Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q22" name="q22"
                                                value="Kidney Disease"';
                    if ($row["q22"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q22">Kidney Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q23" name="q23"
                                                value="Blood Diseases"';
                    if ($row["q23"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q23">Blood Diseases</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q24" name="q24"
                                                value="Stroke"';
                    if ($row["q24"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q24">Stroke</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q25" name="q25"
                                                value="Thyroid Problem"';
                    if ($row["q25"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q25">Thyroid Problem</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="q26" name="q26"
                                                value="Emphysema"';
                    if ($row["q26"] == "true")
                        echo 'checked';
                    echo '>
                                            <label class="form-check-label" for="q26">Emphysema</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
  <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q28" name="q28"
                                                        value="Radiation Therapy"';
                    if ($row["q28"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q28">Radiation Therapy</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q29" name="q29"
                                                        value="Swollen ankles"';
                    if ($row["q29"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q29">Swollen ankles</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q30" name="q30"
                                                        value="Joint Replacement/Implant"';
                    if ($row["q30"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q30">Joint Replacement /
                                                        Implant</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q31" name="q31"
                                                        value="Kidney disease"';
                    if ($row["q31"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q31">Kidney disease</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q32" name="q32"
                                                        value="Heart Surgery"';
                    if ($row["q32"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q32">Heart Surgery</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q33" name="q33"
                                                        value="Heart Attack"';
                    if ($row["q33"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q33">Heart Attack</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q34" name="q34"
                                                        value="Chest pain"';
                    if ($row["q34"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q34">Chest pain</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q35" name="q35"
                                                        value="Joint Replacement/Implant"
                                                        ';
                    if ($row["q35"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q35">Angina</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q36" name="q36"
                                                        value="Joint Replacement/Implant"
                                                        ';
                    if ($row["q36"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q36">Bleeding Problems</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q37" name="q37"
                                                        value="Joint Replacement/Implant"';
                    if ($row["q37"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q37">Head Injuries</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q38" name="q38"
                                                        value="Joint Replacement/Implant"';
                    if ($row["q38"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q38">Arthritis /
                                                        Rheumatism</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="q39" name="q39"
                                                        value="Rapid Weight Loss"
                                                        ';
                    if ($row["q39"] == "true")
                        echo 'checked';
                    echo '>
                                                    <label class="form-check-label" for="q39">Rapid Weight Loss</label>
                                                </div>

                                       
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <strong>Others, Please specify:</strong>
                                        <input type="text" class="form-control" id="q27" placeholder="Others, Specify" value="' . $row["q27"] . '">
                                    </div>
                                </div>
                                <hr>


                            </div>
                            <div id="formResult"></div>
                            <footer class="sticky-footer">
                                <div class="container my-auto">
                                    <div class="copyright text-center my-auto">
                                        <a href="#" class="btn btn-success btn-icon-split"
                                            onclick="updateMedHistoryProfile()">
                                            <span class="icon text-white-50"><i class="fas fa-fw fa-save"></i></span>
                                            <span class="text">Save</span>
                                        </a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-icon-split"
                                            onclick="location.replace(\'clientProfileList.php\');">
                                            <span class="icon text-white-50"><i class="fas fa-fw fa-times"></i></span>
                                            <span class="text">Cancel</span>
                                        </a>
                                    </div>
                                </div>
                            </footer>

';
                }
            }

        } catch (Exception $e) {
            return "Error:" . $e->getMessage();
        }



    }
    //UNTIL THIS CODE

}
//UNTIL HERE COPY



?>