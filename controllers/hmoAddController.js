function add() {
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
    fd.append('name', name);
    fd.append('accountnumber', accountnumber);
    fd.append('birthdate', birthdate);
    fd.append('company', company);
    fd.append('contact', contact);
    fd.append('hmo', hmo);

    $.ajax({
        url: "services/hmoAddService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result = result.trim();
            if (result == "success") {
                toastRedirect("successToast", "Successfully Added", "hmoList.php");
            } else {
                showToast("errorToast", result);
            }
        }
    });
}
function reloadPage() {
    location.reload();
}
