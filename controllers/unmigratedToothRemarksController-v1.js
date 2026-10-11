loadUnmigratedToothRemarks();

window.addEventListener("message", function (event) {
    if (event.origin !== window.location.origin ||
        !event.data ||
        event.data.type !== "dental-chart-migration-completed") {
        return;
    }

    var clientId = String(event.data.clientId);
    var patientLink = document.querySelector(
        '#responseBody .patient-chart-link[data-clientid="' + clientId + '"]'
    );
    var patientRow = patientLink ? patientLink.closest("tr") : null;
    if (!patientRow) {
        return;
    }

    var remarksCell = patientRow.querySelector(".migration-remarks");
    var completedLink = document.createElement("a");
    completedLink.href = remarksCell.dataset.patientChartUrl;
    completedLink.target = "_blank";
    completedLink.rel = "noopener noreferrer";
    completedLink.textContent = "Remarks Completed";
    completedLink.style.color = "black";
    remarksCell.appendChild(completedLink);
});

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

function openAllPatientCharts() {
    var chartLinks = document.querySelectorAll("#responseBody .patient-chart-link");

    chartLinks.forEach(function (link) {
        openPatientChart(link.href, link.dataset.clientid);
    });
}

function openPatientChart(url, clientId) {
    var chartWindow = window.open(url, "dentalChartMigration_" + clientId);
    return chartWindow === null;
}
