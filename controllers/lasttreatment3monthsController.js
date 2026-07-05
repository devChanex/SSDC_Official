changeDateToday("asOf");
getclientdata();

function getclientdata() {
    var group = document.getElementById("group").value;
    var asOf = document.getElementById("asOf").value;
    document.getElementById("h3id").innerHTML = "As of: " + asOf;
    $("#loading").fadeIn();
    var fd = new FormData();
    fd.append("asOf", asOf);
    fd.append("group", group);
    $.ajax({
        url: "services/lasttreatment3monthsService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            document.getElementById("responseBody").innerHTML = result;
        },
        complete: function () {
            $("#loading").fadeOut();
        }
    });
    document.getElementById("content-table").style.zoom = "60%";
}

function notifyPatients() {
    var rows = document.querySelectorAll('#responseBody table tbody tr');
    if (rows.length === 0) {
        toastError('No patients available to notify. Please load the report first.');
        return;
    }

    $('#notifyConfirmModal').modal('show');
}

function confirmNotifyPatients() {
    $('#notifyConfirmModal').modal('hide');

    var rows = document.querySelectorAll('#responseBody table tbody tr');
    var emailsSent = 0;
    var emailsSkipped = 0;
    var pending = 0;
    var completed = 0;

    rows.forEach(function (row) {
        var emailCell = row.cells[2];
        var nameCell = row.cells[1];
        if (!emailCell || !nameCell) {
            emailsSkipped++;
            return;
        }

        var email = emailCell.textContent.trim();
        var fullName = nameCell.textContent.trim();
        if (!email) {
            emailsSkipped++;
            return;
        }

        var subject = 'Reminder: You\'re Due for Your Dental Cleaning';
        var greetings = fullName || ',';
        var msg = 'Hi ' + greetings + '\n\n' +
            'This is a friendly reminder that you’re due for your 6-month dental cleaning and checkup.\n\n' +
            'Regular dental visits help keep your smile healthy and prevent future dental problems.\n\n' +
            'To schedule your appointment, please contact Smile Save Dental Care at:\n' +
            '(049) 539 0277 or 0919 009 3099\n\n' +
            'Note: This is a system-generated email. Please do not reply directly to this email.\n\n' +
            'Thank you, and we look forward to seeing you!\n\n' +
            'Smile Save Dental Care';
        pending++;
        sendNotificationEmail(email, subject, greetings, msg, function (success) {
            completed++;
            if (success) {
                emailsSent++;
            } else {
                emailsSkipped++;
            }
            if (completed === pending) {
                toastSuccess('Notification process complete.<br> Sent: ' + emailsSent + ',<br>skipped: ' + emailsSkipped + '.');
            }
        });
    });

    if (pending === 0) {
        toastError('No valid email addresses found to notify.');
    }
}

function sendNotificationEmail(to, subject, greetings, msg, callback) {
    var fd = new FormData();
    fd.append('to', to);
    fd.append('subject', subject);
    fd.append('greetings', greetings);
    fd.append('msg', msg);

    $.ajax({
        url: 'services/mailerService.php',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function () {
            callback(true);
        },
        error: function () {
            callback(false);
        }
    });
}
