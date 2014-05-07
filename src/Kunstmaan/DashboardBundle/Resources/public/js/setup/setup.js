$(function () {

    // $('#properties').css('display', 'none');
    // $('#profiles').css('display', 'none');

    var accountId = 0;
    var propertyId = 0;
    var profileId = 0;

    /* =============================== ACCOUNTS =============================== */

        // on account selected
        $("#accounts").change(function() {
            // hide the properties and profiles selects
            $('#properties').removeClass('setup-item__shown');
            $('#profiles').removeClass('setup-item__shown');
            $("#accounts option:selected").each(function() {
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
            $('#properties').children().each(function (i) {
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
                    for(var i = 0; i < data.length; i++) {
                        // add the options
                        var option = $('<option>', { 'data-id': data[i].propertyId, text: data[i].propertyName});
                        $('#properties').append(option);
                        $('#properties').addClass('setup-item__shown');
                    }
                }
            });
        }

        // on property selected
        $("#properties").change(function() {
            // hide the profiles select
            $('#profiles').removeClass('setup-item__shown');
            $("#properties option:selected").each(function() {
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
            $('#profiles').children().each(function (i) {
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
                    for(var i = 0; i < data.length; i++) {
                        // add the options
                        var option = $('<option>', { 'data-id': data[i].profileId, text: data[i].profileName});
                        $('#profiles').append(option);
                        $('#profiles').addClass('setup-item__shown');
                    }
                }
            });
        }

        // on profile selected
        $("#profiles").change(function() {
            $("#profiles option:selected").each(function() {
                // get the profile id of the selected profile
                profileId = $(this).attr('data-id');
                $('#submit_save').removeAttr('disabled');
            });
        });


        $('#submit_save').click(function() {
            var url = $('#path_config_save').attr('data-url');

            // get the option data
            $.ajax({
                type: 'get',
                url: url,
                data: {'profileId' : profileId, 'propertyId' : propertyId, 'accountId' : accountId},
                success: function (data) {
                    location.reload();
                }
            });

            return false;
        });

    /* =============================== SEGMENTS =============================== */

        // on checkbox use-segments checked
        $("#use-segments").change(function() {
            // if unchecked: hide all new segments
            if(!$(this).is(':checked')) {
                $('#segements').fadeOut();
                $('#segement-button__add').fadeOut();
                $('#submit_segement_save').fadeOut();
                return;
            }

            // show new segments
            $('#segements').fadeIn();
            $('#segement-button__add').fadeIn();
            $('#submit_segement_save').fadeIn();

            // if first segment: automatically add a first input field
            if($('#segements').children().length == 0) {
                addSegmentInput();
            }
        });

        // on segment add button click
        $('#segement-button__add').click(function() {
            addSegmentInput();
            return false;
        });

        function addSegmentInput() {
            // create an id
            var id = $.now();

            // create elements
            var segmentDiv = $('<div>', {'id' : 'segmentDiv'+id});
            var segmentLabel = $('<label>', {'for' : 'segement'+id, 'text' : 'Add a segment query'});
            var segmentInput = $('<input>', { 'type': 'text', 'id' : 'segement'+id, 'class' : 'segment-query'});
            var segmentName = $('<input>', { 'type': 'text', 'id' : 'segement_name'+id, 'class' : 'segment-name', 'placeholder' : 'name'});
            var segmentButton = $('<input>', {'type': 'button', 'data-segment-id' : 'segmentDiv'+id, 'class' : 'segment-button__delete btn__delete btn', 'value' : 'X', 'placeholder' : 'query'})

            // add event trigger for the delete button
            segmentButton.click(function() {
                var segementId = $(this).attr('data-segment-id');
                $('#'+segementId).remove();
                return false;
            });

            // append elements
            segmentDiv.append(segmentLabel);
            segmentDiv.append(segmentName);
            segmentDiv.append(segmentInput);
            segmentDiv.append(segmentButton);
            $('#segements').append(segmentDiv);
        }

        // on segment save button click
        $('#submit_segement_save').click(function(){
            // get all new segments
            var segments = $('#segements').children();
            for (var i = 0; i < segments.length; i++) {
                // get input field values for each segment
                var query = $(segments[i]).find('input.segment-query').val();
                var name = $(segments[i]).find('input.segment-name').val();

                // TODO add more checks!
                // if query isset
                if (name && name != '' && query && query != '') {
                    // save the segment
                    var url = $('#path_segment_add').attr('data-url');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {'query' : query, 'name' : name},
                        success: function (data) {
                            // reload page when done
                            location.reload();
                        }
                    });
                }
            }

            return false;
        });

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
        })
});
