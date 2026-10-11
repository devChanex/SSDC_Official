var migrationFrames = {};
var currentBatch = null;
var batchNumber = 0;
var completedBatches = 0;
var autoStartNextBatch = false;
var batchCountdownTimer = null;

document.getElementById("migrationThemeToggle").addEventListener("click", function () {
    var modalContent = document.querySelector("#dentalChartMigrationModal .modal-content");
    var isDark = modalContent.classList.toggle("migration-modal-dark");
    var icon = this.querySelector("i");
    var label = document.getElementById("migrationThemeToggleLabel");
    this.setAttribute("aria-pressed", String(isDark));
    this.setAttribute("aria-label", isDark ? "Disable dark crystal theme" : "Enable dark crystal theme");
    this.title = isDark ? "Disable dark crystal theme" : "Enable dark crystal theme";
    icon.classList.toggle("fa-moon", !isDark);
    icon.classList.toggle("fa-sun", isDark);
    label.textContent = isDark ? "Light" : "Dark";
});

loadUnmigratedToothRemarks();

window.addEventListener("message", function (event) {
    if (event.origin !== window.location.origin || !event.data) {
        return;
    }
    var migration = migrationFrames[String(event.data.clientId)];
    if (!migration || event.source !== migration.iframe.contentWindow) {
        return;
    }
    if (event.data.type === "dental-chart-migration-completed") {
        finishPatientMigration(migration, true);
    } else if (event.data.type === "dental-chart-migration-incomplete") {
        finishPatientMigration(migration, false);
    }
});

$(document).on("click", "#responseBody .patient-chart-link", function (event) {
    event.preventDefault();
    if (migrationFrames[this.dataset.clientid] &&
        migrationFrames[this.dataset.clientid].status !== "failed") {
        showMigrationModal();
        return;
    }
    startMigrationBatch(false, [patientFromLink(this)]);
});

function patientFromLink(link) {
    return {
        clientId: link.dataset.clientid,
        url: link.href,
        clientName: link.textContent,
        recordCount: getPatientRecordCount(link)
    };
}

function startPatientChartMigration(patient) {
    var clientId = String(patient.clientId);
    if (migrationFrames[clientId] && migrationFrames[clientId].status === "failed") {
        migrationFrames[clientId].iframe.remove();
        delete migrationFrames[clientId];
    }

    var iframe = document.createElement("iframe");
    iframe.title = "Dental chart migration for " + patient.clientName;
    iframe.src = patient.url + (patient.url.indexOf("?") === -1 ? "?" : "&") +
        "embedded=1&batch=" + currentBatch.number;
    iframe.style.width = "1px";
    iframe.style.height = "1px";
    iframe.style.border = "0";

    var migration = {
        clientId: clientId,
        clientName: patient.clientName,
        recordCount: patient.recordCount,
        iframe: iframe,
        status: "running",
        batchNumber: currentBatch.number
    };
    migrationFrames[clientId] = migration;
    currentBatch.pendingClientIds[clientId] = true;
    document.getElementById("migrationFramePool").appendChild(iframe);
}

function startMigrationBatch(automatic, selectedPatients) {
    if (currentBatch && Object.keys(currentBatch.pendingClientIds).length > 0) {
        setMigrationProgress("A migration batch is already running.", "warning");
        return;
    }
    if (batchCountdownTimer) {
        setMigrationProgress("The next batch is already scheduled.", "warning");
        return;
    }

    var patients = selectedPatients || Array.from(
        document.querySelectorAll("#responseBody .patient-chart-link")
    ).map(patientFromLink);
    var pendingPatients = patients.filter(function (patient) {
        return !migrationFrames[String(patient.clientId)] ||
            migrationFrames[String(patient.clientId)].status === "failed";
    });
    if (pendingPatients.length === 0) {
        autoStartNextBatch = false;
        setMigrationProgress("No unstarted patient migrations in this report.", "secondary");
        return;
    }

    autoStartNextBatch = Boolean(automatic);
    currentBatch = {
        number: ++batchNumber,
        totalRecords: pendingPatients.length,
        successfulSaves: 0,
        failedSaves: 0,
        completedPatients: 0,
        failedPatients: 0,
        pendingClientIds: {},
        patients: pendingPatients.map(function (patient) {
            return {
                clientId: String(patient.clientId),
                clientName: patient.clientName,
                status: "in-progress"
            };
        }),
        autoContinue: autoStartNextBatch
    };

    document.getElementById("batchNumberProgress").textContent = currentBatch.number;
    document.getElementById("batchCountdownProgress").textContent = "";
    updateBatchProgress();
    $("#dentalChartMigrationModal").modal("show");
    pendingPatients.forEach(startPatientChartMigration);
    renderBatchPatientList();
    setMigrationProgress("Batch " + currentBatch.number + " running. One save request per patient.", "info");
}

