function updateMedHistoryProfile() {

    var confirmation = confirm("Are you sure you want to update this medical history profile?");

    if (confirmation) {
        var clientId = document.getElementById("clientId").value;
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
        var q28 = document.getElementById("q28").checked;
        var q29 = document.getElementById("q29").checked;
        var q30 = document.getElementById("q30").checked;
        var q31 = document.getElementById("q31").checked;
        var q32 = document.getElementById("q32").checked;
        var q33 = document.getElementById("q33").checked;
        var q34 = document.getElementById("q34").checked;
        var q35 = document.getElementById("q35").checked;
        var q36 = document.getElementById("q36").checked;
        var q37 = document.getElementById("q37").checked;
        var q38 = document.getElementById("q38").checked;
        var q39 = document.getElementById("q39").checked;

        submitform(
            clientId,
            q1, q2, q3, q4, q5, q6, q7, q8, q9, q10,
            q11, q12, q13, q14, q15, q16, q17, q18, q19, q20,
            q21, q22, q23, q24, q25, q26, q27,
            q28, q29, q30, q31, q32, q33, q34, q35, q36, q37, q38, q39
        );
    }




}
function submitform(clientId, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10,
    q11, q12, q13, q14, q15, q16, q17, q18, q19, q20,
    q21, q22, q23, q24, q25, q26, q27,
    q28, q29, q30, q31, q32, q33, q34, q35, q36, q37, q38, q39) {
    var fd = new FormData();

    var goodhealth = document.querySelector('input[name="goodHealth"]:checked')?.value || null;
    var treatment = document.querySelector('input[name="underTreatment"]:checked')?.value || null;
    var treatmentCondition = document.getElementById("treatmentCondition").value.trim() || null;
    var medication = document.querySelector('input[name="medication"]:checked')?.value || null;
    var medicationCondition = document.getElementById("medicationCondition").value.trim() || null;
    var allergies = document.querySelector('input[name="allergicTo"]:checked')?.value || null;
    var allergiesCondition = Array.from(document.querySelectorAll('input[name="allergies"]:checked')).map(cb => cb.value) || null;
    var otherAllergyField = document.getElementById("otherAllergyField").value.trim() || null;
    var pregnant = document.querySelector('input[name="pregnant"]:checked')?.value || null;
    var nursing = document.querySelector('input[name="nursing"]:checked')?.value || null;
    var birthControl = document.querySelector('input[name="birthControl"]:checked')?.value || null;
    var bleeding = document.getElementById("bleedingTime").value;
    fd.append('clientId', clientId);
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
    fd.append('q28', q28);
    fd.append('q29', q29);
    fd.append('q30', q30);
    fd.append('q31', q31);
    fd.append('q32', q32);
    fd.append('q33', q33);
    fd.append('q34', q34);
    fd.append('q35', q35);
    fd.append('q36', q36);
    fd.append('q37', q37);
    fd.append('q38', q38);
    fd.append('q39', q39);
    fd.append('goodhealth', goodhealth);
    fd.append('treatment', treatment);
    fd.append('treatmentCondition', treatmentCondition);
    fd.append('medication', medication);
    fd.append('medicationCondition', medicationCondition);
    fd.append('allergies', allergies);
    fd.append('allergiesCondition', allergiesCondition.join(','));
    fd.append('otherAllergyField', otherAllergyField);
    fd.append('pregnant', pregnant);
    fd.append('nursing', nursing);
    fd.append('birthControl', birthControl);
    fd.append("bleeding", bleeding);

    $.ajax({
        url: "../services/medHistoryUpdateService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            if (result == "success") {
                showToast("successToast", "Sucessfully updated");
            } else {
                showToast("errorToast", result);
            }
        }
    });
}
function reloadPage() {

    window.location.href = "clientProfileList.php";
}
