$(document).ready(function () {
  display_events();
}); //end document.ready block


loadTreatment();
function loadTreatment() {
  var fd = new FormData();
  $.ajax({
    url: "services/OptionloadTreatmentService.php",
    data: fd,
    processData: false,
    contentType: false,
    type: 'POST',
    success: function (result) {
      document.getElementById("treatment").innerHTML = result;
    }
  });

}

const defaultRadio = document.getElementById("defaultEvent");
const customRadio = document.getElementById("customEvent");

const defaultContainer = document.getElementById("defaultEventContainer");
const customContainer = document.getElementById("customEventContainer");

function toggleEventInput() {
  if (defaultRadio.checked) {
    defaultContainer.style.display = "block";
    customContainer.style.display = "none";
  } else {
    defaultContainer.style.display = "none";
    customContainer.style.display = "block";
  }
}

defaultRadio.addEventListener("change", toggleEventInput);
customRadio.addEventListener("change", toggleEventInput);

// Initialize on page load
toggleEventInput();


function display_events() {

  $.ajax({
    url: 'display_event.php',
    dataType: 'json',
    success: function (response) {

      var events = [];

      $.each(response.data, function (i, item) {

        events.push({
          event_id: item.event_id,
          event_name: item.event_name,
          event_start_date: item.event_start_date,
          event_end_date: item.event_end_date,
          event_time: item.event_time,
          patient: item.patient,
          dentist: item.dentist,
          contact: item.contact,
          hmo: item.hmo,
          event_timeto: item.event_timeto,
          treatment: item.treatment,
          title: item.title,
          start: item.start,
          end: item.end,
          color: item.color,
          url: item.url,
          event_status: item.event_status
        });

      });

      // Destroy existing calendar before recreating
      if ($('#calendar').data('fullCalendar')) {
        $('#calendar').fullCalendar('destroy');
      }

      $('#calendar').fullCalendar({
        defaultView: 'month',
        timeZone: 'local',
        editable: true,
        selectable: true,
        selectHelper: true,

        // Hide the default time displayed by FullCalendar
        displayEventTime: false,

        events: events,

        select: function (start, end) {
          $('#event_id').val('');
          $('#delete_event_button').prop('disabled', true);
          $('#modalLabel').text('Appointment');
          $('#customEvent').prop('checked', true);
          $("input[name='eventStatus'][value='Confirmed']").prop('checked', true);
          toggleEventInput();
          $('#event_start_date').val(moment(start).format('YYYY-MM-DD'));
          $('#event_end_date').val(moment(end).format('YYYY-MM-DD'));
          $('#event_entry_modal').modal('show');
        },

        eventRender: function (event, element) {
          element[0].style.setProperty('--event-accent', event.color || '#3a87ad');

          var eventContent = element.find('.fc-content').empty();
          var specialEvents = {
            'closed': { icon: 'fa-ban' },
            'fully booked': { icon: 'fa-calendar-check' },
            'holiday-closed': { icon: 'fa-calendar-times' }
          };
          var eventName = event.event_name || 'Appointment';
          var specialEvent = specialEvents[eventName.trim().toLowerCase()];

          if (specialEvent) {
            var specialNotice = $('<div>', { 'class': 'calendar-event-card__notice' })
              .append($('<i>', { 'class': 'fas ' + specialEvent.icon, 'aria-hidden': 'true' }))
              .append($('<span>').text(eventName));
            eventContent.append(specialNotice);
          } else {
            var eventHeader = $('<div>', { 'class': 'calendar-event-card__header' });
            var eventDetails = $('<div>', { 'class': 'calendar-event-card__details' });
            var detailLines = eventName.split(/\r?\n/);

            $.each(detailLines, function (index, line) {
              if (!line.trim()) {
                return;
              }

              var separatorIndex = line.indexOf(':');
              var detailLine = $('<div>', { 'class': 'calendar-event-card__detail-line' });

              if (separatorIndex > 0) {
                var field = line.substring(0, separatorIndex).trim();
                var value = line.substring(separatorIndex + 1).trim();
                detailLine.append($('<strong>').text(field + ':'));

                if (value) {
                  detailLine.append(document.createTextNode(' ' + value));
                }
              } else {
                detailLine.text(line.trim());
              }

              eventDetails.append(detailLine);
            });

            if (event.event_time) {
              var formattedTime = moment(event.event_time, ['HH:mm:ss', 'HH:mm']).format('h:mm A');
              var eventTime = $('<div>', { 'class': 'calendar-event-card__time' })
                .append($('<i>', { 'class': 'fas fa-clock', 'aria-hidden': 'true' }))
                .append($('<span>').text(formattedTime));
              eventHeader.append(eventTime);
            }

            eventContent.append(eventHeader, eventDetails);
          }

          var status = (event.event_status || '').toLowerCase();
          var statusIcons = {
            confirmed: { icon: 'fa-clock', label: 'Confirmed' },
            completed: { icon: 'fa-check-circle', label: 'Completed' },
            cancelled: { icon: 'fa-ban', label: 'Cancelled' }
          };
          var statusInfo = statusIcons[status];

          if (statusInfo) {
            var statusIndicator = $('<span>', {
              'class': 'calendar-event-status calendar-event-status--' + status,
              title: statusInfo.label,
              'aria-label': 'Event status: ' + statusInfo.label
            }).append($('<i>', { 'class': 'fas ' + statusInfo.icon, 'aria-hidden': 'true' }));

            element.append(statusIndicator);
          }

          element.on('click', function (clickEvent) {
            clickEvent.preventDefault();
            $('#event_id').val(event.event_id);
            $('#event_start_date').val(event.event_start_date);
            $('#event_end_date').val(event.event_end_date);
            $('#appointmentTime').val(event.event_time.substring(0, 5));
            $("input[name='eventStatus'][value='" + event.event_status + "']").prop('checked', true);

            $('#patientName').val(event.patient || '');
            $('#dentist').val(event.dentist || '');
            $('#contactNumber').val(event.contact || '');
            var hmoValue = (event.hmo || '').trim();
            var hmoSelect = $('#hmo');
            if (hmoValue && !hmoSelect.find('option').filter(function () {
              return this.value === hmoValue;
            }).length) {
              hmoSelect.append($('<option>', { value: hmoValue, text: hmoValue }));
            }
            hmoSelect.val(hmoValue);


            var treatmentValue = (event.treatment || '').trim();
            var treatmentSelect = $('#treatment');
            if (treatmentValue && !treatmentSelect.find('option').filter(function () {
              return this.value === treatmentValue;
            }).length) {
              treatmentSelect.append($('<option>', { value: treatmentValue, text: treatmentValue }));
            }
            treatmentSelect.val(treatmentValue);
            $('#appointmentTimeto').val(event.event_timeto.substring(0, 5));

            if (['Closed', 'Fully Booked', 'Holiday-Closed'].indexOf(event.event_name) !== -1) {
              $('#defaultEvent').prop('checked', true);
              $('#event_name').val(event.event_name);
            } else {
              $('#customEvent').prop('checked', true);
              $('#custom_event_name').val(event.event_name);
            }

            toggleEventInput();
            $('#modalLabel').text('Edit Appointment');
            $('#delete_event_button').prop('disabled', false);
            $('#event_entry_modal').modal('show');
            return false;
          });
        }
      });

    },

    error: function (xhr, status, error) {
      console.log(error);
    }

  });

}
function eventDeletion(id) {

  if (!id) {
    return false;
  }

  var x = confirm("Are you sure you want to delete this event?");
  if (x) {

    var fd = new FormData();
    fd.append('id', id);
    $.ajax({
      url: "services/deleteCalendarEvent.php",
      data: fd,
      processData: false,
      contentType: false,
      type: 'POST',
      success: function (result) {
        result = result.trim();
        if (result == "success") {
          location.reload();
        } else {
          alert("An error has occured during event deletion. Try again.");
        }
      }
    });
  }

}
function save_event() {

  let event_name = "";

  var patientName = $("#patientName").val().trim();
  var dentistName = $("#dentist").val().trim();
  var contactNumber = $("#contactNumber").val().trim();
  var event_time_to = $("#appointmentTimeto").val();
  var hmo = $("#hmo").val().trim();
  var treatment = $("#treatment").val().trim();

  // Get value based on selected radio button
  if ($("#defaultEvent").is(":checked")) {
    event_name = $("#event_name").val();
  } else {
    // event_name = $("#custom_event_name").val().trim();
    event_name = "Patient: " + patientName + "\nDentist: " + dentistName + "\nContact: " + contactNumber + "\nTime: " + $("#appointmentTime").val() + "\nTreatment: " + treatment + "\nHMO: " + hmo;
  }

  var event_start_date = $("#event_start_date").val();
  var event_end_date = $("#event_end_date").val();

  if (event_name == "" || event_start_date == "" || event_end_date == "") {
    alert("Please enter all required details.");
    return false;
  }
  var event_time = $("#appointmentTime").val(); // e.g. "14:30"
  var eventId = $("#event_id").val();


  var eventStatus = $("input[name='eventStatus']:checked").val();



  $.ajax({
    url: eventId ? "update_event.php" : "save_event.php",
    type: "POST",
    dataType: "json",
    data: {
      event_id: eventId,
      event_name: event_name,
      event_start_date: event_start_date,
      event_end_date: event_end_date,
      event_time: event_time,
      event_time_to: event_time_to,
      event_status: eventStatus,
      patientName: patientName,
      dentistName: dentistName,
      contactNumber: contactNumber,
      hmo: hmo,
      treatment: treatment
    },
    success: function (response) {
      if (response.status == true) {
        $("#event_entry_modal").modal("hide");
        location.reload();
      } else {
        alert(response.msg || "Event was not added, but the server did not provide an error message.");
      }
    },
    error: function (xhr, status) {
      console.error("Save event request failed:", status, xhr.status, xhr.responseText, xhr.responseJSON);
      var message = xhr.responseJSON && xhr.responseJSON.msg
        ? xhr.responseJSON.msg
        : "The server request failed (" + xhr.status + "): " + (xhr.statusText || status);
      alert(message);
    }
  });

  return false;
}