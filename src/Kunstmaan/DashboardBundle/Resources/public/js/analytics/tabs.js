
    function removeNumberFormat(number) {
        var parts = number.split(",");
        var number = '';
        for (var i = 0; i < parts.length; i++) {
            number += parts[i];
        }
        return parseInt(number);
    }


    $(document).ready(function() {
        $('#dashboard').animate({opacity: '1'}, 500);

        // show first tab
        var tab = $('#tab3');
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



                    // add functions here to add component behaviour
                    // these functions are declared in a per-template js file (public/js/analytics/)
                    setChart(data);
                    setVisits(data);
                    setTraffic(data);
                    setPages(data);
                    setGoals(data);
                    initChart();

                }
            });
        }

        var updateButtonText = $('#updateButton').html();
        var updating = false;

        $('#updateButton').mouseenter(function() {
            if (!updating) {
                $('#updateButton').html($('#updateButton').attr('data-update-text'));
                $('#updateButton').attr('style', 'font-weight:bold;');
            }
        }).mouseleave(function() {
            if (!updating) {
                $('#updateButton').html(updateButtonText);
                $('#updateButton').attr('style', 'font-weight:normal;');
            }
        }).click(function() {
            if (!updating) {
                updating = true;
                $('#updateButton').html($('#updateButton').attr('data-updating-text'));
                $.get(
                    "analytics/updateData",
                    function(data) {
                        updating = false;
                        location.reload(true);
                    }
                );
            }
        });
    });

