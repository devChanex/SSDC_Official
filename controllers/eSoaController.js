loadTreatment();
function loadTreatment() {
    var fd = new FormData();
    $.ajax({
        url: "services/OptionloadTreatmentService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            document.getElementById("treatment").innerHTML = result;
        }
    });

}

function add() {
    var e = document.getElementById("treatment");
    var treatment = e.value;
    var remarks = document.getElementById("remarks").value;
    var details = document.getElementById("details").value;

    var diagnosis = document.getElementById("diagnosis").value;
    var price = document.getElementById("price").value;
    if (price == null || price == "") {
        price = 0;
    }


    document.getElementById("treatmentList").innerHTML += "<tr><td>" + treatment + "</td><td>" + diagnosis + "</td><td>" + details + "</td><td>" + remarks + "</td><td>" + price + "</td><td><button class=\"btn btn-success btn-circle btn-sm\" onclick=\"editTreatment(this,'" + treatment + "','" + diagnosis + "','" + details + "','" + remarks + "','" + price + "')\" title=\"Edit treatment\"><i class=\"fas fa-edit\"></i></button><button class=\"btn btn-danger btn-circle btn-sm\" onclick=\"deleteTreatment(this)\" title=\"Delete treatment\"><i class=\"fas fa-times\"></i></button></td></tr>";
    computeTotal();

}

function editTreatment(o, treatment, diagnosis, details, remarks, price) {
    document.getElementById("treatment").value = treatment;
    document.getElementById("remarks").value = remarks;
    var details = document.getElementById("details").value = details;
    document.getElementById("diagnosis").value = diagnosis;
    document.getElementById("price").value = price;
    $(o).closest('tr').remove();

    var table = document.getElementById("treatmentList");
    var rowCount = table.rows.length;
    if (rowCount == 1) {
        table.deleteRow(0);
    }

    recomputeTotal();
}
function deleteTreatment(o) {

    $(o).closest('tr').remove();

    var table = document.getElementById("treatmentList");
    var rowCount = table.rows.length;
    if (rowCount == 1) {
        table.deleteRow(0);
    }
    recomputeTotal();
}

function computeTotal() {

    var table = document.getElementById("treatmentList");
    var rowCount = table.rows.length;
    if (rowCount > 1) {
        table.deleteRow(rowCount - 2);
    }

    var total = 0;
    for (let i in table.rows) {
        let row = table.rows[i]
        for (let j in row.cells) {
            if (j == 4) {
                let col = row.cells[j]
                total += parseFloat(col.innerHTML);
            }

        }
    }
    document.getElementById("treatmentList").innerHTML += "<td colspan=\"4\">Total</td><td>" + total + "</td><td></td></tr>";


}
function recomputeTotal() {

    var table = document.getElementById("treatmentList");
    var rowCount = table.rows.length;
    if (rowCount > 1) {
        table.deleteRow(rowCount - 1);

    }

    var total = 0;
    for (let i in table.rows) {
        let row = table.rows[i]
        for (let j in row.cells) {
            if (j == 4) {
                let col = row.cells[j]
                total += parseFloat(col.innerHTML);
            }

        }
    }
    document.getElementById("treatmentList").innerHTML += "<td colspan=\"4\">Total</td><td>" + total + "</td><td></td></tr>";


}


function submit() {
    var table = document.getElementById("treatmentList");
    var rowCount = table.rows.length;
    var total = 0;
    for (let i in table.rows) {
        let row = table.rows[i]
        if (i == rowCount - 1) {
            for (let j in row.cells) {
                if (j == 1) {
                    let col = row.cells[j]
                    total = parseFloat(col.innerHTML);
                }
            }
        }


    }


    var e = document.getElementById("dentist");
    var dentist = e.value;
    var dates = document.getElementById("date").value;
    var time = document.getElementById("time").value;
    var clientid = document.getElementById("clientid").value;
    var hmo = document.getElementById("hmo").value;
    if (rowCount == 0) {
        toastError("You do not have any treatment added.");
    } else if (dentist == "" || dates == "" || time == "") {
        toastError("All Field is required.");

    } else {
        submitform(dentist, dates, time, clientid, total, hmo);
    }



}

function submitform(dentist, dates, time, clientid, total, hmo) {

    var agreement = document.getElementById("agreement").value;

    var confirmation = confirm('Are you sure you want to submit this form?');
    if (confirmation) {
        var fd = new FormData();
        fd.append('dentist', dentist);
        fd.append('date', dates);
        fd.append('time', time);
        fd.append('clientid', clientid);
        fd.append('total', total);
        fd.append('hmo', hmo);
        fd.append('agreement', agreement);

        $.ajax({
            url: "services/esoaSubmitService.php",
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (result) {

                if (parseInt(result) > 0) {
                    submitSubSoa(result);
                } else {
                    toastError(result);
                }

            }
        });
    }
}
function submitSubSoa(soaid) {
    var table = document.getElementById("treatmentList");
    var rowCount = table.rows.length;
    var clientid = document.getElementById("clientid").value;
    var total = 0;
    for (let i in table.rows) {
        let row = table.rows[i]
        if (i < rowCount - 1) {
            var treatment = row.cells[0].innerHTML;
            var diagnosis = row.cells[1].innerHTML;
            var details = row.cells[2].innerHTML;
            var remarks = row.cells[3].innerHTML;
            var price = parseFloat(row.cells[4].innerHTML);

            if (treatment) {
                submitSubSoatoService(treatment, diagnosis, details, remarks, price, clientid, soaid);
            }



        }


    }


}

function submitSubSoatoService(treatment, diagnosis, details, remarks, price, clientid, soaid) {


    var fd = new FormData();
    fd.append('treatment', treatment);
    fd.append('diagnosis', diagnosis);
    fd.append('details', details);
    fd.append('remarks', remarks);
    fd.append('price', price);
    fd.append('clientid', clientid);
    fd.append('soaid', soaid);
    $.ajax({
        url: "services/esoaSubmitSubService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            toastRedirect("successToast", "E-SOA successfully submitted", "soaViewing.php?soaid=" + soaid);
        }
    });



}
function reloadPage() {
    location.reload();
}


