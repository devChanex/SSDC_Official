loadDentalChartForMigration();

function loadDentalChartForMigration() {
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
            migrateLegacyDentalCharts().then(function () {
                var status = document.getElementById("dental-chart-migration-status").textContent;
                if (status === "Migration Completed" || status === "Migrated") {
                    if (window.parent !== window) {
                        window.parent.postMessage({
                            type: "dental-chart-migration-completed",
                            clientId: document.getElementById("clientid").value,
                            clientName: document.getElementById("chartPatientName").textContent.trim()
                        }, window.location.origin);
                    } else {
                        document.getElementById("dental-chart-migration-status").textContent =
                            "Migration Completed - closing...";
                        window.setTimeout(function () {
                            window.close();
                            window.setTimeout(function () {
                                if (!window.closed) {
                                    document.getElementById("dental-chart-migration-status").textContent =
                                        "Migration Completed - you may close this tab.";
                                }
                            }, 500);
                        }, 500);
                    }
                } else {
                    window.parent.postMessage({
                        type: "dental-chart-migration-incomplete",
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
        }
    });
}
