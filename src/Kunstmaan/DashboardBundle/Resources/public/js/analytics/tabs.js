
    // function to remove the number format on data so charts can work with it
    function removeNumberFormat(number) {
        var parts = number.split(",");
        var number = '';
        for (var i = 0; i < parts.length; i++) {
            number += parts[i];
        }
        return parseInt(number);
    }

    $(document).ready(function() {
        // fade the dashboard in once everything is loaded
        // might need a progress thingy while loading?
        $('#dashboard').animate({opacity: '1'}, 500);

        // show first tab
        var tab = $('.db-tabs__item:nth-child(3) > div');
        console.log(tab);
        switchTab(tab.attr('data-id'), tab.attr('data-path'))

        // Tab switcher
        $(".db-tabs__item").click(function(){
            var id = $(this).find('.db-tabs__controller').attr('data-id');
            var url = $(this).find('.db-tabs__controller').attr('data-path');
            switchTab(id, url);
        });

        function switchTab(id, url) {
            $.get(url, function(data) {
                if(data.responseCode==200) {
                    $('.db-tabs__item').removeClass('db-tabs__item--active');
                    $('#tab'+id).addClass('db-tabs__item--active');

                    if (data.overview.visits == 0) {
                        $('#data_no_overview').css('display', 'block');
                        $('#data_overview').css('display', 'none');
                    } else {
                        $('#data_no_overview').css('display', 'none');
                        $('#data_overview').css('display', 'block');

                        // add functions here to add component behaviour
                        // these functions are declared in a per-template js file (public/js/analytics/)
                        setChart(data);
                        setGoals(data);
                    }

                }
            });
        }

        // unused, but please keep for a while, might need it again
        // var updateButtonText = $('#updateButton').html();
        // var updating = false;
        //
        // $('#updateButton').mouseenter(function() {
        //     if (!updating) {
        //         $('#updateButton').html($('#updateButton').attr('data-update-text'));
        //         $('#updateButton').attr('style', 'font-weight:bold;');
        //     }
        // }).mouseleave(function() {
        //     if (!updating) {
        //         $('#updateButton').html(updateButtonText);
        //         $('#updateButton').attr('style', 'font-weight:normal;');
        //     }
        // }).click(function() {
        //     if (!updating) {
        //         updating = true;
        //         $('#updateButton').html($('#updateButton').attr('data-updating-text'));
        //         $.get(
        //             "analytics/updateData",
        //             function(data) {
        //                 updating = false;
        //                 location.reload(true);
        //             }
        //         );
        //     }
        // });
    });

