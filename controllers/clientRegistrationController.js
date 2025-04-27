var message = "";
function switchToTab1(tab) {
    document.getElementById(tab).click();
}

function addPatientPersonalInfo() {
    var lastName = document.getElementById("lastName").value;
    var firstName = document.getElementById("firstName").value;
    var middleName = document.getElementById("middleName").value;
    var nickName = document.getElementById("nickName").value;
    var age = document.getElementById("age").value; //age
    var e = document.getElementById("gender");
    var gender = e.value;


    var cStatus = document.getElementById("civilStatus");
    var civilStatus = cStatus.value;
    var hmoOption = document.getElementById("hmo");
    var hmo = hmoOption.value;

    var cardNumber = document.getElementById("cardNumber").value;
    var company = document.getElementById("company").value;

    var religion = document.getElementById("religion").value;
    const base64Image = document.getElementById('capturedPhoto').value;



    var birthday = document.getElementById("birthday").value;
    var occupation = document.getElementById("occupation").value;
    var homeAddress = document.getElementById("homeAddress").value;
    var contactNumber = document.getElementById("contactNumber").value;
    var guardianName = document.getElementById("guardianName").value;
    var guardianOccupation = document.getElementById("guardianOccupation").value;
    var referredBy = document.getElementById("referredBy").value;
    var msg = '';
    document.getElementById("formResult").innerHTML = "";
    if (lastName == "") {
        msg = "Last Name is required";
    }
    else if (firstName == "") {
        msg = "First Name is required";
    }
    else if (nickName == "") {
        msg = "Nickname is required";
    }
    else if (civilStatus == "") {
        msg = "Civil Status is required";
    }
    else if (gender == "") {
        msg = "Gender is required";
    }
    else if (religion == "") {
        msg = "Religion is required";
    }
    else if (age <= 1) {
        msg = "Please review Birth date";
    }
    else if (birthday == null) {
        msg = "Birthday is required";
    }
    else if (occupation == "") {
        msg = "Occupation is required";
    }
    else if (homeAddress == "") {
        msg = "Home Address is required";
    }
    else if (contactNumber == "") {
        msg = "Contact Number is required";
    }
    else if (age < 18) {
        if (guardianName == "") {
            msg = "Guardian Name is required";
        }
        else if (guardianOccupation == "") {
            msg = "Guardian Occupation is required";
        }

    }
    if (msg == '') {
        // submitClientform(lastName, firstName, middleName, nickName, gender, age, birthday, occupation, homeAddress, contactNumber, guardianName, guardianOccupation, referredBy, civilStatus, religion, base64Image, hmo, cardNumber, company);
        switchToTab1("tab2-tab");
    } else {
        showToast("errorToast", msg);
    }



}

function computeAge() {

    var birthday = document.getElementById("birthday").value;
    var dob = new Date(birthday);

    if (birthday == null || birthday == '') {
        document.getElementById("message").innerHTML = "**Choose a date please!";
        return false;
    } else {

        //calculate month difference from current date in time  
        var month_diff = Date.now() - dob.getTime();

        //convert the calculated difference in date format  
        var age_dt = new Date(month_diff);

        //extract year from date      
        var year = age_dt.getUTCFullYear();

        //now calculate the age of the user  
        var age = Math.abs(year - 1970);

        //display the calculated age  
        document.getElementById("age").value = age;
    }
}
function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8); // version 4 UUID
        return v.toString(16);
    });
}
function submitClientform(type) {
    var dateSigned = document.getElementById("dateSigned").value;
    var dentistSignature = document.getElementById("dentist-signature-input").value;
    var patientSignature = document.getElementById("patient-signature-input").value;
    var dentistName = document.getElementById("dentistName").value;


    var msg = '';
    if (!dateSigned) {
        msg = 'Date signed is Required';
    } else if (patientSignature == '') {
        msg = 'Incomplete Signature';
    } else if (dentistName == '') {
        msg = "Dentist Name is required.";
    }

    if (type == "submit") {
        if (msg != '') {
            toastError(msg);
            return;
        }
    }

    message = "";
    const imageBlob = document.getElementById('capturedPhoto').value;
    var lastName = document.getElementById("lastName").value;
    var firstName = document.getElementById("firstName").value;
    var middleName = document.getElementById("middleName").value;
    var nickName = document.getElementById("nickName").value;
    var age = document.getElementById("age").value; //age
    var e = document.getElementById("gender");
    var gender = e.value;
    var cStatus = document.getElementById("civilStatus");
    var civilStatus = cStatus.value;
    var hmoOption = document.getElementById("hmo");
    var hmo = hmoOption.value;
    var cardNumber = document.getElementById("cardNumber").value;
    var company = document.getElementById("company").value;
    var religion = document.getElementById("religion").value;
    const base64Image = document.getElementById('capturedPhoto').value;
    var birthday = document.getElementById("birthday").value;
    var occupation = document.getElementById("occupation").value;
    var homeAddress = document.getElementById("homeAddress").value;
    var contactNumber = document.getElementById("contactNumber").value;
    var guardianName = document.getElementById("guardianName").value;
    var guardianOccupation = document.getElementById("guardianOccupation").value;
    var referredBy = document.getElementById("referredBy").value;
    var fd = new FormData();
    fd.append('lastName', lastName);
    fd.append('lastName', lastName);
    fd.append('firstName', firstName);
    fd.append('middleName', middleName);
    fd.append('nickName', nickName);
    fd.append('gender', gender);
    fd.append('age', age);
    fd.append('birthday', birthday);
    fd.append('occupation', occupation);
    fd.append('homeAddress', homeAddress);
    fd.append('contactNumber', contactNumber);
    fd.append('guardianName', guardianName);
    fd.append('guardianOccupation', guardianOccupation);
    fd.append('referredBy', referredBy);
    fd.append('civilStatus', civilStatus);
    fd.append('religion', religion);
    fd.append('profilePhoto', imageBlob);
    fd.append('company', company);
    fd.append('hmo', hmo);

    fd.append('cardNumber', cardNumber);
    $.ajax({
        url: "services/clientRegistrationService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            if (result == "success") {
                message += "Patient Profile Recorded.<br>"
                submitMedHistory(type);
            } else {
                toastError(result);
            }
        }
    });



}
function reloadPage() {
    location.reload();
}


