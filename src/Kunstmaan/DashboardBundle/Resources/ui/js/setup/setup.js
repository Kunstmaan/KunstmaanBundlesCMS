$(function () {

    var accountId = $('#account_id').attr('data-id');
    var propertyId = $('#property_id').attr('data-id');
    var profileId = $('#profile_id').attr('data-id');
    var configId = $('#config_id').attr('data-id');


    $('#submit_save').click(function() {
        var disableGoals = 0;

        if ($('#disableGoals').is(":checked")) {
            disableGoals = 1;
        }

        // get the option data
        var url = $('#path_config_save').attr('data-url');
        $.ajax({
            type: 'get',
            url: url,
            data: {'profileId' : profileId, 'propertyId' : propertyId, 'accountId' : accountId, 'disableGoals' : disableGoals, 'configId' : configId},
            success: function (data) {
                // get all new segments
                var segments = $('#segments-new').children();

                if (!segments.length) {
                    location.reload();
                }

                for (var i = 0; i < segments.length; i++) {
                    // get input field values for each segment
                    var query = $(segments[i]).find('input.segment-query').val();
                    var name = $(segments[i]).find('input.segment-name').val();

                    // if query isset
                    if (name && name != '' && query && query != '') {
                        // save the segment
                        var url = $('#path_segment_add').attr('data-url');
                        $.ajax({
                            type: 'get',
                            url: url,
                            data: {'query' : query, 'name' : name, 'configId' : configId},
                            success: function (data) {
                                location.reload();
                            }
                        });
                    }
                }
            }
        });

        return false;
    });

    $('#disableGoals').change(function() {
    $('#submit_save').removeAttr('disabled');
});

function triggerUpdate() {
    $('#accounts select').trigger('chosen:updated');
    $('#properties select').trigger('chosen:updated');
    $('#profiles select').trigger('chosen:updated');
    $('#accounts select').trigger('liszt:updated');
    $('#properties select').trigger('liszt:updated');
    $('#profiles select').trigger('liszt:updated');
}

/* =============================== ACCOUNTS =============================== */
//$("#accounts select").chosen(
//    {'search_contains' : true}
//);
//$("#properties select").chosen(
//    {'search_contains' : true}
//);
//$("#profiles select").chosen(
//    {'search_contains' : true}
//);

// on account selected
$("#accounts select").change(function() {
    $("#accounts select").attr('disabled', 'disabled');

    triggerUpdate();

    // hide the properties and profiles selects
    $('#properties').fadeOut();
    $('#profiles').fadeOut();
    $("#accounts select option:selected").each(function() {
        // get the account id of the selected account
        accountId = $(this).attr('data-id');
        setPropertyData(accountId);
    });
});

/* =============================== PROPERTIES =============================== */

// show the property select
function setPropertyData(accountId) {
    var url = $('#path_properties').attr('data-url');

    // clear all the options
    $('#properties select').children().each(function (i) {
        if (i != 0) {
            $(this).remove();
        }
    })
    $('#profiles select').children().each(function (i) {
        if (i != 0) {
            $(this).remove();
        }
    })

    // get the option data
    $.ajax({
        type: 'get',
        url: url,
        data: {'accountId' : accountId},
        success: function (data) {
            $.each(data, function(key, value) {
                var option = $('<option>', { 'data-id': data[key].propertyId, text: data[key].propertyName});
                $('#properties select').append(option);
                $('#properties').fadeIn();
                $("#accounts select").removeAttr('disabled');
                triggerUpdate();
            });
        },
        error: function (data) {
            $("#accounts select").removeAttr('disabled');
            triggerUpdate();
        }
    });
}

// on property selected
$("#properties select").change(function() {
    $('#profiles').fadeOut();
    $("#properties select").attr('disabled', 'disabled');
    $("#accounts select").attr('disabled', 'disabled');
    triggerUpdate();
    // hide the profiles select
    $('#profiles select').addClass('setup-item__not_shown');
    $("#properties select option:selected").each(function() {
        // get the property id of the selected property
        propertyId = $(this).attr('data-id');
        setProfileData(propertyId);
    });
});

/* =============================== PROFILE =============================== */

// show the profile select
function setProfileData(propertyId) {
    var url = $('#path_profiles').attr('data-url');

    // clear all the options
    $('#profiles select').children().each(function (i) {
        if (i != 0) {
            $(this).remove();
        }
    })

    // get the option data
    $.ajax({
        type: 'get',
        url: url,
        data: {'propertyId' : propertyId, 'accountId' : accountId},
        success: function (data) {
            $.each(data, function(key, value) {
                // add the options
                var option = $('<option>', { 'data-id': data[key].profileId, text: data[key].profileName});
                $('#profiles select').append(option);
                $('#profiles').fadeIn();
                $("#properties select").removeAttr('disabled');
                $("#accounts select").removeAttr('disabled');
                triggerUpdate();
            });
        },
        error: function (data) {
            $("#properties select").removeAttr('disabled');
            $("#accounts select").removeAttr('disabled');
            triggerUpdate();
        }
    });
}

// on profile selected
$("#profiles select").change(function() {
    $('#submit_save').removeAttr('disabled');

    $("#profiles select option:selected").each(function() {
        // get the profile id of the selected profile
        profileId = $(this).attr('data-id');
        $('#submit_save').removeAttr('disabled');
    });
});

/* =============================== SEGMENTS =============================== */

// on segment add button click
$('#segment-button__add').click(function() {
    $('#submit_save').removeAttr('disabled');
    addSegmentInput();
    return false;
});

function addSegmentInput(name, query) {
    if (!name) { name = ''; }
    if (!query) { query = ''; }
    // create an id
    var id = $.now();

    // create elements
    var segmentDiv = $('<div>', {'id' : 'segmentDiv'+id, 'class': 'well add-segment__wrapper clearfix'});
    var segmentInput = $('<input>', { 'type': 'text', 'id' : 'segment'+id, 'class' : 'segment-query', 'placeholder': 'query', 'value' : query});
    var segmentName = $('<input>', { 'type': 'text', 'id' : 'segment_name'+id, 'class' : 'segment-name', 'placeholder' : 'name', 'value' : name});
    var segmentButton = $('<input>', {'type': 'button', 'data-segment-id' : 'segmentDiv'+id, 'class' : 'btn btn-danger pull-right', 'value' : 'cancel'})

    // add event trigger for the delete button
    segmentButton.click(function() {
        var segmentId = $(this).attr('data-segment-id');
        $('#'+segmentId).remove();
        return false;
    });

    // append elements
    segmentDiv.append(segmentName);
    segmentDiv.append(segmentInput);
    segmentDiv.append(segmentButton);

    $('#segments-new').append(segmentDiv);
}

// on existing segment delete button click
$('.segment-list-button__delete').click(function() {
    // get segment id
    var id = $(this).attr('data-segment-id');

    // delete the segment in the database
    var url = $('#path_segment_delete').attr('data-url');
    $.ajax({
        type: 'get',
        url: url,
        data: {'id' : id},
        success: function (data) {
            // fadeout the segment box
            $('#segment-list__'+id).fadeOut();
        }
    });
    return false;
})

$('.segment-list-button__edit').click(function() {
    // get segment id
    var id = $(this).attr('data-segment-id');

    $('#segment-list__'+id+' .edit-view').css('display', 'block');
    $('#segment-list__'+id+' .display-view').css('display', 'none');
    return false;
});

$('.segment-list-button__confirm').click(function() {
    // get segment id
    var id = $(this).attr('data-segment-id');

    var query = $('#segment-list__'+id).find('input.segment-query').val();
    var name = $('#segment-list__'+id).find('input.segment-name').val();

    if (query && name) {
        var url = $('#path_segment_edit').attr('data-url');
        $.ajax({
            type: 'get',
            url: url,
            data: {'id' : id, 'name' : name, 'query' : query},
            success: function (data) {
                $('#segment-list__'+id+' .edit-view').css('display', 'none');
                $('#segment-list__'+id+' .display-view').css('display', 'block');
                $('#segment-list__'+id+' .display-segment-query').html(query)
                $('#segment-list__'+id+' .display-segment-name').html(name);
            }
        });
    } else {
        $('#segment-list__'+id+' .edit-view').css('display', 'none');
        $('#segment-list__'+id+' .display-view').css('display', 'block');
    }
    return false;
});
});
