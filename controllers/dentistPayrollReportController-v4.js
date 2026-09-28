document.addEventListener('DOMContentLoaded', function () {
    // Automatically load report if the dentist list already has a selected value.
    if (document.getElementById('dentist').value !== '') {
        loadPayrollReport();
    }
});

function loadPayrollReport() {

    var dentist = document.getElementById('dentist').value;
    var from = document.getElementById('from').value;
    var to = document.getElementById('to').value;

    if (!dentist) {
        document.getElementById('responseBody').innerHTML = '<div class="alert alert-warning">Please select a dentist before loading the report.</div>';
        document.getElementById('payrollTotals').style.display = 'none';
        return;
    }

    var subtitle = 'Date Range: ' + (from || '-') + ' to ' + (to || '-') + ' | Dentist: ' + dentist;
    var subtitleEl = document.getElementById('reportSubtitle');
    if (subtitleEl) {
        subtitleEl.innerText = subtitle;
    }
    document.getElementById('payslipDentist').innerText = dentist;
    document.getElementById('payslipPeriod').innerText = (from || '-') + ' to ' + (to || '-');


    var fd = new FormData();
    fd.append('dentist', dentist);
    fd.append('from', from);
    fd.append('to', to);

    document.getElementById('loading').style.display = 'block';
    document.getElementById('responseBody').innerHTML = '';


    $.ajax({
        url: 'services/dentistPayrollReportService.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            document.getElementById('responseBody').innerHTML = result;
            loadPayrollAdjustmentReport(); // Load the payroll adjustment report after the main report is loaded

        },
        complete: function () {
            document.getElementById('loading').style.display = 'none';
            recalculateTotal();

        }
    });
}

function loadPayrollAdjustmentReport() {

    var dentist = document.getElementById('dentist').value;
    var from = document.getElementById('from').value;
    var to = document.getElementById('to').value;

    if (!dentist) {
        document.getElementById('responseBody-dentistPayrollAdjustments').innerHTML = '';
        return;
    }
    var fd = new FormData();
    fd.append('dentist', dentist);
    fd.append('from', from);
    fd.append('to', to);

    document.getElementById('loading').style.display = 'block';
    document.getElementById('responseBody-dentistPayrollAdjustments').innerHTML = '';


    $.ajax({
        url: 'services/dentistPayrollReportSubService.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            document.getElementById('responseBody-dentistPayrollAdjustments').innerHTML = result;
            recalculateNetPay(); // Recalculate net pay after loading the payroll adjustment report

        },
        complete: function () {
            document.getElementById('loading').style.display = 'none';
        }
    });
}

function printPayroll() {
    window.print();
}

function openBasicSalaryModal() {
    $('#basicSalaryModal').modal('show');
}

function openPayslipModal() {
    $('#payslipModal').modal('show');
}

function updateBasicSalary() {

    const daysRendered = parseFloat(
        document.getElementById('daysRendered').value
    ) || 0;

    const ratePerDay = parseFloat(
        document.getElementById('ratePerDay').value
    ) || 0;

    const basicSalary = daysRendered * ratePerDay;

    // Update details
    document.getElementById('basicSalaryDetails').textContent =
        'Days Rendered: ' + daysRendered +
        ', Rate Per Day: ' +
        ratePerDay.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    // Update salary amount
    document.getElementById('basicSalaryAmount').textContent =
        basicSalary.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    // Update salary amount
    document.getElementById('totalBasicSalary').textContent =
        basicSalary.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    recalculateNetPay(); // Recalculate net pay after updating basic salary
    // Close modal
    $('#basicSalaryModal').modal('hide');

}



function updateComissionAmount(thisObject) {
    // Get the current table row
    const row = thisObject.closest('tr');

    // Get price and commission cells
    const priceCell = row.querySelector('.price');
    const rawMaterialCell = row.querySelector('.raw_material');
    const commissionCell = row.querySelector('.commission');
    const select = row.querySelector('select[name="commision_rate"]');
    if (!select) {
        return;
    }
    // Get price as a number
    const price = parseFloat(priceCell.textContent.replace(/,/g, ''));
    const rawMaterial = parseFloat(rawMaterialCell.querySelector('input').value) || 0;
    let commission;

    if (select.value === 'Other') {
        if (thisObject.matches('select[name="commision_rate"]')) {
            const amount = prompt('Enter commission amount:');

            if (amount === null) {
                select.value = '0';
                commission = 0;
            } else {
                commission = parseFloat(amount.replace(/,/g, ''));
            }

            if (isNaN(commission) || commission < 0) {
                alert('Please enter a valid commission amount.');
                select.value = '0';
                commission = 0;
            }
        } else {
            commission = parseFloat(commissionCell.textContent.replace(/,/g, '')) || 0;
        }
    } else {
        const rate = parseFloat(select.value);
        commission = (price - rawMaterial) * (rate / 100);
    }

    commissionCell.textContent = commission.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    $.ajax({
        url: 'services/updateDentistPayrollCommissionService.php',
        type: 'POST',
        dataType: 'json',
        data: {
            tsubid: row.dataset.tsubid,
            raw_material: rawMaterial,
            commision_rate: select.value,
            commision: commission
        },
        error: function (xhr) {
            console.error('Unable to save treatment commission:', xhr.responseText);
        }
    });

    recalculateTotal();
}

