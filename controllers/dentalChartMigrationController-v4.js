loadDentalChartForMigrationV2();

function loadDentalChartForMigrationV2() {
    var formData = new FormData();
    formData.append("id", document.getElementById("clientid").value);

    $.ajax({
        url: "services/dentalChartMigrationService.php",
        data: formData,
        processData: false,
        contentType: false,
        type: "POST",
        success: function (result) {
            document.getElementById("dental-chart-region").innerHTML = result;
            migratePatientLegacyDentalChartsV2().then(function (migrationSucceeded) {
                var messageType = migrationSucceeded
                    ? "dental-chart-migration-completed"
                    : "dental-chart-migration-incomplete";
                if (window.parent !== window) {
                    window.parent.postMessage({
                        type: messageType,
                        clientId: document.getElementById("clientid").value,
                        clientName: document.getElementById("chartPatientName").textContent.trim()
                    }, window.location.origin);
                }
            });
        },
        error: function () {
            document.getElementById("dental-chart-region").innerHTML =
                '<div class="alert alert-danger mb-0" role="alert">Unable to load the dental chart. Please try again.</div>';
            document.getElementById("dental-chart-migration-status").textContent = "Unable to load";
            if (window.parent !== window) {
                window.parent.postMessage({
                    type: "dental-chart-migration-incomplete",
                    clientId: document.getElementById("clientid").value,
                    clientName: document.getElementById("chartPatientName").textContent.trim()
                }, window.location.origin);
            }
        }
    });
}

async function migratePatientLegacyDentalChartsV2() {
    var legacyTeeth = Array.from(
        document.querySelectorAll('#dental-chart-region .tooth[data-legacy="true"]')
    );
    var status = document.getElementById("dental-chart-migration-status");
    if (legacyTeeth.length === 0) {
        status.textContent = "Migrated";
        return true;
    }

    status.textContent = "Preparing " + legacyTeeth.length + " legacy tooth records";
    legacyTeeth.forEach(function (tooth) {
        tooth.dataset.migrating = "true";
    });

    try {
        var records = await Promise.all(legacyTeeth.map(async function (tooth) {
            var image = tooth.querySelector("img");
            await waitForImage(image);
            return {
                tooth: tooth.dataset.tooth,
                remarks: tooth.dataset.remarks || "-",
                regions: readLegacyRegionColors(image)
            };
        }));

        status.textContent = "Saving " + records.length + " records in one request";
        var formData = new FormData();
        formData.append("clientid", document.getElementById("clientid").value);
        formData.append("records", JSON.stringify(records));

        var response = await fetch("dentalcharts/save_remarks_v2.php", {
            method: "POST",
            body: formData
        });
        if (!response.ok) {
            throw new Error("The server returned HTTP " + response.status);
        }

        var result = await response.json();
        if (result.status !== "success" || result.saved_count !== records.length) {
            throw new Error(result.message || "The server did not confirm every tooth record.");
        }

        records.forEach(function (record, index) {
            updateToothDisplay(legacyTeeth[index], record.remarks, record.regions);
            legacyTeeth[index].dataset.migrating = "false";
        });
        status.textContent = "Migration Completed";
        return true;
    } catch (error) {
        legacyTeeth.forEach(function (tooth) {
            tooth.dataset.migrating = "false";
        });
        console.error("Patient dental chart migration failed:", error);
        status.textContent = "Migration Failed";
        toastError("The patient chart could not be migrated. The legacy records remain available for retry.");
        return false;
    }
}
