loadUnmigratedToothRemarks();

var migrationQueue = [];
var migrationRunTotal = 0;
var completedMigrationCount = 0;

window.addEventListener("message", function (event) {
    if (event.origin !== window.location.origin ||
        event.source !== document.getElementById("dentalChartMigrationFrame").contentWindow ||
        !event.data) {
        return;
    }

    if (event.data.type === "dental-chart-migration-incomplete") {
        migrationQueue = [];
        setMigrationProgress(
            "Migration could not complete for " + event.data.clientName +
            ". The chart remains open for review.",
            "warning"
        );
        return;
    }
    if (event.data.type !== "dental-chart-migration-completed") {
        return;
    }

    var clientId = String(event.data.clientId);
    markPatientRemarksCompleted(clientId);
    completedMigrationCount++;

    if (migrationQueue.length > 0) {
        startPatientChartMigration(migrationQueue.shift());
        return;
    }

    setMigrationProgress(
        "Migration completed for " + completedMigrationCount + " patient" +
        (completedMigrationCount === 1 ? "" : "s") + ".",
        "success"
    );
    $("#dentalChartMigrationModal").modal("hide");
});

$("#dentalChartMigrationModal").on("hidden.bs.modal", function () {
    if (migrationQueue.length > 0) {
        setMigrationProgress("Migration paused before all queued patients were processed.", "warning");
    }
    migrationQueue = [];
    document.getElementById("dentalChartMigrationFrame").src = "about:blank";
});

$(document).on("click", "#responseBody .patient-chart-link", function (event) {
    event.preventDefault();
    migrationQueue = [];
    completedMigrationCount = 0;
    migrationRunTotal = 1;
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
    setMigrationProgress(
        "Migrating patient " + (completedMigrationCount + 1) + " of " + migrationRunTotal +
        ": " + patient.clientName,
        "info"
    );
    document.getElementById("dentalChartMigrationModalTitle").textContent =
        "Dental Chart Migration - " + patient.clientName;
    document.getElementById("dentalChartMigrationFrame").src =
        patient.url + (patient.url.indexOf("?") === -1 ? "?" : "&") + "embedded=1";
    $("#dentalChartMigrationModal").modal("show");
}

function loadUnmigratedToothRemarks() {
    var limit = document.getElementById("limit").value;
    document.getElementById("h3id").textContent = "LIMIT: " + limit + " PATIENTS";
    setMigrationProgress("Loading report. Migration is not running yet.", "secondary");
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
            setMigrationProgress(
                "Ready - migration starts when you click a patient name or Open All.",
                "secondary"
            );
        },
        error: function () {
            document.getElementById("responseBody").textContent =
                "Unable to load the unmigrated tooth remarks report. Please try again.";
            setMigrationProgress("Unable to load the report.", "danger");
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
        setMigrationProgress("No patients are available to migrate in this report.", "secondary");
        return;
    }

    completedMigrationCount = 0;
    migrationRunTotal = migrationQueue.length;
    setMigrationProgress("Starting migration for " + migrationRunTotal + " patients.", "info");
    startPatientChartMigration(migrationQueue.shift());
}

function setMigrationProgress(message, type) {
    var indicator = document.getElementById("migrationProgress");
    indicator.className = "badge badge-" + type;
    indicator.textContent = message;
}
