$(function () {

    $('#properties').css('display', 'none');
    $('#profiles').css('display', 'none');
    // setAccountData();

    /* =============================== ACCOUNTS =============================== */

        $("#accounts").change(function() {
            $('#properties').fadeOut();
            $('#profiles').fadeOut();
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

        $("#properties").change(function() {
            $('#profiles').fadeOut();
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
                        $('#properties').fadeIn();
                    }
                }
            });
        }

    /* =============================== PROFILE =============================== */

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
                        $('#profiles').fadeIn();
                    }
                }
            });
        }

});
