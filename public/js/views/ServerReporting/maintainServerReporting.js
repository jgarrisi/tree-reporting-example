/**
 * Created by garrisi on 4/1/2015.
 */

 var updateServerList = [];

 jQuery(document)
 .ready(function ($) {
 	$('.dateField')
 	.datepicker();

 	$('#dateSelectionDiv')
 	.hide();

 	$('#textEPRID')
 	.keyup(function (event) {
 		var EPR = $('#textEPRID')
 		.val();
 		console.log("text field changed EPR=" + EPR);
 		getEnvironmentsForEPR(EPR);
 	});

 	$('#loadBtn')
 	.click(function (event) {
 		$('#messagesDiv')
 		.empty();

 		updateServerList = [];
 		$('#updateServerList')
 		.text(updateServerList);

 		var EPR = $('#textEPRID')
 		.val();
 		var Env = $('#selEnv')
 		.val();
 		console.log("dropdown changed EPR=" + EPR + " and Environment=" + Env);
 		$('#serverTable')
 		.find('tbody')
 		.empty();
 		getServersforEPREnvironment(EPR, Env);

 	});

 	$('#saveButton')
 	.click(function (event) {
 		$('#messagesDiv')
 		.empty();

 		var EPR = $('#textEPRID')
 		.val();
 		var env = $('#selEnv')
 		.val();

 		console.log("save button clicked");

 		updateServersforEPREnvironment(EPR, env);


 	});

 	$('tbody')
 	.on('changeDate', '.dateField', function () {
 		console.log(this);
 		var serverName = this.id.split('-')[1];
 		cellChanged(serverName);
 	});

 	$('tbody')
 	.on('change', '.dateField', function () {
 		console.log(this);
 		var serverName = this.id.split('-')[1];
 		cellChanged(serverName);
 	});

 	$('tbody')
 	.on('change', '.statusDropdown', function () {
 		console.log(this);
 		var serverName = this.id.split('-')[1];
 		cellChanged(serverName);
 	});

 });

/**
 * when a cell in a row is changed, save the serverName to the list, and color the text blue so the user knows that row will be updated when the save button is clicked
 * @param serverName
 */
 function cellChanged(serverName) {
 	console.log("cell changed for -" + serverName + '-');
 	updateServerList.push(serverName);
	//$('#updateServerList').text(updateServerList);
	$("#" + serverName.replace(/\./g, ''))
	.css('color', 'blue');
}


function getEnvironmentsForEPR(EPR) {
	var token = $('input[name=_token]')
	.val();
	$.ajax({
		url: 'maintainServerReporting/getEnvironments',
		type: "GET",
		data: {
			textEPRID: EPR,
			_token: token
		},
		dataType: 'json',
		success: function (data) {
			console.log("AJAX success...");
			console.log(data);
			$('#selEnv').empty();
			$.each(data, function (index, value) {
				console.log(index + " : " + value);
				$('#selEnv')
				.append($('<option></option>')
					.val(value)
					.html(value));
			});


		},
		error: function (message) {
			var errorMessage = '<div class="alert alert-danger">AJAX call failed: ' + message.responseText + '</div>';
			$('#messagesDiv')
			.append(errorMessage);
			console.log(message);
		}
	});
}


function getServersforEPREnvironment(EPR, Env) {
	var token = $('input[name=_token]')
	.val();
	$.ajax({
		url: 'maintainServerReporting/getServers',
		type: "GET",
		data: {
			textEPRID: EPR,
			selEnv: Env,
			_token: token
		},
		dataType: 'json',
		success: function (serverList) {
			console.log("AJAX success...");
			console.log(serverList);

			$.each(serverList, function (index, value) {
				//console.log(index+" : "+value);
				$("#serverTable")
				.find('tbody')
				.append(
					$('<tr>')
					.attr('id', value.ServerName.replace(/\./g, ''))
					.append(
						$('<td>')
						.text(value.ServerName)
						.attr('id', "servername-" + value.ServerName.replace(/\./g, ''))
						)
					.append(
						$('<td>')
						.html($('<input type="text" class="dateField" data-date-format="yyyy-mm-dd">')
							.attr('id', "planDate-" + value.ServerName.replace(/\./g, ''))
							.val(value.RIPPlanDate)
							.datepicker())
						)
					.append(
						$('<td>')
						.html($('<input type="text" class="dateField" data-date-format="yyyy-mm-dd">')
							.attr('id', "actualDate-" + value.ServerName.replace(/\./g, ''))
							.val(value.RIPActualDate)
							.datepicker())
						)
					.append(
						$('<td>')
						.html($('<input type="text" class="dateField" data-date-format="yyyy-mm-dd">')
							.attr('id', "excludeDate-" + value.ServerName.replace(/\./g, ''))
							.val(value.RIPExcludeDate)
							.datepicker())
						)
					.append(
							//$('<td>').text(value.Status).attr('id', "status-" + value.ServerName.replace(/\./g,''))
							makeStatusDropdown(value.ServerName.replace(/\./g, ''), value.Status)
							)
					);
});

var successMessage = '<div class="alert alert-success">Found ' + serverList.length + ' Servers for EPR: ' + EPR +
' | Environment: ' + Env + ' </div>';
$('#messagesDiv')
.append(successMessage);

$('#dateSelectionDiv')
.show();
},
error: function (message) {
	var errorMessage = '<div class="alert alert-danger">AJAX call failed: ' + message.responseText + '</div>';
	$('#messagesDiv')
	.append(errorMessage);
	console.log(message);
}
});
}

