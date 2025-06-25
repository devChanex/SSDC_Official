<?php
//Service for Registration

require_once('databaseService.php');

$clientId = urldecode($_POST['clientId']);
$q1 = urldecode($_POST['q1']);
$q2 = urldecode($_POST['q2']);
$q3 = urldecode($_POST['q3']);
$q4 = urldecode($_POST['q4']);
$q5 = urldecode($_POST['q5']);
$q6 = urldecode($_POST['q6']);
$q7 = urldecode($_POST['q7']);
$q8 = urldecode($_POST['q8']);
$q9 = urldecode($_POST['q9']);
$q10 = urldecode($_POST['q10']);
$q11 = urldecode($_POST['q11']);
$q12 = urldecode($_POST['q12']);
$q13 = urldecode($_POST['q13']);
$q14 = urldecode($_POST['q14']);
$q15 = urldecode($_POST['q15']);
$q16 = urldecode($_POST['q16']);
$q17 = urldecode($_POST['q17']);
$q18 = urldecode($_POST['q18']);
$q19 = urldecode($_POST['q19']);
$q20 = urldecode($_POST['q20']);
$q21 = urldecode($_POST['q21']);
$q22 = urldecode($_POST['q22']);

$q23 = urldecode($_POST['q23']);
$q24 = urldecode($_POST['q24']);
$q25 = urldecode($_POST['q25']);
$q26 = urldecode($_POST['q26']);
$q27 = urldecode($_POST['q27']);
$q28 = urldecode($_POST['q28']);
$q29 = urldecode($_POST['q29']);
$q30 = urldecode($_POST['q30']);
$q31 = urldecode($_POST['q31']);
$q32 = urldecode($_POST['q32']);
$q33 = urldecode($_POST['q33']);
$q34 = urldecode($_POST['q34']);
$q35 = urldecode($_POST['q35']);
$q36 = urldecode($_POST['q36']);
$q37 = urldecode($_POST['q37']);
$q38 = urldecode($_POST['q38']);
$q39 = urldecode($_POST['q39']);