function submitMedHistory(type) {
    var q1 = document.getElementById("q1").checked;
    var q2 = document.getElementById("q2").checked;
    var q3 = document.getElementById("q3").checked;
    var q4 = document.getElementById("q4").checked;
    var q5 = document.getElementById("q5").checked;
    var q6 = document.getElementById("q6").checked;
    var q7 = document.getElementById("q7").checked;
    var q8 = document.getElementById("q8").checked;
    var q9 = document.getElementById("q9").checked;
    var q10 = document.getElementById("q10").checked;
    var q11 = document.getElementById("q11").checked;
    var q12 = document.getElementById("q12").checked;
    var q13 = document.getElementById("q13").checked;
    var q14 = document.getElementById("q14").checked;
    var q15 = document.getElementById("q15").checked;
    var q16 = document.getElementById("q16").checked;
    var q17 = document.getElementById("q17").checked;
    var q18 = document.getElementById("q18").checked;
    var q19 = document.getElementById("q19").checked;
    var q20 = document.getElementById("q20").checked;
    var q21 = document.getElementById("q21").checked;
    var q22 = document.getElementById("q22").checked;
    var q23 = document.getElementById("q23").checked;
    var q24 = document.getElementById("q24").checked;
    var q25 = document.getElementById("q25").checked;
    var q26 = document.getElementById("q26").checked;
    var q27 = document.getElementById("q27").value;

    submitMedHistoryform(
        q1, q2, q3, q4, q5, q6, q7, q8, q9, q10,
        q11, q12, q13, q14, q15, q16, q17, q18, q19, q20,
        q21, q22, q23, q24, q25, q26, q27, type
    );


}

function submitMedHistoryform(
    q1, q2, q3, q4, q5, q6, q7, q8, q9, q10,
    q11, q12, q13, q14, q15, q16, q17, q18, q19, q20,
    q21, q22, q23, q24, q25, q26, q27, type) {
    var fd = new FormData();


    fd.append('q1', q1);
    fd.append('q2', q2);
    fd.append('q3', q3);
    fd.append('q4', q4);
    fd.append('q5', q5);
    fd.append('q6', q6);
    fd.append('q7', q7);
    fd.append('q8', q8);
    fd.append('q9', q9);
    fd.append('q10', q10);
    fd.append('q11', q11);
    fd.append('q12', q12);
    fd.append('q13', q13);
    fd.append('q14', q14);
    fd.append('q15', q15);
    fd.append('q16', q16);
    fd.append('q17', q17);
    fd.append('q18', q18);
    fd.append('q19', q19);
    fd.append('q20', q20);
    fd.append('q21', q21);
    fd.append('q22', q22);
    fd.append('q23', q23);
    fd.append('q24', q24);
    fd.append('q25', q25);
    fd.append('q26', q26);
    fd.append('q27', q27);


    $.ajax({
        url: "services/medHistoryRegService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            if (result == "success") {
                message += "Medical History Added.<br>";
                if (type == "submit") {
                    submitConsentform();
                } else {
                    toastSuccess(message);
                    setTimeout(() => location.reload(), 3000);

                }
            } else {
                toastError(result);
            }
        }
    });
}



function submitConsentform() {
    var dateSigned = document.getElementById("dateSigned").value;
    var dentistSignature = document.getElementById("dentist-signature-input").value;
    var patientSignature = document.getElementById("patient-signature-input").value;
    var dentistName = document.getElementById("dentistName").value;
    var fd = new FormData();
    fd.append('dateSigned', dateSigned);
    fd.append('dentistSignature', dentistSignature);
    fd.append('patientSignature', patientSignature);
    fd.append('dentistName', dentistName);
    $.ajax({
        url: "services/consentAddService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            if (result == "success") {
                message += "Consent Successfully Added.";
                toastSuccess(message);
                setTimeout(() => location.reload(), 3000);

            } else {
                toastError(result);
            }
        }
    });
}