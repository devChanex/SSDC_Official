var migrationFrames = {};
var currentBatch = null;
var batchNumber = 0;
var completedBatches = 0;
var autoStartNextBatch = false;
var batchCountdownTimer = null;

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

    if (event.data.type === "dental-chart-tooth-save") {
        recordToothSaveResult(migration, event.data.success);
    } else if (event.data.type === "dental-chart-migration-completed") {
        finishPatientMigration(migration, true);
    } else if (event.data.type === "dental-chart-migration-incomplete") {
        finishPatientMigration(migration, false);
    }
});

$(document).on("click", "#responseBody .patient-chart-link", function (event) {
    event.preventDefault();
    var existing = migrationFrames[this.dataset.clientid];
    if (existing) {
        showMigrationModal();
        return;
    }

    startMigrationBatch(false, [{
        clientId: this.dataset.clientid,
        url: this.href,
        clientName: this.textContent,
        recordCount: getPatientRecordCount(this)
    }]);
});

function startPatientChartMigration(patient) {
    if (migrationFrames[patient.clientId]) {
        if (migrationFrames[patient.clientId].status !== "failed") {
            return migrationFrames[patient.clientId];
        }
        discardFailedMigration(patient.clientId);
    }

    var iframe = document.createElement("iframe");
    iframe.title = "Dental chart migration for " + patient.clientName;
    iframe.src = patient.url + (patient.url.indexOf("?") === -1 ? "?" : "&") +
        "embedded=1&batch=" + currentBatch.number;
    iframe.style.width = "1px";
    iframe.style.height = "1px";
    iframe.style.border = "0";

    var migration = {
        clientId: String(patient.clientId),
        clientName: patient.clientName,
        iframe: iframe,
        status: "running",
        batchNumber: currentBatch.number
    };
    migrationFrames[migration.clientId] = migration;
    currentBatch.pendingClientIds[migration.clientId] = true;
    document.getElementById("migrationFramePool").appendChild(iframe);
    renderBatchPatientList();
    return migration;
}

function discardFailedMigration(clientId) {
    var previous = migrationFrames[clientId];
    if (!previous || previous.status !== "failed") {
        return;
    }

    previous.iframe.remove();
    delete migrationFrames[clientId];
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
    ).map(function (link) {
        return {
            clientId: link.dataset.clientid,
            url: link.href,
            clientName: link.textContent,
            recordCount: getPatientRecordCount(link)
        };
    });
    var pendingPatients = patients.filter(function (patient) {
        var existing = migrationFrames[patient.clientId];
        return !existing || existing.status === "failed";
    });

    if (pendingPatients.length === 0) {
        autoStartNextBatch = false;
        setMigrationProgress("No unstarted patient migrations in this report.", "secondary");
        return;
    }

    autoStartNextBatch = Boolean(automatic);
    currentBatch = {
        number: ++batchNumber,
        totalRecords: pendingPatients.reduce(function (total, patient) {
            return total + patient.recordCount;
        }, 0),
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

    document.getElementById("batchNumberProgress").textContent =
        currentBatch.number;
    document.getElementById("batchCountdownProgress").textContent = "";
    updateBatchProgress();
    $("#dentalChartMigrationModal").modal("show");

    pendingPatients.forEach(startPatientChartMigration);
    renderBatchPatientList();
    setMigrationProgress(
        "Batch " + currentBatch.number + " running in the background.",
        "info"
    );
}

function getPatientRecordCount(link) {
    var row = link.closest("tr");
    var countCell = row ? row.cells[3] : null;
    return countCell ? parseInt(countCell.textContent.replace(/,/g, ""), 10) || 0 : 0;
}

