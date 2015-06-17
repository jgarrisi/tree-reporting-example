jQuery(document).ready(function ($) {
	if (typeof viewType == 'undefined') {
		viewType = "agendaWeek";
	} else if (viewType == 'no view type') {
		viewType = "agendaWeek";
	}

	var filters = {
		shift: '',
		location: '',
		bench: ''
	};

	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		defaultDate: new Date(),
		defaultView: viewType,
		weekNumbers: "ISO",
		editable: false
	});

	//initial load
	getNewDataForDateRange();

	$('.fc-button').click(function (event) {
		getNewDataForDateRange();
	});

	function getNewDataForDateRange() {
		var view = $('#calendar').fullCalendar('getView');
		console.log(view.type + "-->" + view.title);
		console.log("going to AJAX for new data.........");
		//make post call to controller
		var token = $('input[name=_token]').val();
		$.ajax({
			url: 'schedule',
			type: "POST",
			data: {
				viewType: view.type,
				viewRange: view.title,
				_token: token
			},
			success: function (data) {
				var result = jQuery.parseJSON(data);
				console.log(result);
				//handle data and redraw schedule
				updateCalendar(result.events, filters);
			},
			error: function (message) {
				console.log(message);
			}
		});
	}


	$("#workWeekTextBox").change(function () {
		var workWeek = $("#workWeekTextBox").val();
		var year = $("#calendar").fullCalendar('getDate').year();
		var date = firstDayOfWeek(workWeek, year);
		$("#calendar").fullCalendar('gotoDate', date);
		getNewDataForDateRange();
	});


	$("#shiftSelect").change(function () {
		if ($("#shiftSelect").val() != 'Shift') {
			filters.shift = $("#shiftSelect").val();
		} else {
			filters.shift = '';
		}
		getNewDataForDateRange();
	});

	$("#locationSelect").change(function () {
		if ($("#locationSelect").val() != 'Location') {
			filters.location = $("#locationSelect").val();
		} else {
			filters.location = '';
		}
		getNewDataForDateRange();
	});

	$("#benchSelect").change(function () {
		if ($("#benchSelect").val() != 'Bench') {
			filters.bench = $("#benchSelect").val();
		} else {
			filters.bench = '';
		}
		getNewDataForDateRange();
	});
});

function updateCalendar(events_js, filters) {
	console.log("Curently set filters are: " + filters);

	//filter the events by the Shift
	var shifted = jQuery.grep(events_js, function (event) {
		return (event.shift.indexOf(filters.shift) > -1 || filters.shift == '');
	});
	//filter the events by the Location
	var locationed = jQuery.grep(shifted, function (event) {
		return (event.location.indexOf(filters.location) > -1 || filters.location == '');
	});
	//filter the events by the Bench
	var benched = jQuery.grep(locationed, function (event) {
		return (event.bench.indexOf(filters.bench) > -1 || filters.bench == '');
	});


	//reload the calendar
	$('#calendar').fullCalendar('removeEvents');
	$('#calendar').fullCalendar('addEventSource', benched);

}

function firstDayOfWeek(week, year) {
	if (year == null) {
		year = (new Date()).getFullYear();
	}

	var date = firstWeekOfYear(year),
		weekTime = weeksToMilliseconds(week),
		targetTime = date.getTime() + weekTime;

	return date.setTime(targetTime);

}

function weeksToMilliseconds(weeks) {
	return 1000 * 60 * 60 * 24 * 7 * (weeks - 1);
}

function firstWeekOfYear(year) {
	var date = new Date();
	date = firstDayOfYear(date, year);
	date = firstWeekday(date);
	return date;
}

function firstDayOfYear(date, year) {
	date.setYear(year);
	date.setDate(1);
	date.setMonth(0);
	date.setHours(0);
	date.setMinutes(0);
	date.setSeconds(0);
	date.setMilliseconds(0);
	return date;
}

/**
 * Sets the given date as the first day of week of the first week of year.
 */
function firstWeekday(firstOfJanuaryDate) {
	// 0 correspond au dimanche et 6 correspond au samedi.
	var FIRST_DAY_OF_WEEK = 1; // Monday, according to iso8601
	var WEEK_LENGTH = 7; // 7 days per week
	var day = firstOfJanuaryDate.getDay();
	day = (day === 0) ? 7 : day; // make the days monday-sunday equals to 1-7 instead of 0-6
	var dayOffset = -day + FIRST_DAY_OF_WEEK; // dayOffset will correct the date in order to get a Monday
	if (WEEK_LENGTH - day + 1 < 4) {
		// the current week has not the minimum 4 days required by iso 8601 => add one week
		dayOffset += WEEK_LENGTH;
	}
	return new Date(firstOfJanuaryDate.getTime() + dayOffset * 24 * 60 * 60 * 1000);
}
