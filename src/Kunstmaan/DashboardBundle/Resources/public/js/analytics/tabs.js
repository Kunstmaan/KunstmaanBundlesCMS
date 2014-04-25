    $(function() {
        // show first tab
        var tab = $('.dashboard-tabs__item:nth-child(2) > div');

        function switchTab(id, url) {
            $('#data_overview').addClass('dashboard__content--loading');
            $('.dashboard-tabs__item').removeClass('dashboard-tabs__item--active');
            $('#tab'+id).addClass('dashboard-tabs__item--active');

            $.ajax({
                type: 'get',
                url: url,
                cache: false,
                success: function(data) {
                    if (data.overview.sessions === 0) {
                        $('#data_no_overview').css('display', 'block');
                        $('#data_overview').css('display', 'none');
                    } else {
                        $('#data_no_overview').css('display', 'none');
                        $('#data_overview').css('display', 'block');
                        $('#data_overview').removeClass('dashboard__content--loading');

                        $("#dashboard-chart--audience").html('');
                        // render the chart

                        var step = 100;
                        if (data.overview.chartDataMaxValue < 100) {
                            var step = 10;
                        }

                        var mainChart = new Morris.Area({
                            element: 'dashboard-chart--audience',
                            lineWidth: 1,
                            lineColors: [ '#8ac9e1','#ce77b6', '#ee9c27', '#d13f37'],
                            fillOpacity: '.9',
                            hideHover: 'auto',
                            pointSize: 0,
                            data: data.overview.chartData,
                            xkey: 'timestamp',
                            ykeys: ['pageviews', 'sessions', 'users','newusers'],
                            labels: ['Pageviews', 'Sessions', 'Users', 'New Sessions'],
                            behaveLikeLine: true,
                            gridTextColor: '#a7a7a7',
                            smooth: true,
                            ymax: Math.ceil(data.overview.chartDataMaxValue/step)*step
                        });
                        setMetrics(data);
                        setGoals(data);
                    }
                }
            });
        }

        switchTab(tab.attr('data-id'), tab.attr('data-path'));

        // Tab switcher
        $('.dashboard-tabs__item').on('click', function(){
            var id = $(this).find('.dashboard-tabs__controller').attr('data-id');
            var url = $(this).find('.dashboard-tabs__controller').attr('data-path');
            switchTab(id, url);
        });
    });

