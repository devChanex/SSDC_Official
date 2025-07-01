getclientdata();
function getclientdata() {

    var search = document.getElementById("tableSearch").value;
    var page = document.getElementById("currentPage").value;
    var fd = new FormData();
    fd.append("search", search);
    fd.append("page", page);
    $.ajax({
        url: "services/hmopaymentListService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {

            document.getElementById("resultResponseBody").innerHTML = result;
            getclientdataPagination();
        }

    });
    document.getElementById("content-table").style.zoom = "60%";
}

function setPage(page) {
    document.getElementById("currentPage").value = page;
    getclientdata();

}

function search() {
    document.getElementById("currentPage").value = 1;
    getclientdata();
}

$('#editExpenseModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal

    $('#modal-hmopaymentid').val(button.data('hmopaymentid'));
    $('#modal-soadate').val(button.data('soadate'));
    $('#modal-datesubmitted').val(button.data('datesubmitted'));
    $('#modal-amount').val(button.data('amount'));
    $('#modal-paymentdate').val(button.data('paymentdate'));
    $('#modal-hmo').val(button.data('hmo'));
    $('#modal-bank').val(button.data('bank'));
});

function getclientdataPagination() {

    var search = document.getElementById("tableSearch").value;
    var page = document.getElementById("currentPage").value;

    var fd = new FormData();
    fd.append("search", search);
    fd.append("page", page);
    $.ajax({
        url: "services/hmopaymentPaginationListService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            // $('#sortableTable').DataTable().destroy();
            // $('#sortableTable').find('tbody').append(result);
            // $('#sortableTable').DataTable().draw();
            document.getElementById("pagination").innerHTML = result;

        }

    });
    document.getElementById("content-table").style.zoom = "60%";
}


function submitCart() {

    var hmopaymentid = document.getElementById("modal-hmopaymentid").value;
    var fd = new FormData();
    const form = document.getElementById("editExpenseForm");
    const elements = form.querySelectorAll("input, select, textarea");

    elements.forEach(el => {
        if (el.id) {
            fd.append(el.id, el.value);
        }
    });

    $.ajax({
        url: "services/upserthmopaymentService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            if (result == "success") {
                if (hmopaymentid == "") {
                    toastSuccess("Expense Added Successfully");
                } else {
                    toastSuccess("Expense Updated Successfully");

                }
                $('#editExpenseModal').modal('hide');
                getclientdata();
            } else {
                console.log(result);
                toastError(result);
            }


        }

    });
    document.getElementById("content-table").style.zoom = "60%";
}


function deleteCart() {


    var fd = new FormData();
    const form = document.getElementById("deleteExpenseForm");
    const elements = form.querySelectorAll("input, select, textarea");

    elements.forEach(el => {
        if (el.id) {
            fd.append(el.id, el.value);
        }
    });

    $.ajax({
        url: "services/deletehmopaymentService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            if (result == "success") {

                toastSuccess("HMO deleted Successfully");
                $('#deleteExpenseModal').modal('hide');
                getclientdata();
            } else {

                toastError(result);
            }


        }

    });
    document.getElementById("content-table").style.zoom = "60%";
}

$('#deleteExpenseModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal

    $('#modal-delete-hmopaymentid').val(button.data('hmopaymentid'));

});