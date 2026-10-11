loadUnmigratedToothRemarks();

function loadUnmigratedToothRemarks() {
    var limit = document.getElementById("limit").value;
    document.getElementById("h3id").textContent = "LIMIT: " + limit + " PATIENTS";
    $("#loading").fadeIn();

    var fd = new FormData();
    fd.append("limit", limit);

    $.ajax({
        url: "services/unmigratedToothRemarksService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: "POST",
        success: function (result) {
            document.getElementById("responseBody").innerHTML = result;
        },
        error: function () {
            document.getElementById("responseBody").textContent =
                "Unable to load the unmigrated tooth remarks report. Please try again.";
        },
        complete: function () {
            $("#loading").fadeOut();
        }
    });
}
