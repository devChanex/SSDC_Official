var calendarIsInitialized = false;
var appointmentsForDay = [];
var calendarUsesMobileEventLimit = null;

function isMobileOrTabletCalendar() {
  return window.matchMedia(
    '(max-width: 1399.98px), (pointer: coarse)'
  ).matches;
}

$(function () {
  bindCalendarControls();
  bindEventTypeControls();
  loadTreatment();
  display_events();
});

function bindCalendarControls() {
  $('#calendar').on('mouseenter.calendarMore', '.fc-more', function () {
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
      $(this).trigger('click');
    }
  });

  $('#calendar-prev').on('click', function () {
    $('#calendar').fullCalendar('prev');
  });

  $('#calendar-next').on('click', function () {
    $('#calendar').fullCalendar('next');
  });

  $('#calendar-today').on('click', function () {
    $('#calendar').fullCalendar('today');
  });

  $('#calendar-view').on('change', function () {
    $('#calendar').fullCalendar('changeView', this.value);
  });

  $('#calendar-create-event').on('click', function () {
    openNewEvent(moment(), null, true);
  });
}

function bindEventTypeControls() {
  $('#defaultEvent, #customEvent').on('change', toggleEventInput);
  toggleEventInput();
}

function toggleEventInput() {
  var isDefault = $('#defaultEvent').prop('checked');
  $('#defaultEventContainer').toggle(isDefault);
  $('#customEventContainer').toggle(!isDefault);
}

function loadTreatment() {
  $.ajax({
    url: 'services/OptionloadTreatmentService.php',
    type: 'POST',
    success: function (result) {
      $('#treatment').html(result);
    },
    error: function () {
      showCalendarFeedback('Unable to load the treatment list. Please refresh the page and try again.');
    }
  });
}

function display_events() {
  $.ajax({
    url: 'display_event.php',
    dataType: 'json',
    success: function (response) {
      var events = $.map(response && response.data ? response.data : [], function (item) {
        return {
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
          allDay: item.allDay,
          color: item.color,
          event_status: item.event_status
        };
      });

      hideCalendarFeedback();

      if (calendarIsInitialized) {
        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar('addEventSource', events);
        return;
      }

      calendarIsInitialized = true;
      calendarUsesMobileEventLimit = isMobileOrTabletCalendar();
      $('#calendar').fullCalendar({
        defaultView: 'month',
        timeZone: 'local',
        header: false,
        height: 'auto',
        aspectRatio: 1.55,
        firstDay: 0,
        selectable: true,
        selectHelper: true,
        editable: false,
        eventLimit: isMobileOrTabletCalendar() ? 1 : true,
        eventLimitText: 'more',
        eventLimitClick: function (cellInfo, jsEvent) {
          $('#calendar .fc-more-popover').remove();

          if (!isMobileOrTabletCalendar()) {
            return 'popover';
          }

          jsEvent.preventDefault();
          showDayAppointments(cellInfo.date);
          return false;
        },
        nowIndicator: true,
        displayEventTime: false,
        slotDuration: '00:30:00',
        minTime: '08:00:00',
        maxTime: '20:00:00',
        events: events,
        viewRender: function (view) {
          $('#calendar-title').text(view.title);
          $('#calendar-view').val(view.name);
        },
        dayClick: function (date, jsEvent, view) {
          var allDay = view.name === 'month' || (date.hasTime && !date.hasTime());
          if (allDay) {
            openNewEvent(date, null, true);
          } else {
            openNewEvent(date, date.clone().add(1, 'hour'), false);
          }
        },
        select: function (start, end, jsEvent, view) {
          var finalEnd = end.clone();
          var allDay = view.name === 'month' || (start.hasTime && !start.hasTime());
          if (allDay) {
            finalEnd.subtract(1, 'day');
          }
          openNewEvent(start, finalEnd, allDay);
          $('#calendar').fullCalendar('unselect');
        },
        eventRender: function (event, element) {
          applyEventColor(element[0], event.color || '#4169e1');

          var eventName = $.trim(event.event_name || 'Appointment');
          var specialEvents = {
            'closed': 'Clinic closed',
            'fully booked': 'Fully booked',
            'holiday-closed': 'Holiday closed'
          };
          var specialLabel = specialEvents[eventName.toLowerCase()];
          var title = specialLabel || event.patient || eventName.split(/\r?\n/)[0];
          var summary = $('<span>', { 'class': 'fc-event-summary' });
          summary.append($('<span>', { 'class': 'fc-event-summary__title' }).text(title));

          if (!specialLabel) {
            var meta = [];
            if (event.event_time) {
              meta.push(formatEventTime(event.event_time));
            }
            if (event.dentist) {
              meta.push(event.dentist);
            }
            if (meta.length) {
              summary.append($('<span>', { 'class': 'fc-event-summary__meta' }).text(meta.join(' · ')));
            }
            if (event.treatment) {
              summary.append(
                $('<span>', { 'class': 'fc-event-summary__meta' })
                  .text('Treatment: ' + event.treatment)
              );
            }
          }

          element.find('.fc-content').empty().append(summary);
          element.attr('title', eventName);

          var statuses = {
            confirmed: { icon: 'fa-clock', label: 'Confirmed' },
            completed: { icon: 'fa-check-circle', label: 'Completed' },
            cancelled: { icon: 'fa-ban', label: 'Cancelled' }
          };
          var status = statuses[(event.event_status || '').toLowerCase()];
          if (status) {
            element.append($('<span>', {
              'class': 'calendar-event-status calendar-event-status--' + event.event_status.toLowerCase(),
              title: status.label,
              'aria-label': 'Event status: ' + status.label
            }).append($('<i>', { 'class': 'fas ' + status.icon, 'aria-hidden': 'true' })));
          }
        },
        eventClick: function (event, jsEvent) {
          jsEvent.preventDefault();
          openExistingEvent(event);
          return false;
        }
      });
    },
    error: function (xhr, status, error) {
      console.error('Calendar events could not be loaded:', status, error);
      showCalendarFeedback('Unable to load calendar events. Please try again.');
    }
  });
}

