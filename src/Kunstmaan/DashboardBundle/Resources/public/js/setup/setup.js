$(function () {

    // $('#properties').css('display', 'none');
    // $('#profiles').css('display', 'none');

    /* =============================== ACCOUNTS =============================== */

        $("#accounts").change(function() {
            $('#properties').removeClass('setup-item__shown');
            $('#profiles').removeClass('setup-item__shown');
            $("#accounts option:selected").each(function() {
                var id = $(this).attr('data-id');
                var url = $('#path_account_save').attr('data-url');
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {'id' : id},
                    success: function (data) {
                        setPropertyData();
                    }
                });
            });
        });

    /* =============================== PROPERTIES =============================== */

        function setPropertyData() {
            var url = $('#path_properties').attr('data-url');

            $('#properties').children().each(function (i) {
                if (i != 0) {
                    $(this).remove();
                }
            })

            $.ajax({
                type: 'get',
                url: url,
                success: function (data) {
                    for(var i = 0; i < data.length; i++) {
                        var option = $('<option>', { 'data-id': data[i].propertyId, text: data[i].propertyName});
                        $('#properties').append(option);
                        $('#properties').addClass('setup-item__shown');
                    }
                }
            });
        }

        $("#properties").change(function() {
            $('#profiles').removeClass('setup-item__shown');
            $("#properties option:selected").each(function() {
                var id = $(this).attr('data-id');
                var url = $('#path_property_save').attr('data-url');
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {'id' : id},
                    success: function (data) {
                        setProfileData();
                    }
                });
            });
        });

    /* =============================== PROFILE =============================== */

        function setProfileData() {
            var url = $('#path_profiles').attr('data-url');

            $('#profiles').children().each(function (i) {
                if (i != 0) {
                    $(this).remove();
                }
            })

            $.ajax({
                type: 'get',
                url: url,
                success: function (data) {
                    for(var i = 0; i < data.length; i++) {
                        var option = $('<option>', { 'data-id': data[i].profileId, text: data[i].profileName});
                        $('#profiles').append(option);
                        $('#profiles').addClass('setup-item__shown');
                    }
                }
            });
        }

        $("#profiles").change(function() {
            $("#profiles option:selected").each(function() {
                var id = $(this).attr('data-id');
                var url = $('#path_profile_save').attr('data-url');
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {'id' : id},
                    success: function (data) {
                        $('#submit_save').removeAttr('disabled');
                    }
                });
            });
        });

    /* =============================== SEGMENTS =============================== */

        $("#use-segments").change(function() {
            if(!$(this).is(':checked')) {
                $('#segements').fadeOut();
                $('#segement-button__add').fadeOut();
                $('#submit_segement_save').fadeOut();
                return;
            }

            $('#segements').fadeIn();
            $('#segement-button__add').fadeIn();
            $('#submit_segement_save').fadeIn();

            if($('#segements').children().length == 0) {
                addSegmentInput();
            }
        });

        $('#segement-button__add').click(function() {
            addSegmentInput();
            return false;
        });

        function addSegmentInput() {
            var id = $.now();

            var segmentDiv = $('<div>', {'id' : 'segmentDiv'+id});
            var segmentLabel = $('<label>', {'for' : 'segement'+id, 'text' : 'Add a segment query'});
            var segmentInput = $('<input>', { 'type': 'text', 'id' : 'segement'+id, 'class' : 'segment-query'});
            var segmentButton = $('<input>', {'type': 'button', 'data-segment-id' : 'segmentDiv'+id, 'class' : 'segment-button__delete btn__delete btn', 'value' : 'X'})

            segmentButton.click(function() {
                var segementId = $(this).attr('data-segment-id');
                $('#'+segementId).remove();
                return false;
            });

            segmentDiv.append(segmentLabel);
            segmentDiv.append(segmentInput);
            segmentDiv.append(segmentButton);

            $('#segements').append(segmentDiv);
        }

        $('#submit_segement_save').click(function(){
            var segments = $('#segements').children();
            for (var i = 0; i < segments.length; i++) {
                var query = $(segments[i]).find('input.segment-query').val();
                if (query && query != '') {
                    var url = $('#path_segment_add').attr('data-url');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {'query' : query},
                        success: function (data) {
                            location.reload();
                        }
                    });
                }
            }

            return false;
        });

        $('.segment-list-button__delete').click(function() {
            var id = $(this).attr('data-segment-id');
            var url = $('#path_segment_delete').attr('data-url');
            $.ajax({
                type: 'get',
                url: url,
                data: {'id' : id},
                success: function (data) {
                    $('#segment-list__'+id).fadeOut();
                }
            });
        })
});
