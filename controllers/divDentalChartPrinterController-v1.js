

function printDental(name) {

    var note = document.getElementById("dentalNote").value;
    var formattedNote = note.replace(/\n/g, "<br>");

    document.getElementById("dentalChartNoteField").innerHTML = "<div>" + formattedNote + "</div>";
    var divToPrint = document.getElementById("dental-chart-region");
    var newWindow = window.open('', '_blank');

    newWindow.document.write(`
        <html>
        <head>
            <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
             <link href="css/dentalchart.css" rel="stylesheet">

            <title>Print</title>
            <style>
                @media print {
                    body {
                        margin: 0.5in;
                        font-family: Arial, sans-serif !important;
                        font-size: 8pt !important;
                        color: #000 !important;
                    }

                    table, th, td {
                        font-size: 8pt !important;
                    }

                    .table {
                        border-collapse: collapse !important;
                        width: 100% !important;
                    }

                    .table th, .table td {
                        padding: 4px !important;
                        border: 0px solid #000 !important;
                    }

                       .signatory {
                margin-top: 50px;
                text-align: center;
            }

            .signatory img {
                max-height: 80px;
                margin-bottom: 5px;
            }

            .signatory-line {
                border-top: 1px solid #000;
                width: 250px;
                margin: 0 auto;
                margin-top: 5px;
            }
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            <div class="print-area">
               <div>
                                <div style=" text-align:left;">
                                    <h2 style="margin:0; font-weight:bold; font-size: 1.5rem;">Smile Save Dental Care
                                    </h2>
                                    <div style="font-size:14px;">L22-24 B2 2/F Mondo Bambini Commercial Strip Bldg. Brgy. Zapote, Binan City, Laguna</div>
                                     <div style="font-size:14px;">Contact: 0919 009 3099 / (049) 539 0277</div>
                                </div>
                                <hr>
                                <div style="text-align:center;">
                                    <h2 style="margin:0; font-weight:bold;">Patient Dental Chart</h2>
                                    <h5 id="h3id" style="margin:0;">${name}</h5>
                                </div>
                                <div style="width:180px;"></div> <!-- Spacer for symmetry, adjust width as needed -->
                            </div>
                            <br>
                ${divToPrint.innerHTML}

               

            <!-- Signatory Section -->
<div class="signatory" style="text-align:right; margin-top:50px;">
    <div style="display:inline-block; text-align:center;">
        <img src="img/e-sign.png" alt="Dentist Signature" style="max-height:80px; margin-bottom:5px;">
        <div style="border-top:1px solid #000; width:250px; margin-top:5px;"></div>
        <div style="margin-top:5px; font-weight:bold;">Dr. Maria Regina I. Valencia</div>
        <div style="font-size:10pt;">Attending Dentist</div>
    </div>
</div>

        </div>
            </div>
        </body>
        </html>
    `);

    newWindow.document.close();
}