function showDayAppointments(date) {
  var dayStart = moment(date).clone().startOf('day');
  var nextDay = dayStart.clone().add(1, 'day');
  var calendarEvents = $('#calendar').fullCalendar('clientEvents');
  appointmentsForDay = $.grep(calendarEvents, function (event) {
    if (!event.start) {
      return false;
    }

    var eventStart = moment(event.start);
    var eventEnd = event.end ? moment(event.end) : eventStart.clone();
    return eventStart.isBefore(nextDay) &&
      (eventEnd.isAfter(dayStart) || eventStart.isSame(dayStart, 'day'));
  });

  $('#day-appointments-title').text(
    'Appointments · ' + moment(date).format('dddd, MMMM D')
  );

  var list = $('#day-appointments-list').empty();
  if (!appointmentsForDay.length) {
    list.append(
      $('<p>', { 'class': 'text-muted text-center mb-0' }).text('No appointments for this day.')
    );
  }

  $.each(appointmentsForDay, function (index, event) {
    var isSpecial = ['closed', 'fully booked', 'holiday-closed']
      .indexOf((event.event_name || '').toLowerCase()) !== -1;
    var title = isSpecial
      ? event.event_name
      : (event.patient || (event.event_name || 'Appointment').split(/\r?\n/)[0]);
    var meta = [];

    if (!isSpecial && event.event_time) {
      meta.push(formatEventTime(event.event_time));
    }
    if (!isSpecial && event.dentist) {
      meta.push(event.dentist);
    }
    if (!isSpecial && event.treatment) {
      meta.push('Treatment: ' + event.treatment);
    }
    if (event.event_status) {
      meta.push(event.event_status);
    }

    var option = $('<button>', {
      type: 'button',
      'class': 'calendar-appointment-option'
    }).css('--event-accent', event.color || '#64748b');
    option.append($('<span>', { 'class': 'calendar-appointment-option__title' }).text(title));
    if (meta.length) {
      option.append(
        $('<span>', { 'class': 'calendar-appointment-option__meta' }).text(meta.join(' · '))
      );
    }
    option.data('appointment-index', index);
    list.append(option);
  });

  $('#day-appointments-modal').modal('show');
}

$(document).on('click', '.calendar-appointment-option', function () {
  var event = appointmentsForDay[$(this).data('appointment-index')];
  if (!event) {
    return;
  }

  $('#day-appointments-modal').one('hidden.bs.modal', function () {
    openExistingEvent(event);
  }).modal('hide');
});

