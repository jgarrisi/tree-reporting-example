/**
 * Created by garrisi on 4/1/2015.
 */

var updateEPRStatusList = [];

jQuery(document).ready(function ($) {
    $('.dateField').datepicker();

    $('#dateSelectionDiv').hide();

    $('#textEPRID').keyup(function (event) {
        var EPR = $('#textEPRID').val();
        console.log("text field changed EPR=" + EPR);
        getEnvironmentsForEPR(EPR);
    });

    $('#loadBtn').click(function (event) {
        $('#messagesDiv').empty();

        updateEPRList = [];
        $('#updateEPRStatusList').text(updateEPRStatusList);

        var EPR = $('#textEPRID').val();
        var Env = $('#selEnv').val();
        console.log("dropdown changed EPR=" + EPR + " and Environment=" + Env);
        $('#EPRStatusTable').find('tbody').empty();
        getEPRStatusesforEPREnvironment(EPR, Env);

    });

    $('#saveButton').click(function (event) {
        $('#messagesDiv').empty();

        var EPR = $('#textEPRID').val();
        var env = $('#selEnv').val();

        console.log("save button clicked");

        updateEPRStatusforEPREnvironment(EPR, env);


    });

    $('tbody').on('changeDate', '.dateField', function () {
        console.log(this);
        var key = this.id.split('-')[1]+'-'+this.id.split('-')[2];
        cellChanged(key);
    });

    $('tbody').on('change', '.dateField', function () {
        console.log(this);
        var key = this.id.split('-')[1]+'-'+this.id.split('-')[2];
        cellChanged(key);
    });

    $('tbody').on('change', '.statusDropdown', function () {
        console.log(this);
        var key = this.id.split('-')[1]+'-'+this.id.split('-')[2];
        cellChanged(key);
    });
    $('tbody').on('change', '.checkbox', function () {
        console.log(this);
        var key = this.id.split('-')[1]+'-'+this.id.split('-')[2];
        cellChanged(key);
    });

});

/**
 * when a cell in a row is changed, save the serverName to the list, and color the text blue so the user knows that row will be updated when the save button is clicked
 * @param serverName
 */
function cellChanged(key) {
    console.log("cell changed for -" + key + '-');

    if($.inArray(key, updateEPRStatusList)==-1){
        updateEPRStatusList.push(key);
    }

    $('#updateEPRStatusList').text(updateEPRStatusList);
    $("#" + key).css('color', 'blue');
}


function getEnvironmentsForEPR(EPR) {
    var token = $('input[name=_token]').val();
    $.ajax({
        url: 'maintainServerReporting/getEnvironments',
        type: "GET",
        data: {textEPRID: EPR, _token: token},
        dataType: 'json',
        success: function (data) {
            console.log("AJAX success...");
            console.log(data);
            $('#selEnv').empty()
            $.each(data, function (index, value) {
                console.log(index + " : " + value);
                $('#selEnv').append($('<option></option>').val(value).html(value));
            });


        },
        error: function (message) {
            var errorMessage = '<div class="alert alert-danger">AJAX call failed: ' + message.responseText + '</div>';
            $('#messagesDiv').append(errorMessage);
            console.log(message);
        }
    });
}