function recalculateTotal() {
    let total = 0;
    document.querySelectorAll('#commissionTable .commission').forEach(function (cell) {
        let amount = parseFloat(cell.textContent.replace(/,/g, ''));

        if (!isNaN(amount)) {
            total += amount;
        }
    });

    document.getElementById('totalCommission').textContent =
        total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    document.getElementById('totalGross').textContent =
        (total * .10).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });



    recalculateNetPay(); // Recalculate net pay after updating total commission 
}


function recalculateNetPay() {
    document.getElementById('totalGrossPay').textContent =
        ((parseFloat(document.getElementById('totalCommission').textContent.replace(/,/g, '') || 0) * .90) + parseFloat(document.getElementById('totalBasicSalary').textContent.replace(/,/g, '') || 0) + parseFloat(document.getElementById('totalAdditional').textContent.replace(/,/g, '') || 0)).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });


    const totalGrossPay = parseFloat(
        document.getElementById('totalGrossPay').textContent.replace(/,/g, '')
    ) || 0;



    const totalDeductions = parseFloat(
        document.getElementById('totalDeductions').textContent.replace(/,/g, '')
    ) || 0;

    const netPay =
        totalGrossPay -

        totalDeductions;

    document.getElementById('netpay').textContent =
        netPay.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    populatePayslip();
}


function populatePayslip() {

    // =========================
    // Employee / Filter Details
    // =========================

    const dentist = document.getElementById('dentist').value;
    const from = document.getElementById('from').value;
    const to = document.getElementById('to').value;

    document.getElementById('payslipDentist').textContent =
        dentist || '-';

    // Format dates
    if (from && to) {
        const fromDate = new Date(from + 'T00:00:00');
        const toDate = new Date(to + 'T00:00:00');

        const dateOptions = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };

        document.getElementById('payslipPeriods').textContent =
            fromDate.toLocaleDateString('en-US', dateOptions) +
            ' - ' +
            toDate.toLocaleDateString('en-US', dateOptions);
    } else {
        document.getElementById('payslipPeriods').textContent = '-';
    }

    // Pay date = "To" date



    // =========================
    // Payroll Values
    // =========================
    const commission =
        parseFloat(document.getElementById('totalCommission').textContent.replace(/,/g, '')) || 0;
    const commission_less =
        parseFloat(document.getElementById('totalGross').textContent.replace(/,/g, '')) || 0;

    const basicSalary = parseFloat(
        document.getElementById('totalBasicSalary').textContent.replace(/,/g, '')
    ) || 0;

    const additional =
        parseFloat(document.getElementById('totalAdditional').textContent.replace(/,/g, '')) || 0;

    const gross = basicSalary + additional + commission - commission_less;

    const deductions =
        document.getElementById('totalDeductions').textContent;

    const lessDeductions =
        document.getElementById('totalGross').textContent;

    const netPay =
        document.getElementById('netpay').textContent;




    // =========================
    // Populate Payslip
    // =========================
    document.getElementById('payslipCommission').textContent = (commission - commission_less).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });;

    document.getElementById('payslipBasicSalary').textContent =
        basicSalary.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

    document.getElementById('payslipAdditional').textContent =
        additional.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

    document.getElementById('payslipGrossPay').textContent =
        gross.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

    document.getElementById('payslipDeductions').textContent =
        deductions.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;


    document.getElementById('payslipTaxMaterial').textContent =
        lessDeductions.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

    document.getElementById('payslipOtherDeductions').textContent =
        deductions.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });;

    document.getElementById('payslipNetPay').textContent =
        netPay.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    document.getElementById('payslipDaysRendered').textContent = "Days Rendered: " + (document.getElementById('daysRendered').value || ' Days Rendered: 0.00');
    document.getElementById('payslipRatePerDay').textContent = "Rate Per Day:" + (document.getElementById('ratePerDay').value || 'Rate Per Day: 0.00');

    document.getElementById('payslipDentists').textContent = document.getElementById('dentist').value || '-';
}