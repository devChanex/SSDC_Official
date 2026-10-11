var migrationFrames = {};
var migrationRunTotal = 0;
var completedMigrationCount = 0;
var failedMigrationCount = 0;
var visibleMigrationClientId = null;

loadUnmigratedToothRemarks();

window.addEventListener("message", function (event) {
    if (event.origin !== window.location.origin || !event.data) {
        return;
    }

    var clientId = String(event.data.clientId);
    var migration = migrationFrames[clientId];
    if (!migration || event.source !== migration.iframe.contentWindow) {
        return;
    }

    if (event.data.type === "dental-chart-migration-completed") {
        if (migration.status !== "completed") {
            migration.status = "completed";
            completedMigrationCount++;
            markPatientRemarksCompleted(clientId);
            updateMigrationOption(migration);
            updateMigrationProgress();
        }
    } else if (event.data.type === "dental-chart-migration-incomplete") {
        if (migration.status !== "failed") {
            migration.status = "failed";
            failedMigrationCount++;
            updateMigrationOption(migration);
            updateMigrationProgress();
        }
    }
});

$("#dentalChartMigrationModal").on("hidden.bs.modal", function () {
    moveVisibleMigrationToPool();
});

$(document).on("click", "#responseBody .patient-chart-link", function (event) {
    event.preventDefault();
    var migration = startPatientChartMigration({
        clientId: this.dataset.clientid,
        url: this.href,
        clientName: this.textContent
    });
    if (migration) {
        selectMigrationPatient(migration.clientId);
    }
});

function startPatientChartMigration(patient) {
    if (migrationFrames[patient.clientId]) {
        return migrationFrames[patient.clientId];
    }

    var iframe = document.createElement("iframe");
    iframe.title = "Dental chart migration for " + patient.clientName;
    iframe.src = patient.url + (patient.url.indexOf("?") === -1 ? "?" : "&") + "embedded=1";
    iframe.style.width = "1px";
    iframe.style.height = "1px";
    iframe.style.border = "0";

    var migration = {
        clientId: String(patient.clientId),
        clientName: patient.clientName,
        iframe: iframe,
        status: "running"
    };
    migrationFrames[migration.clientId] = migration;
    migrationRunTotal++;
    document.getElementById("migrationFramePool").appendChild(iframe);
    addMigrationOption(migration);
    updateMigrationProgress();
    return migration;
}

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

function addMigrationOption(migration) {
    var select = document.getElementById("migrationPatientSelect");
    var option = document.createElement("option");
    option.value = migration.clientId;
    option.textContent = migration.clientName + " - Running";
    select.appendChild(option);
    select.value = migration.clientId;
}

function updateMigrationOption(migration) {
    var option = document.querySelector(
        '#migrationPatientSelect option[value="' + migration.clientId + '"]'
    );
    if (option) {
        var statusText = migration.status === "completed" ? "Completed" : "Needs review";
        option.textContent = migration.clientName + " - " + statusText;
    }
}

function showMigrationModal() {
    var clientIds = Object.keys(migrationFrames);
    if (clientIds.length === 0) {
        setMigrationProgress("No migration charts are currently running.", "secondary");
        return;
    }

    var current = migrationFrames[visibleMigrationClientId] || migrationFrames[clientIds[0]];
    selectMigrationPatient(current.clientId);
    $("#dentalChartMigrationModal").modal("show");
}

function showMigrationPatient(clientId) {
    var migration = migrationFrames[String(clientId)];
    if (!migration) {
        return;
    }

    moveVisibleMigrationToPool();
    var iframe = migration.iframe;
    iframe.style.position = "static";
    iframe.style.left = "auto";
    iframe.style.top = "auto";
    iframe.style.width = "100%";
    iframe.style.height = "75vh";
    document.getElementById("migrationChartView").appendChild(iframe);
    visibleMigrationClientId = migration.clientId;
    document.getElementById("dentalChartMigrationModalTitle").textContent =
        "Dental Chart Migration - " + migration.clientName +
        (migration.status === "running" ? " (Running)" : "");
    document.getElementById("migrationPatientSelect").value = migration.clientId;
}

function selectMigrationPatient(clientId) {
    document.getElementById("migrationPatientSelect").value = String(clientId);
    showMigrationPatient(clientId);
}

function moveVisibleMigrationToPool() {
    if (!visibleMigrationClientId || !migrationFrames[visibleMigrationClientId]) {
        return;
    }

    var iframe = migrationFrames[visibleMigrationClientId].iframe;
    iframe.style.position = "absolute";
    iframe.style.left = "-10000px";
    iframe.style.top = "0";
    iframe.style.width = "1px";
    iframe.style.height = "1px";
    document.getElementById("migrationFramePool").appendChild(iframe);
    visibleMigrationClientId = null;
}

function loadUnmigratedToothRemarks() {
    var limit = document.getElementById("limit").value;
    document.getElementById("h3id").textContent = "LIMIT: " + limit + " PATIENTS";
    setMigrationProgress("Loading report. Migration has not started.", "secondary");
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
                "Ready - click Open All to migrate all listed patients in the background.",
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
    var patients = Array.from(
        document.querySelectorAll("#responseBody .patient-chart-link")
    ).map(function (link) {
        return {
            clientId: link.dataset.clientid,
            url: link.href,
            clientName: link.textContent
        };
    });
    var pendingPatients = patients.filter(function (patient) {
        return !migrationFrames[patient.clientId];
    });

    if (pendingPatients.length === 0) {
        setMigrationProgress("No unstarted patient migrations in this report.", "secondary");
        return;
    }

    pendingPatients.forEach(startPatientChartMigration);
    updateMigrationProgress();
}

function updateMigrationProgress() {
    var runningCount = Object.keys(migrationFrames).filter(function (clientId) {
        return migrationFrames[clientId].status === "running";
    }).length;
    var message = "Migration running in background: " + runningCount + " running";
    if (migrationRunTotal > 0) {
        message += ", " + completedMigrationCount + " completed of " + migrationRunTotal;
    }
    if (failedMigrationCount > 0) {
        message += ", " + failedMigrationCount + " need review";
    }

    setMigrationProgress(message + ".", failedMigrationCount > 0 ? "warning" : "info");
}

function setMigrationProgress(message, type) {
    var indicator = document.getElementById("migrationProgress");
    indicator.className = "badge badge-" + type;
    indicator.textContent = message;
}
