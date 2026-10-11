changeDateToday("from");
getclientdata(1);

function getclientdata(page) {
    var group = document.getElementById("group").value;
    var from = document.getElementById("from").value;
    document.getElementById("h3id").textContent = "DATE: " + from;
    $("#loading").fadeIn();

    var fd = new FormData();
    fd.append("from", from);
    fd.append("group", group);
    fd.append("page", page || 1);

    $.ajax({
        url: "services/dailytransactionsummaryservice-v2.php",
        data: fd,
        processData: false,
        contentType: false,
        type: "POST",
        success: function (result) {
            document.getElementById("responseBody").innerHTML = result;
        },
        error: function (xhr) {
            var message = xhr.responseText || "Unable to load the daily transaction summary.";
            document.getElementById("responseBody").textContent = message;
        },
        complete: function () {
            $("#loading").fadeOut();
        }
    });
}