// Additional medical history fields
$goodhealth = urldecode($_POST['goodhealth']);
$treatment = urldecode($_POST['treatment']);
$treatmentCondition = urldecode($_POST['treatmentCondition']);
$medication = urldecode($_POST['medication']);
$medicationCondition = urldecode($_POST['medicationCondition']);
$allergies = urldecode($_POST['allergies']);
$allergiesCondition = urldecode($_POST['allergiesCondition']);
$otherAllergyField = urldecode($_POST['otherAllergyField']);
$pregnant = urldecode($_POST['pregnant']);
$nursing = urldecode($_POST['nursing']);
$birthControl = urldecode($_POST['birthControl']);
$bleeding = urldecode($_POST['bleeding']);
//echo'<script>alert("tesT");</script>';
//INHERITANCE -- CREATING NEW INSTANCE OF A CLASS (INSTANTIATE)
$service = new ServiceClass();
$result = $service->addMedHistory(
	$clientId,
	$q1,
	$q2,
	$q3,
	$q4,
	$q5,
	$q6,
	$q7,
	$q8,
	$q9,
	$q10,
	$q11,
	$q12,
	$q13,
	$q14,
	$q15,
	$q16,
	$q17,
	$q18,
	$q19,
	$q20,
	$q21,
	$q22,
	$q23,
	$q24,
	$q25,
	$q26,
	$q27,
	$q28,
	$q29,
	$q30,
	$q31,
	$q32,
	$q33,
	$q34,
	$q35,
	$q36,
	$q37,
	$q38,
	$q39,
	$goodhealth,
	$treatment,
	$treatmentCondition,
	$medication,
	$medicationCondition,
	$allergies,
	$allergiesCondition,
	$otherAllergyField,
	$pregnant,
	$nursing,
	$birthControl,
	$bleeding
);
echo $result;
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
	public function addMedHistory(
		$clientId,
		$q1,
		$q2,
		$q3,
		$q4,
		$q5,
		$q6,
		$q7,
		$q8,
		$q9,
		$q10,
		$q11,
		$q12,
		$q13,
		$q14,
		$q15,
		$q16,
		$q17,
		$q18,
		$q19,
		$q20,
		$q21,
		$q22,
		$q23,
		$q24,
		$q25,
		$q26,
		$q27,
		$q28,
		$q29,
		$q30,
		$q31,
		$q32,
		$q33,
		$q34,
		$q35,
		$q36,
		$q37,
		$q38,
		$q39,
		$goodhealth,
		$treatment,
		$treatmentCondition,
		$medication,
		$medicationCondition,
		$allergies,
		$allergiesCondition,
		$otherAllergyField,
		$pregnant,
		$nursing,
		$birthControl,
		$bleeding
	) {
		//:a,:b parameter
		try {

			$query = "update medhistory set q1=:q1,q2=:q2,q3=:q3,q4=:q4,q5=:q5,q6=:q6,q7=:q7,q8=:q8,q9=:q9,q10=:q10,q11=:q11,q12=:q12,q13=:q13,q14=:q14,q15=:q15,q16=:q16,q17=:q17,q18=:q18,q19=:q19,q20=:q20,q21=:q21,q22=:q22,q23=:q23,q24=:q24,q25=:q25,q26=:q26,q27=:q27, q28=:q28, q29=:q29, q30=:q30, q31=:q31, q32=:q32, q33=:q33, q34=:q34, q35=:q35, q36=:q36, q37=:q37, q38=:q38, q39=:q39,goodhealth=:goodhealth,treatment=:treatment,treatmentCondition=:treatmentCondition,medication=:medication,medicationCondition=:medicationCondition,allergies=:allergies,allergiesCondition=:allergiesCondition,allergiesOther=:otherAllergyField,pregnant=:pregnant,nursing=:nursing,pills=:birthControl,bleeding=:bleeding where clientid=:clientid";

			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':clientid', $clientId);
			$stmt->bindParam(':q1', $q1);
			$stmt->bindParam(':q2', $q2);
			$stmt->bindParam(':q3', $q3);
			$stmt->bindParam(':q4', $q4);
			$stmt->bindParam(':q5', $q5);
			$stmt->bindParam(':q6', $q6);
			$stmt->bindParam(':q7', $q7);
			$stmt->bindParam(':q8', $q8);
			$stmt->bindParam(':q9', $q9);
			$stmt->bindParam(':q10', $q10);
			$stmt->bindParam(':q11', $q11);
			$stmt->bindParam(':q12', $q12);
			$stmt->bindParam(':q13', $q13);
			$stmt->bindParam(':q14', $q14);
			$stmt->bindParam(':q15', $q15);
			$stmt->bindParam(':q16', $q16);
			$stmt->bindParam(':q17', $q17);
			$stmt->bindParam(':q18', $q18);
			$stmt->bindParam(':q19', $q19);
			$stmt->bindParam(':q20', $q20);
			$stmt->bindParam(':q21', $q21);
			$stmt->bindParam(':q22', $q22);
			$stmt->bindParam(':q23', $q23);
			$stmt->bindParam(':q24', $q24);
			$stmt->bindParam(':q25', $q25);
			$stmt->bindParam(':q26', $q26);
			$stmt->bindParam(':q27', $q27);
			$stmt->bindParam(':q28', $q28);
			$stmt->bindParam(':q29', $q29);
			$stmt->bindParam(':q30', $q30);
			$stmt->bindParam(':q31', $q31);
			$stmt->bindParam(':q32', $q32);
			$stmt->bindParam(':q33', $q33);
			$stmt->bindParam(':q34', $q34);
			$stmt->bindParam(':q35', $q35);
			$stmt->bindParam(':q36', $q36);
			$stmt->bindParam(':q37', $q37);
			$stmt->bindParam(':q38', $q38);
			$stmt->bindParam(':q39', $q39);
			$stmt->bindParam(':goodhealth', $goodhealth);
			$stmt->bindParam(':treatment', $treatment);
			$stmt->bindParam(':treatmentCondition', $treatmentCondition);
			$stmt->bindParam(':medication', $medication);
			$stmt->bindParam(':medicationCondition', $medicationCondition);
			$stmt->bindParam(':allergies', $allergies);
			$stmt->bindParam(':allergiesCondition', $allergiesCondition);
			$stmt->bindParam(':otherAllergyField', $otherAllergyField);
			$stmt->bindParam(':pregnant', $pregnant);
			$stmt->bindParam(':nursing', $nursing);
			$stmt->bindParam(':birthControl', $birthControl);
			$stmt->bindParam(':bleeding', $bleeding);
			$stmt->execute();
			return "success";
		} catch (Exception $e) {
			return "Error:" . $e->getMessage();
		}



	}
	//UNTIL THIS CODE

}
//UNTIL HERE COPY



?>