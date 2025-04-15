loadPatientSignature("patientSignature");
loadPatientSignature("dentistSignature");
function loadPatientSignature(signature) {
    var consentId = document.getElementById("consentId").value;
    var fd = new FormData();
    fd.append('consentId', consentId);
    fd.append('role', signature);
    $.ajax({
        url: "services/consentViewSignatureService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            if (result != "") {

                document.getElementById(signature).src = result;
            } else {

            }
        }
    });
}


loadMedHistory();

function loadMedHistory() {
    var clientId = document.getElementById("clientId").value;
    var fd = new FormData();
    fd.append('clientId', clientId);


    $.ajax({
        url: "services/consentMedHistoryViewService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {


            document.getElementById("medHistory").innerHTML = result;

        }
    });

}