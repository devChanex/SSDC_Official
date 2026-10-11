loadUnmigratedToothRemarks();

var migrationQueue = [];

window.addEventListener("message", function (event) {
    if (event.origin !== window.location.origin ||
        event.source !== document.getElementById("dentalChartMigrationFrame").contentWindow ||
        !event.data ||
        event.data.type !== "dental-chart-migration-completed") {
        return;
    }

    var clientId = String(event.data.clientId);
    markPatientRemarksCompleted(clientId);

    if (migrationQueue.length > 0) {
        startPatientChartMigration(migrationQueue.shift());
        return;
    }

    $("#dentalChartMigrationModal").modal("hide");
});

$("#dentalChartMigrationModal").on("hidden.bs.modal", function () {
    migrationQueue = [];
    document.getElementById("dentalChartMigrationFrame").src = "about:blank";
});

$(document).on("click", "#responseBody .patient-chart-link", function (event) {
    event.preventDefault();
    migrationQueue = [];
    startPatientChartMigration({
        clientId: this.dataset.clientid,
        url: this.href,
        clientName: this.textContent
    });
});

function markPatientRemarksCompleted(clientId) {
    var patientLink = document.querySelector(
        '#responseBody .patient-chart-link[data-clientid="' + clientId + '"]'
    );
    var patientRow = patientLink ? patientLink.closest("tr") : null;
    if (!patientRow) {
        return;
    }

    var remarksCell = patientRow.querySelector(".migration-remarks");
    if (remarksCell.querySelector(".patient-chart-completed-link")) {
        return;
    }

    var completedLink = document.createElement("a");
    completedLink.href = remarksCell.dataset.patientChartUrl;
    completedLink.target = "_blank";
    completedLink.rel = "noopener noreferrer";
    completedLink.textContent = "Remarks Completed";
    completedLink.style.color = "black";
    completedLink.className = "patient-chart-completed-link";
    remarksCell.appendChild(completedLink);
}

function startPatientChartMigration(patient) {
    document.getElementById("dentalChartMigrationModalTitle").textContent =
        "Dental Chart Migration - " + patient.clientName;
    document.getElementById("dentalChartMigrationFrame").src =
        patient.url + (patient.url.indexOf("?") === -1 ? "?" : "&") + "embedded=1";
    $("#dentalChartMigrationModal").modal("show");
}

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
    migrationQueue = Array.from(
        document.querySelectorAll("#responseBody .patient-chart-link")
    ).map(function (link) {
        return {
            clientId: link.dataset.clientid,
            url: link.href,
            clientName: link.textContent
        };
    });
    if (migrationQueue.length === 0) {
        return;
    }

    startPatientChartMigration(migrationQueue.shift());
}
