document.addEventListener('DOMContentLoaded', function () {
    // Automatically load report if the dentist list already has a selected value.
    if (document.getElementById('dentist').value !== '') {
        loadPayrollReport();
        loadPayrollAdjustmentReport();
        recalculateNetPay();
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



function updateComissionAmount(select) {
    // Get the current table row
    const row = select.closest('tr');

    // Get price and commission cells
    const priceCell = row.querySelector('.price');
    const commissionCell = row.querySelector('.commission');

    // Get price as a number
    const price = parseFloat(priceCell.textContent.replace(/,/g, ''));

    let commission;

    if (select.value === 'Other') {

        // Ask user for the commission amount
        let amount = prompt('Enter commission amount:');

        if (amount === null) {
            // User clicked Cancel
            select.value = '0';
            commissionCell.textContent = '0.00';
            return;
        }

        commission = parseFloat(amount.replace(/,/g, ''));

        // Validate input
        if (isNaN(commission) || commission < 0) {
            alert('Please enter a valid commission amount.');
            select.value = '0';
            commissionCell.textContent = '0.00';
            return;
        }

    } else {

        // Calculate percentage
        const rate = parseFloat(select.value);

        commission = price * (rate / 100);
    }

    // Update commission display
    commissionCell.textContent = commission.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
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
        (total * 0.9).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    recalculateNetPay(); // Recalculate net pay after updating total commission 
}


function recalculateNetPay() {

    const totalGross = parseFloat(
        document.getElementById('totalGross').textContent.replace(/,/g, '')
    ) || 0;


    const basicSalary = parseFloat(
        document.getElementById('totalBasicSalary').textContent.replace(/,/g, '')
    ) || 0;

    const totalAdditional = parseFloat(
        document.getElementById('totalAdditional').textContent.replace(/,/g, '')
    ) || 0;

    const totalDeductions = parseFloat(
        document.getElementById('totalDeductions').textContent.replace(/,/g, '')
    ) || 0;

    const netPay =
        totalGross +
        basicSalary +
        totalAdditional -
        totalDeductions;

    document.getElementById('netpay').textContent =
        netPay.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
}