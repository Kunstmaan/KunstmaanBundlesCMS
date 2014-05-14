$(function () {
    var accountId = 0;
    var propertyId = 0;
    var profileId = 0;

    $('#submit_save').click(function() {
        // get the option data
        var url = $('#path_config_save').attr('data-url');
        $.ajax({
            type: 'get',
            url: url,
            data: {'profileId' : profileId, 'propertyId' : propertyId, 'accountId' : accountId},
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
                                location.reload();
                            }
                        });
                    }
                }
            }
        });
        return false;
    });

    /* =============================== ACCOUNTS =============================== */
        $("#accounts").chosen(
            {'search_contains' : true}
        );
        $("#properties").chosen(
            {'search_contains' : true}
        );
        $("#profiles").chosen(
            {'search_contains' : true}
        );

        // on account selected
        $("#accounts").change(function() {
            $("#accounts").attr('disabled', 'disabled');
            // hide the properties and profiles selects
            $('#properties + .properties_chzn').fadeOut();
            $('#profiles + .properties_chzn').fadeOut();
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
            $('#profiles').children().each(function (i) {
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
                        $('#properties').append(option);
                        $('#properties + .properties_chzn').fadeIn();
                        $('#properties').trigger('liszt:updated');
                        $('#profiles').trigger('liszt:updated');
                        $("#accounts").removeAttr('disabled');
                    });
                },
                error: function (data) {
                    $("#accounts").removeAttr('disabled');
                }
            });
        }

        // on property selected
        $("#properties").change(function() {
            $("#properties").attr('disabled', 'disabled');
            $("#accounts").attr('disabled', 'disabled');
            // hide the profiles select
            $('#profiles').addClass('setup-item__not_shown');
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
                    $.each(data, function(key, value) {
                        // add the options
                        var option = $('<option>', { 'data-id': data[key].profileId, text: data[key].profileName});
                        $('#profiles').append(option);
                        $('#profiles + .properties_chzn').fadeIn();
                        $('#profiles').trigger('liszt:updated');
                        $("#properties").removeAttr('disabled');
                        $("#accounts").removeAttr('disabled');
                    });
                },
                error: function (data) {
                    $("#properties").removeAttr('disabled');
                    $("#accounts").removeAttr('disabled');
                }
            });
        }

        // on profile selected
        $("#profiles").change(function() {
            $('#submit_save').removeAttr('disabled');

            $("#profiles option:selected").each(function() {
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

        function addSegmentInput() {
            // create an id
            var id = $.now();

            // create elements
            var segmentDiv = $('<div>', {'id' : 'segmentDiv'+id});
            var segmentInput = $('<input>', { 'type': 'text', 'id' : 'segment'+id, 'class' : 'segment-query', 'placeholder' : 'query'});
            var segmentName = $('<input>', { 'type': 'text', 'id' : 'segment_name'+id, 'class' : 'segment-name', 'placeholder' : 'name'});
            var segmentButton = $('<input>', {'type': 'button', 'data-segment-id' : 'segmentDiv'+id, 'class' : 'segment-button__delete btn__delete btn', 'value' : 'X', 'placeholder' : 'query'})

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