function getPatientRecordCount(link) {
    var row = link.closest("tr");
    var countCell = row ? row.cells[3] : null;
    return countCell ? parseInt(countCell.textContent.replace(/,/g, ""), 10) || 0 : 0;
}

function finishPatientMigration(migration, succeeded) {
    if (!currentBatch ||
        migration.batchNumber !== currentBatch.number ||
        !currentBatch.pendingClientIds[migration.clientId]) {
        return;
    }

    delete currentBatch.pendingClientIds[migration.clientId];
    migration.status = succeeded ? "completed" : "failed";
    currentBatch.completedPatients++;
    if (succeeded) {
        currentBatch.successfulSaves++;
        markPatientRemarksCompleted(migration.clientId);
    } else {
        currentBatch.failedSaves++;
        currentBatch.failedPatients++;
        addPatientMigrationFailure(migration.clientId);
    }
    currentBatch.patients.forEach(function (patient) {
        if (patient.clientId === migration.clientId) {
            patient.status = migration.status;
        }
    });
    updateBatchProgress();
    if (Object.keys(currentBatch.pendingClientIds).length === 0) {
        completeCurrentBatch();
    }
}

function markPatientRemarksCompleted(clientId) {
    var link = document.querySelector(
        '#responseBody .patient-chart-link[data-clientid="' + clientId + '"]'
    );
    var row = link ? link.closest("tr") : null;
    var cell = row ? row.querySelector(".migration-remarks") : null;
    if (!cell || cell.querySelector(".patient-chart-completed-link")) {
        return;
    }
    var completedLink = document.createElement("a");
    completedLink.href = cell.dataset.patientChartUrl;
    completedLink.target = "_blank";
    completedLink.rel = "noopener noreferrer";
    completedLink.textContent = "Remarks Completed";
    completedLink.style.color = "black";
    completedLink.className = "patient-chart-completed-link";
    cell.appendChild(completedLink);
}

function addPatientMigrationFailure(clientId) {
    var link = document.querySelector(
        '#responseBody .patient-chart-link[data-clientid="' + clientId + '"]'
    );
    var row = link ? link.closest("tr") : null;
    var cell = row ? row.querySelector(".migration-remarks") : null;
    if (cell && !cell.textContent) {
        cell.textContent = "Migration Failed";
        cell.className += " text-danger";
    }
}

function showMigrationModal() {
    if (!currentBatch) {
        setMigrationProgress("No migration batch has been started.", "secondary");
        return;
    }
    $("#dentalChartMigrationModal").modal("show");
    renderBatchPatientList();
}

function completeCurrentBatch() {
    completedBatches++;
    if (!currentBatch.autoContinue) {
        document.getElementById("batchCountdownProgress").textContent =
            currentBatch.failedPatients > 0
                ? "Batch finished with failures. Failed patients remain available for retry."
                : "Batch completed successfully.";
        setMigrationProgress("Batch " + currentBatch.number + " completed.",
            currentBatch.failedPatients > 0 ? "warning" : "success");
        updateBatchProgress();
        return;
    }

    var remainingSeconds = 5;
    document.getElementById("batchCountdownProgress").textContent =
        "Batch completed. Refreshing report in " + remainingSeconds + " seconds...";
    updateBatchProgress();
    batchCountdownTimer = window.setInterval(function () {
        remainingSeconds--;
        if (remainingSeconds > 0) {
            document.getElementById("batchCountdownProgress").textContent =
                "Batch completed. Refreshing report in " + remainingSeconds + " seconds...";
            return;
        }
        window.clearInterval(batchCountdownTimer);
        batchCountdownTimer = null;
        loadUnmigratedToothRemarks(function () {
            if (!autoStartNextBatch) {
                return;
            }
            var remainingPatients = Array.from(
                document.querySelectorAll("#responseBody .patient-chart-link")
            ).map(patientFromLink).filter(function (patient) {
                return !migrationFrames[String(patient.clientId)] ||
                    migrationFrames[String(patient.clientId)].status === "failed";
            });
            if (remainingPatients.length === 0) {
                autoStartNextBatch = false;
                document.getElementById("batchCountdownProgress").textContent =
                    "No new patients remain. All available batches are done.";
                setMigrationProgress("Migration finished. " + completedBatches + " batches completed.", "success");
                return;
            }
            startMigrationBatch(true, remainingPatients);
        });
    }, 1000);
}