function recordToothSaveResult(migration, succeeded) {
    if (!currentBatch || migration.batchNumber !== currentBatch.number) {
        return;
    }

    if (succeeded) {
        currentBatch.successfulSaves++;
    } else {
        currentBatch.failedSaves++;
    }
    updateBatchProgress();
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
    currentBatch.patients.forEach(function (patient) {
        if (patient.clientId === migration.clientId) {
            patient.status = migration.status;
        }
    });

    if (succeeded) {
        markPatientRemarksCompleted(migration.clientId);
    } else {
        currentBatch.failedPatients++;
        addPatientMigrationFailure(migration.clientId);
    }

    updateBatchProgress();

    if (Object.keys(currentBatch.pendingClientIds).length === 0) {
        completeCurrentBatch();
    }
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
    if (!remarksCell) {
        return;
    }
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

function addPatientMigrationFailure(clientId) {
    var patientLink = document.querySelector(
        '#responseBody .patient-chart-link[data-clientid="' + clientId + '"]'
    );
    var patientRow = patientLink ? patientLink.closest("tr") : null;
    var remarksCell = patientRow ? patientRow.querySelector(".migration-remarks") : null;
    if (remarksCell && !remarksCell.textContent) {
        remarksCell.textContent = "Migration Failed";
        remarksCell.className += " text-danger";
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
    if (batchCountdownTimer) {
        window.clearInterval(batchCountdownTimer);
        batchCountdownTimer = null;
    }

    completedBatches++;
    var hasFailures = currentBatch.failedSaves > 0 || currentBatch.failedPatients > 0;
    if (hasFailures) {
        autoStartNextBatch = false;
        currentBatch.autoContinue = false;
        document.getElementById("batchCountdownProgress").textContent =
            "Automatic batches paused because failures need review.";
        setMigrationProgress(
            "Batch " + currentBatch.number + " finished with failures. Review before restarting.",
            "warning"
        );
        updateBatchProgress();
        return;
    }

    if (!currentBatch.autoContinue) {
        document.getElementById("batchCountdownProgress").textContent =
            "Batch completed successfully.";
        setMigrationProgress("Batch " + currentBatch.number + " completed.", "success");
        updateBatchProgress();
        return;
    }

    var remainingSeconds = 5;
    document.getElementById("batchCountdownProgress").textContent =
        "Batch completed. Refreshing report in " + remainingSeconds + " seconds...";
    setMigrationProgress("Batch " + currentBatch.number + " completed successfully.", "success");
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
            ).map(function (link) {
                return {
                    clientId: link.dataset.clientid,
                    url: link.href,
                    clientName: link.textContent,
                    recordCount: getPatientRecordCount(link)
                };
            }).filter(function (patient) {
                var existing = migrationFrames[patient.clientId];
                return !existing || existing.status === "failed";
            });

            if (remainingPatients.length === 0) {
                autoStartNextBatch = false;
                document.getElementById("batchCountdownProgress").textContent =
                    "No new patients remain. All available batches are done.";
                setMigrationProgress(
                    "Migration finished. " + completedBatches + " batches completed.",
                    "success"
                );
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

    document.getElementById("batchNumberProgress").textContent =
        currentBatch.number;
    document.getElementById("completedBatchesProgress").textContent =
        completedBatches;

    var total = currentBatch.totalRecords;
    var processed = currentBatch.successfulSaves + currentBatch.failedSaves;
    var successWidth = total > 0 ? Math.min(100, currentBatch.successfulSaves / total * 100) : 0;
    var failureWidth = total > 0 ? Math.min(100 - successWidth, currentBatch.failedSaves / total * 100) : 0;
    var successBar = document.getElementById("successfulSaveProgress");
    var failureBar = document.getElementById("failedSaveProgress");
    successBar.style.width = successWidth + "%";
    successBar.setAttribute("aria-valuenow", Math.round(successWidth));
    successBar.textContent = "Successful: " + currentBatch.successfulSaves;
    failureBar.style.width = failureWidth + "%";
    failureBar.setAttribute("aria-valuenow", Math.round(failureWidth));
    failureBar.textContent = "Failed: " + currentBatch.failedSaves;
    document.getElementById("processedRecordsProgress").textContent =
        "Save calls processed: " + processed + " / " + total +
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
            badge.textContent = patient.status === "failed" ? "Needs review" : "Done";
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
    var limit = document.getElementById("limit").value;
    var lastNameInitial = document.getElementById("lastNameInitial").value;
    document.getElementById("h3id").textContent =
        "LAST NAME: " + (lastNameInitial || "ALL") + " | LIMIT: " + limit + " PATIENTS";
    $("#loading").fadeIn();

    var fd = new FormData();
    fd.append("limit", limit);
    fd.append("lastNameInitial", lastNameInitial);

    $.ajax({
        url: "services/unmigratedToothRemarksService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: "POST",
        success: function (result) {
            document.getElementById("responseBody").innerHTML = result;
            if (typeof onSuccess === "function") {
                onSuccess();
            } else if (!currentBatch || Object.keys(currentBatch.pendingClientIds).length === 0) {
                setMigrationProgress(
                    "Ready - click Start Migration to process the next batch.",
                    "secondary"
                );
            }
        },
        error: function () {
            document.getElementById("responseBody").textContent =
                "Unable to load the unmigrated tooth remarks report. Please try again.";
            autoStartNextBatch = false;
            setMigrationProgress("Unable to load the report.", "danger");
            document.getElementById("batchCountdownProgress").textContent =
                "Automatic migration paused because report refresh failed.";
        },
        complete: function () {
            $("#loading").fadeOut();
        }
    });
}

function updateMigrationProgress() {
    if (!currentBatch) {
        return;
    }
    setMigrationProgress(
        "Batch " + currentBatch.number + " running in background: " +
        Object.keys(currentBatch.pendingClientIds).length + " patient charts active.",
        "info"
    );
}

function setMigrationProgress(message, type) {
    var indicator = document.getElementById("migrationProgress");
    indicator.className = "badge badge-" + type;
    indicator.textContent = message;
}