function getEPRStatusesforEPREnvironment(EPR, Env) {
    var token = $('input[name=_token]').val();
    $.ajax({
        url: 'maintainEPRStatusReporting/getEPRStatuses',
        type: "GET",
        data: {textEPRID: EPR, selEnv: Env, _token: token},
        dataType: 'json',
        success: function (EPRStatusList) {
            console.log("AJAX success...");
            console.log(EPRStatusList);

            $.each(EPRStatusList, function (index, value) {
                //console.log(index+" : "+value);
                var key = value.EPRID + '-' + value.Environment;
                $("#EPRStatusTable").find('tbody').append(
                    $('<tr>').attr('id', key).append(
                        $('<td>').text(value.EPRID).attr('id', "EPRID-" + key)
                        ).append(
                             $('<td>').text(value.Company).attr('id', "company-" + key)
                        ).append(
                             $('<td>').text(value.Environment).attr('id', "environment-" + key)
                        ).append(
                             //$('<td>').text(value.Status).attr('id', "status-" + value.ServerName.replace(/\./g,''))
                             makeStatusDropdown(key, value.Status)
                        ).append(
                            $('<td>').html($('<input type="text" class="dateField" data-date-format="yyyy-mm-dd">')
                                .attr('id', "targetDate-" + key)
                                .val(value.TargetDate)
                                .datepicker())
                        ).append(
                            $('<td>').html($('<input type="text" class="dateField" data-date-format="yyyy-mm-dd">')
                                .attr('id', "actualDate-" + key)
                                .val(value.ActualDate)
                                .datepicker())
                        ).append(
                            $('<td>').html($('<input />', { type: 'checkbox', id: 'planningComplete-'+key}).addClass('checkbox').prop('checked',function(){return value.PlanningComplete==1?true:false;}))
                        ).append(
                            $('<td>').html($('<input />', { type: 'checkbox', id: 'designComplete-'+key}).addClass('checkbox').prop('checked',function(){return value.DesignComplete==1?true:false;}))
                        )

                    );
            });

            var successMessage = '<div class="alert alert-success">Found ' + EPRStatusList.length + ' row(s) for EPR: ' + EPR + ' | Environment: ' + Env + ' </div>';
            $('#messagesDiv').append(successMessage);

            $('#dateSelectionDiv').show();
        },
        error: function (message) {
            var errorMessage = '<div class="alert alert-danger">AJAX call failed: ' + message.responseText + '</div>';
            $('#messagesDiv').append(errorMessage);
            console.log(message);
        }
    });
}

function updateEPRStatusforEPREnvironment(EPR, env) {
    var subsetOfTable = [];
    updateEPRStatusList.forEach(function (x) {
        var row = {
            'EPRID': $("#EPRID-" + x).text(),
            'Company': $("#company-" + x).text(),
            'Environment': $("#environment-" + x).text(),
            'Status': $("#status-" + x).val(),
            'TargetDate': $("#targetDate-" + x).val(),
            'ActualDate': $("#actualDate-" + x).val(),
            'PlanningComplete': $("#planningComplete-" + x).prop( "checked" ),
            'DesignComplete': $("#designComplete-" + x).prop( "checked" )
        };
        console.log(row);
        subsetOfTable.push(row);
    });

    console.log(subsetOfTable);

    var token = $('input[name=_token]').val();
    $.ajax({
        url: 'maintainEPRStatusReporting/saveEPRStatusSet',
        type: "POST",
        data: {rowsToUpdate: subsetOfTable, _token: token},
        dataType: 'json',
        success: function (feedback) {
            console.log("AJAX success...");


            var successMessage = '<div class="alert alert-success">Saved Dates for ' + feedback + ' server(s). </div>';
            $('#messagesDiv').append(successMessage);

            $('#dateSelectionDiv').show();
        },
        error: function (message) {
            var errorMessage = '<div class="alert alert-danger">AJAX call failed: ' + message.responseText + '</div>';
            $('#messagesDiv').append(errorMessage);
            console.log(message);
        }
    }).done(function () {
            $('#EPRStatusTable').find('tbody').empty();
            getEPRStatusesforEPREnvironment(EPR, env);
            updateEPRStatusList = [];
            //$('#updateEPRStatusList').text(updateEPRStatusList);

        }
    );
}


function makeStatusDropdown(fieldId, status) {
    //dropdown options
    var options = {
        'Scheduled'                         :   'Scheduled',
        'Biz Engage Complete'               :   'Biz Engage Complete',
        'Design Complete'                   :   'Design Complete',
        'Warranty'                          :   'Warranty',
        'Execution Complete'                :   'Complete',
        'Execution Complete - Partial'      :   'Complete - Partial',
        'Out of Scope'                      :   'Out of Scope'
    };

    var select = $('<select />').addClass('dropdown statusDropdown').attr('id', 'status-' + fieldId);
    select.append('<button class="btn btn-default dropdown-toggle" type="button" id="caret-fieldId" data-toggle="dropdown" aria-expanded="true" />').append('<span class="caret" />');

    $.each(options, function(index, value) {
        $('<option />', {value: value, text: index}).appendTo(select);
    });


    select.val(status);
	return $('<td/>')
		.append(select);

}