$(window).on('resize', function () {
  if (!calendarIsInitialized) {
    return;
  }

  clearTimeout(window.calendarResizeTimer);
  window.calendarResizeTimer = setTimeout(function () {
    var useMobileLimit = isMobileOrTabletCalendar();
    if (useMobileLimit !== calendarUsesMobileEventLimit) {
      calendarUsesMobileEventLimit = useMobileLimit;
      $('#calendar').fullCalendar('option', 'eventLimit', useMobileLimit ? 1 : true);
    }
  }, 150);
});

function openNewEvent(start, end, allDay) {
  var startDate = moment(start);
  var endDate = end ? moment(end) : startDate.clone();

  $('#event_entry_modal input[type="text"]').val('');
  $('#event_id').val('');
  $('#modalLabel').text('New appointment');
  $('#event-modal-feedback').hide().text('');
  $('#delete_event_button').prop('disabled', true);
  $('#customEvent').prop('checked', true);
  $('#defaultEvent').prop('checked', false);
  $('#event_name').val('Closed');
  $("input[name='eventStatus'][value='Confirmed']").prop('checked', true);
  $('#appointmentTime').val(allDay ? '08:00' : startDate.format('HH:mm'));
  $('#appointmentTimeto').val(allDay ? '09:00' : endDate.format('HH:mm'));
  $('#event_start_date').val(startDate.format('YYYY-MM-DD'));
  $('#event_end_date').val(endDate.format('YYYY-MM-DD'));
  $('#hmo').val('');
  $('#treatment').val('');
  toggleEventInput();
  hideCalendarFeedback();
  $('#event_entry_modal').modal('show');
}

function openExistingEvent(event) {
  $('#event_id').val(event.event_id);
  $('#event_start_date').val(event.event_start_date);
  $('#event_end_date').val(event.event_end_date);
  $('#appointmentTime').val((event.event_time || '').substring(0, 5));
  $('#appointmentTimeto').val((event.event_timeto || '').substring(0, 5));
  $("input[name='eventStatus']").prop('checked', false);
  $("input[name='eventStatus']").filter(function () {
    return this.value === event.event_status;
  }).prop('checked', true);

  $('#patientName').val(event.patient || '');
  $('#dentist').val(event.dentist || '');
  $('#contactNumber').val(event.contact || '');
  setSelectValue('#hmo', event.hmo);
  setSelectValue('#treatment', event.treatment);

  var specialNames = ['closed', 'fully booked', 'holiday-closed'];
  if (specialNames.indexOf((event.event_name || '').toLowerCase()) !== -1) {
    $('#defaultEvent').prop('checked', true);
    $('#event_name').val(event.event_name);
  } else {
    $('#customEvent').prop('checked', true);
  }

  toggleEventInput();
  $('#modalLabel').text('Edit appointment');
  $('#event-modal-feedback').hide().text('');
  $('#delete_event_button').prop('disabled', false);
  hideCalendarFeedback();
  $('#event_entry_modal').modal('show');
}

function setSelectValue(selector, value) {
  var select = $(selector);
  var normalizedValue = $.trim(value || '');
  var hasOption = select.find('option').filter(function () {
    return this.value === normalizedValue;
  }).length > 0;

  if (normalizedValue && !hasOption) {
    select.append($('<option>', { value: normalizedValue, text: normalizedValue }));
  }

  select.val(normalizedValue);
}

function formatEventTime(time) {
  var parsedTime = moment(time, ['HH:mm:ss', 'HH:mm'], true);
  return parsedTime.isValid() ? parsedTime.format('h:mm A') : time;
}

function applyEventColor(element, color) {
  var hex = /^#([0-9a-f]{6})$/i.exec(color);
  if (!hex) {
    color = '#64748b';
    hex = /^#([0-9a-f]{6})$/i.exec(color);
  }

  var red = parseInt(hex[1].substring(0, 2), 16);
  var green = parseInt(hex[1].substring(2, 4), 16);
  var blue = parseInt(hex[1].substring(4, 6), 16);
  var background = blendWithWhite(red, green, blue, 0.9);
  var foreground = blendWithBlack(red, green, blue, 0.38);
  var secondary = blendWithBlack(red, green, blue, 0.22);

  element.style.setProperty('--event-accent', color);
  element.style.setProperty('--event-bg', background);
  element.style.setProperty('--event-text', foreground);
  element.style.setProperty('--event-meta', secondary);
}