function updateBatchProgress() {
    if (!currentBatch) {
        return;
    }
    document.getElementById("batchNumberProgress").textContent = currentBatch.number;
    document.getElementById("completedBatchesProgress").textContent = completedBatches;
    var total = currentBatch.totalRecords;
    var successWidth = total > 0 ? currentBatch.successfulSaves / total * 100 : 0;
    var failureWidth = total > 0 ? currentBatch.failedSaves / total * 100 : 0;
    var successBar = document.getElementById("successfulSaveProgress");
    var failureBar = document.getElementById("failedSaveProgress");
    successBar.style.width = successWidth + "%";
    successBar.setAttribute("aria-valuenow", Math.round(successWidth));
    successBar.textContent = "Successful: " + currentBatch.successfulSaves;
    failureBar.style.width = failureWidth + "%";
    failureBar.setAttribute("aria-valuenow", Math.round(failureWidth));
    failureBar.textContent = "Failed: " + currentBatch.failedSaves;
    document.getElementById("processedRecordsProgress").textContent =
        "Patient save requests processed: " +
        (currentBatch.successfulSaves + currentBatch.failedSaves) + " / " + total +
        " (patients complete: " + currentBatch.completedPatients + ")";
    renderBatchPatientList();
}

function renderBatchPatientList() {
    if (!currentBatch) {
        return;
    }
    var inProgress = document.getElementById("inProgressPatientList");
    var completed = document.getElementById("completedPatientList");
    inProgress.textContent = "";
    completed.textContent = "";
    currentBatch.patients.forEach(function (patient) {
        var item = document.createElement("li");
        item.className = "migration-patient-item";
        var name = document.createElement("span");
        name.className = "migration-patient-name";
        name.textContent = patient.clientName;
        item.appendChild(name);
        var badge = document.createElement("span");
        badge.className = "badge migration-patient-badge";
        if (patient.status === "in-progress") {
            badge.classList.add("badge-success");
            badge.textContent = "Running";
            inProgress.appendChild(item);
        } else {
            badge.classList.add(patient.status === "failed" ? "badge-danger" : "badge-primary");
            badge.textContent = patient.status === "failed" ? "For Retry" : "Done";
            completed.appendChild(item);
        }
        item.appendChild(badge);
    });
    document.getElementById("inProgressPatientCount").textContent =
        "(" + currentBatch.patients.filter(function (patient) {
            return patient.status === "in-progress";
        }).length + ")";
    document.getElementById("completedPatientCount").textContent =
        "(" + currentBatch.patients.filter(function (patient) {
            return patient.status !== "in-progress";
        }).length + ")";
}

function loadUnmigratedToothRemarks(onSuccess) {
    var formData = new FormData();
    formData.append("limit", document.getElementById("limit").value);
    formData.append("lastNameInitial", document.getElementById("lastNameInitial").value);
    document.getElementById("h3id").textContent =
        "LAST NAME: " + (document.getElementById("lastNameInitial").value || "ALL") +
        " | LIMIT: " + document.getElementById("limit").value + " PATIENTS";
    $("#loading").fadeIn();
    $.ajax({
        url: "services/unmigratedToothRemarksService-v2.php",
        data: formData,
        processData: false,
        contentType: false,
        type: "POST",
        success: function (result) {
            document.getElementById("responseBody").innerHTML = result;
            if (typeof onSuccess === "function") {
                onSuccess();
            } else if (!currentBatch || Object.keys(currentBatch.pendingClientIds).length === 0) {
                setMigrationProgress("Ready - click Start Migration to process the next batch.", "secondary");
            }
        },
        error: function (xhr) {
            document.getElementById("responseBody").textContent =
                xhr.responseText || "Unable to load the unmigrated tooth remarks report.";
            if (typeof onSuccess === "function") {
                autoStartNextBatch = false;
                document.getElementById("batchCountdownProgress").textContent =
                    "Automatic migration paused because report refresh failed.";
                setMigrationProgress("Unable to load the report.", "danger");
            }
        },
        complete: function () {
            $("#loading").fadeOut();
        }
    });
}

function setMigrationProgress(message, type) {
    var indicator = document.getElementById("migrationProgress");
    indicator.className = "badge badge-" + type;
    indicator.textContent = message;
}