function updateServersforEPREnvironment(EPR, env) {
	var subsetOfTable = [];
	//console.log(updateServerList);
	updateServerList.forEach(function (x) {
		//console.log("#servername-"+x);
		//example id == servername-c1t02165itcshpcom

		var row = {
			'ServerName': $("#servername-" + x)
			.text(),
			'RIPPlanDate': $("#planDate-" + x)
			.val(),
			'RIPActualDate': $("#actualDate-" + x)
			.val(),
			'RIPExcludeDate': $("#excludeDate-" + x)
			.val(),
			'Status': $("#status-" + x)
			.val()
		};
		//console.log(row);
		subsetOfTable.push(row);
	});

	console.log(subsetOfTable);

	var token = $('input[name=_token]')
	.val();
	$.ajax({
		url: 'maintainServerReporting/saveServerSet',
		type: "POST",
		data: {
			rowsToUpdate: subsetOfTable,
			_token: token
		},
		dataType: 'json',
		success: function (feedback) {
			console.log("AJAX success...");


			var successMessage = '<div class="alert alert-success">Saved Dates for ' + feedback + ' server(s). </div>';
			$('#messagesDiv')
			.append(successMessage);

			$('#dateSelectionDiv')
			.show();
		},
		error: function (message) {
			var errorMessage = '<div class="alert alert-danger">AJAX call failed: ' + message.responseText + '</div>';
			$('#messagesDiv')
			.append(errorMessage);
			console.log(message);
		}
	})
	.done(function () {
		$('#serverTable')
		.find('tbody')
		.empty();
		getServersforEPREnvironment(EPR, env);
		updateServerList = [];
				//$('#updateServerList').text(updateServerList);

			}

			);
}

function getServerSetFromTbody() {
	$("#serverTable")
	.find('tbody');

	var columns = ['ServerName', 'RIPPlanDate', 'RIPActualDate', 'RIPExcludeDate', 'status'];

	var tableObject = $('#serverTable tbody tr')
	.map(function (i) {
		var row = {};

			// Find all of the table cells on this row.
			$(this)
			.find('td')
			.each(function (i) {
					// Determine the cell's column name by comparing its index
					//  within the row with the columns list we built previously.
					var colName = columns[i];

					// Add a new property to the row object, using this cell's
					//  column name as the key and the cell's text as the value.
					row[colName] = $(this)
					.text();
				});

			// Finally, return the row's object representation, to be included
			//  in the array that $.map() ultimately returns.
			return row;

			// Don't forget .get() to convert the jQuery set to a regular array.
		})
	.get();

	return tableObject;
}

function makeStatusDropdown(fieldId, status) {
	//dropdown options
	var options = {
		'Not Scheduled':'Not Scheduled',
		'Execution Complete':'Complete',
		'Scheduled':'Scheduled',
		'Out of Scope':'Out of Scope'
	};

	var select = $('<select />')
	.addClass('dropdown statusDropdown')
	.attr('id', 'status-' + fieldId);
	select.append(
		'<button class="btn btn-default dropdown-toggle" type="button" id="caret-fieldId" data-toggle="dropdown" aria-expanded="true" />'
		)
	.append('<span class="caret" />');
	$.each(options, function(index, value) {
		$('<option />', {value: value, text: index}).appendTo(select);
	});

	select.val(status);
	return $('<td/>')
	.append(select);
}
