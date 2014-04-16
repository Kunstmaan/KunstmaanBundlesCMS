    $(document).ready(function() {
        // show first tab
        var tab = $('#tab3');
        switchTab(tab.attr('data-id'), tab.attr('data-path'))

        // Tab switcher
        $(".db-tabs__controller").click(function(){
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-path');
            switchTab(id, url);
        });

        function switchTab(id, url) {
            $.get(url, function(data) {
                if(data.responseCode==200) {
                    $('.db-tabs__item').removeClass('db-tabs__item--active');
                    $('#tab'+id).addClass('db-tabs__item--active');


                    $('.db-content').addClass('db-content--hidden');

                    // add functions here to add component behaviour
                    // these functions are declared in a per-template js file (public/js/analytics/)
                    setHeader(data);
                    setTraffic(data);
                    setReferrals(data);
                    setSearches(data);
                    setCampaigns(data);
                    setVisits(data);
                    setChart(data);
                    setGoals(data);
                    resetGoalChart();
                    initChart();

                    $('.db-content').removeClass('db-content--hidden');
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

