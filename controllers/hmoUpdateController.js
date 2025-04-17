loadDetails();
function loadDetails() {

    var id = document.getElementById("id").value;
    var fd = new FormData();
    fd.append('id', id);
    $.ajax({
        url: "services/hmoLoadUpdateService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            document.getElementById("bodyResult").innerHTML = result;

        }
    });


}


function update() {
    var name = document.getElementById("name").value;
    var accountnumber = document.getElementById("accountnumber").value;
    var birthdate = document.getElementById("birthdate").value;
    var company = document.getElementById("company").value;
    var contact = document.getElementById("contact").value;
    var hmoOption = document.getElementById("hmo");
    var hmo = hmoOption.value;


    var msg = '';
    if (name == "") {
        msg = "Name is required.";
    } else if (accountnumber == "") {
        msg = "Account number is required.";
    }


    if (msg == '') {
        submitform(name, accountnumber, birthdate, company, contact, hmo);
    } else {
        showToast("errorToast", msg);
    }



}

function submitform(name, accountnumber, birthdate, company, contact, hmo) {
    var fd = new FormData();
    var id = document.getElementById("id").value;
    fd.append('id', id);
    fd.append('name', name);
    fd.append('accountnumber', accountnumber);
    fd.append('birthdate', birthdate);
    fd.append('company', company);
    fd.append('contact', contact);
    fd.append('hmo', hmo);

    $.ajax({
        url: "services/updateHMOService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            if (result == "success") {
                toastRedirect("successToast", "Successfully Updated", "hmoList.php");
            } else {
                showToast("errorToast", result);
            }
        }
    });
}
function reloadPage() {
    location.reload();
}
