getclientdata();
function getclientdata() {

    var search = document.getElementById("tableSearch").value;
    var page = document.getElementById("currentPage").value;
    var fd = new FormData();
    fd.append("search", search);
    fd.append("page", page);
    $.ajax({
        url: "services/payrollAdjustmentListService.php",
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

    $('#modal-padjid').val(button.data('id'));
    $('#modal-date').val(button.data('date'));
    $('#modal-particular').val(button.data('particular'));
    $('#modal-description').val(button.data('description'));
    $('#modal-amount').val(button.data('amount'));
    $('#modal-mop').val(button.data('mop'));
});

function getclientdataPagination() {

    var search = document.getElementById("tableSearch").value;
    var page = document.getElementById("currentPage").value;

    var fd = new FormData();
    fd.append("search", search);
    fd.append("page", page);
    $.ajax({
        url: "services/payrollAdjustmentListPaginationService.php",
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

    var expenseid = document.getElementById("modal-padjid").value;
    var fd = new FormData();
    const form = document.getElementById("editExpenseForm");
    const elements = form.querySelectorAll("input, select, textarea");

    elements.forEach(el => {
        if (el.id) {
            fd.append(el.id, el.value);
        }
    });

    $.ajax({
        url: "services/payrollAdjustmentUpsertService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            if (result == "success") {
                if (expenseid == "") {
                    toastSuccess("Payroll Adjustment Added Successfully");
                } else {
                    toastSuccess("Payroll Adjustment Updated Successfully");

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
    alert("document.getElementById('modal-deleteid').value" + document.getElementById('modal-deleteid').value);

    var fd = new FormData();
    const form = document.getElementById("deleteExpenseForm");
    const elements = form.querySelectorAll("input, select, textarea");

    elements.forEach(el => {
        if (el.id) {
            fd.append(el.id, el.value);
        }
    });

    $.ajax({
        url: "services/deletePayrollAdjustmentService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            if (result == "success") {

                toastSuccess("Expense deleted Successfully");
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

    $('#modal-deleteid').val(button.data('id'));

});