function blendWithWhite(red, green, blue, strength) {
  return rgbToHex(
    Math.round(red * (1 - strength) + 255 * strength),
    Math.round(green * (1 - strength) + 255 * strength),
    Math.round(blue * (1 - strength) + 255 * strength)
  );
}

function blendWithBlack(red, green, blue, strength) {
  return rgbToHex(
    Math.round(red * (1 - strength)),
    Math.round(green * (1 - strength)),
    Math.round(blue * (1 - strength))
  );
}

function rgbToHex(red, green, blue) {
  return '#' + [red, green, blue].map(function (channel) {
    return channel.toString(16).padStart(2, '0');
  }).join('');
}

function eventDeletion(id) {
  if (!id || !window.confirm('Are you sure you want to delete this event?')) {
    return false;
  }

  var deleteButton = $('#delete_event_button').prop('disabled', true);
  $.ajax({
    url: 'services/deleteCalendarEvent.php',
    type: 'POST',
    data: { id: id },
    success: function (result) {
      if ($.trim(result) === 'success') {
        $('#event_entry_modal').modal('hide');
        display_events();
      } else {
        showCalendarFeedback('The event could not be deleted. Please try again.');
        deleteButton.prop('disabled', false);
      }
    },
    error: function (xhr, status, error) {
      console.error('Calendar event deletion failed:', status, error);
      showCalendarFeedback('The event could not be deleted. Please try again.');
      deleteButton.prop('disabled', false);
    }
  });

  return false;
}

function save_event() {
  var isDefault = $('#defaultEvent').is(':checked');
  var patientName = $.trim($('#patientName').val());
  var dentistName = $.trim($('#dentist').val());
  var contactNumber = $.trim($('#contactNumber').val());
  var eventTime = $('#appointmentTime').val();
  var eventTimeTo = $('#appointmentTimeto').val();
  var hmo = $.trim($('#hmo').val());
  var treatment = $.trim($('#treatment').val());
  var eventName = isDefault
    ? $('#event_name').val()
    : 'Patient: ' + patientName + '\nDentist: ' + dentistName +
      '\nContact: ' + contactNumber + '\nTime: ' + eventTime + '-' + eventTimeTo +
      '\nTreatment: ' + treatment + '\nHMO: ' + hmo;
  var startDate = $('#event_start_date').val();
  var endDate = $('#event_end_date').val() || startDate;
  var eventId = $('#event_id').val();
  var eventStatus = $("input[name='eventStatus']:checked").val();

  if (!eventName || !startDate || !endDate || (!isDefault && (!eventTime || !eventTimeTo))) {
    showCalendarFeedback('Please complete the required appointment details.');
    return false;
  }

  var saveButton = $('#event_entry_modal .modal-footer .btn-primary').prop('disabled', true);
  $.ajax({
    url: eventId ? 'update_event.php' : 'save_event.php',
    type: 'POST',
    dataType: 'json',
    data: {
      event_id: eventId,
      event_name: eventName,
      event_start_date: startDate,
      event_end_date: endDate,
      event_time: eventTime || '00:00',
      event_time_to: eventTimeTo || eventTime || '00:00',
      event_status: eventStatus,
      patientName: patientName,
      dentistName: dentistName,
      contactNumber: contactNumber,
      hmo: hmo,
      treatment: treatment
    },
    success: function (response) {
      if (response && response.status === true) {
        $('#event_entry_modal').modal('hide');
        display_events();
      } else {
        showCalendarFeedback(response && response.msg ? response.msg : 'The appointment could not be saved.');
      }
    },
    error: function (xhr, status, error) {
      console.error('Calendar event save failed:', status, error);
      showCalendarFeedback(xhr.responseJSON && xhr.responseJSON.msg
        ? xhr.responseJSON.msg
        : 'The appointment could not be saved. Please try again.');
    },
    complete: function () {
      saveButton.prop('disabled', false);
    }
  });

  return false;
}

function showCalendarFeedback(message) {
  if ($('#event_entry_modal').hasClass('show')) {
    $('#event-modal-feedback').text(message).show();
    return;
  }
  $('#calendar-feedback').text(message).stop(true, true).fadeIn(120);
}

function hideCalendarFeedback() {
  $('#calendar-feedback').hide().text('');
  $('#event-modal-feedback').hide().text('');
}
