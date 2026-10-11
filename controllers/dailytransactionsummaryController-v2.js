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

function printDiv(divName) {
    var printWindow = window.open("", "_blank");
    if (!printWindow) {
        window.alert("Please allow pop-ups to print this report.");
        return;
    }

    var from = document.getElementById("from").value;
    var group = document.getElementById("group").value;
    var header = document.querySelector("#" + divName + " > div:first-child");
    printWindow.document.write("<!DOCTYPE html><html><head><title>Daily Transaction Summary</title></head><body>Preparing report for printing...</body></html>");
    printWindow.document.close();
    $("#loading").fadeIn();

    var fd = new FormData();
    fd.append("from", from);
    fd.append("group", group);
    fd.append("page", 1);
    fd.append("print_all", "1");

    $.ajax({
        url: "services/dailytransactionsummaryservice-v2.php",
        data: fd,
        processData: false,
        contentType: false,
        type: "POST",
        success: function (result) {
            var printDocument = "<!DOCTYPE html><html><head><title>Daily Transaction Summary</title>" +
                "<style>" +
                "body{margin:.5in;font:8pt Arial,sans-serif;color:#000}" +
                "table{border-collapse:collapse;width:100%;font-size:8pt}" +
                "th,td{padding:4px;border:1px solid #777;text-align:left;vertical-align:top}" +
                ".text-right{text-align:right}.text-center{text-align:center}" +
                ".pagination,button{display:none!important}" +
                ".table-responsive{overflow:visible!important}" +
                "@media print{thead{display:table-header-group}tr{page-break-inside:avoid}}" +
                "</style></head><body>" +
                (header ? header.innerHTML : "") +
                result +
                "<script>window.onload=function(){window.focus();window.print();};" +
                "window.onafterprint=function(){window.close();};</scr" + "ipt>" +
                "</body></html>";
            printWindow.document.open();
            printWindow.document.write(printDocument);
            printWindow.document.close();
        },
        error: function (xhr) {
            var message = xhr.responseText || "Unable to prepare the full report for printing.";
            printWindow.document.open();
            printWindow.document.write("<!DOCTYPE html><html><head><title>Print error</title></head><body></body></html>");
            printWindow.document.close();
            printWindow.document.body.textContent = message;
        },
        complete: function () {
            $("#loading").fadeOut();
        }
    });
}
