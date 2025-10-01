<?php
//Service for Registration

require_once('databaseService.php');
$soaid = urldecode($_POST['soaid']);
$service = new ServiceClass();
$result = $service->printSoa($soaid);
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
   public function printSoa($soaid)
{
    try {
        include_once('../bars/properties.php');

        $query = "SELECT * FROM dentalcertificate WHERE certid = :a";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':a', $soaid);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo '
        <style>
        @media print {
          h2 {
            -webkit-print-color-adjust: exact !important; /* For Chrome, Safari, Edge */
            print-color-adjust: exact !important;         /* Standard */
            background-color: skyblue !important;
            color: white !important;
          }
        }
        </style>

        <div style="width: 8.5in; height: 11in; padding: 0in; font-family: \'Times New Roman\', serif; font-size: 12pt; line-height: 1.6; text-align: justify; box-sizing: border-box; position: relative;">

            <!-- Centered Logo and Clinic Name -->
            <div style="text-align: center; margin-bottom: 1em;">
                <img src="img/' . $systemlogo . '" alt="Company Logo" style="height: 100px; display: inline-block; vertical-align: middle; margin-bottom: 0.3em;">
                <div style="text-align: left; font-family: \'Calibri\', serif; font-size: 12pt;">  
                    <p>
                        <strong>Address:</strong> L22-24 B2, 2/F Mondo Bambini
                        <span style="margin-left: 330px;"><strong>Clinic Hours:</strong> Tuesday - Sunday</span><br>
                        <span style="margin-left: 60px;">Commercial Strip Bldg.</span><span style="margin-left: 470px;"> Monday (CLOSED)</span><br>
                        <span style="margin-left: 60px;">Brgy. Zapote, Biñan, Laguna</span><span style="margin-left: 440px;">  9:00am - 6:00pm</span><br>
                        <span style="margin-right: 220px;"><strong>Contact #:</strong> ' . $systemcontact . '</span>
                       
                        <span><strong>Email:</strong> ' . $systememail . '</span>
                        
                    </p>
                </div>    
            </div>

            <!-- Certificate Title -->
            <h2 style="text-align: center; font-size: 18pt; margin-top: 0; margin-bottom: 1em; background-color: skyblue; color: white; padding: 5px; border-radius: 1px;">
                D E N T A L   &nbsp;&nbsp;&nbsp; C E R T I F I C A T E
            </h2>

            <!-- Certificate Date (Top Right) -->
            <div style="text-align: right;">
                <strong>Date:</strong> ' . date("F j, Y", strtotime($row["date"])) . '
            </div>

            <!-- Certificate Content -->
            <p style="margin: 1em 0;">
                To Whom it may concern:<br>
                &nbsp;&nbsp;&nbsp; This is to certify that <strong>' . $row["name"] . '</strong>, ' . $row["age"] . ' year-old, residing at ' . $row["address"] . ', 
                Has been a patient by this office. His/Her last visit was on ____________________ <br>
                The following procedure/s was-were performed:<br>
        
                  <span style="margin-left: 30px;"> ☐ Dental Consultation </span><br>
                  <span style="margin-left: 30px;"> ☐  Radio Taking  </span><br>
                  <span style="margin-left: 30px;">☐  Oral Phropylaxis  </span><br>
                  <span style="margin-left: 30px;">☐  Filling of tooth number/s ________________________________________________________________________ </span><br>
                  <span style="margin-left: 30px;">☐  Root Canal Treatment of tooth number/s ____________________________________________________________  </span><br>
                  <span style="margin-left: 30px;">☐  Periodontal Treatment of quadrant/s _______________________________________________________________  </span><br>
                  <span style="margin-left: 30px;">☐  Dental Extraction of tooth number/s _______________________________________________________________  </span><br>
                  <span style="margin-left: 30px;">☐  Others _______________________________________________________________________________________ </span><br>
                    
            </p>

            <p style="margin: 1em 0;">
                Remarks/Recommendations: <u> ' . $row["diagnosis"] . '.</u>
            </p>

            <p style="margin: 1em 0 3em 0;">
                <i>This certificate is being issued upon the request of the patient for whatever purpose it may serve except medico-legal purposes.</i>
            </p>

            <!-- Footer Just Below Last Paragraph -->
            <div style="text-align: right; margin-top: 2em;">
                <img src="img/' . $dentistSignature . '" alt="Dentist Signature" style="height: 40px; display: inline-block; vertical-align: middle; margin-bottom: 0.3em;">
                <p style="margin: 0;">' . $row["dentist"] . '</p>
                
                <p style="margin: 0;">License No. ' . $row["license"] . '</p>
               
            </div>

        </div>
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
