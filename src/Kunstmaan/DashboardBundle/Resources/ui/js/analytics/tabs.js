$(function () {
    // show first tab
    var tab = $('.dashboard-tabs__item:nth-child(2) > a');
    switchTab(tab.attr('data-id'), tab.attr('data-path'));


    function switchTab(id, url) {
        $('#data_overview').addClass('dashboard__content--loading');
        $('.dashboard-tabs__item').removeClass('active');
        $('#tab' + id).addClass('active');

        $.ajax({
            type: 'get',
            url: url,
            cache: false,
            success: function (data) {
                if (!data.overview || data.overview.sessions == 0) {
                    $('#data_no_overview').css('display', 'block');
                    $('#data_overview > .dashboard__content').css('display', 'none');
                    $('#data_overview').removeClass('dashboard__content--loading');
                } else {
                    $('#data_no_overview').css('display', 'none');
                    $('#data_overview > .dashboard__content').css('display', 'block')
                    $('#data_overview').removeClass('dashboard__content--loading');

                    $("#dashboard-chart--audience").html('');
                    // render the chart

                    var step = 100;
                    if (data.overview.chartDataMaxValue < 100) {
                    step = 10;
                    }

                    var mainChart = new Morris.Area({
                        element: 'dashboard-chart--audience',
                        lineWidth: 1,
                        lineColors: [ '#8ac9e1', '#ce77b6', '#ee9c27', '#d13f37'],
                        fillOpacity: '.9',
                        hideHover: 'auto',
                        pointSize: 0,
                        data: data.overview.chartData,
                        xkey: 'timestamp',
                        ykeys: ['pageviews', 'sessions', 'users', 'newusers'],
                        labels: ['Pageviews', 'Sessions', 'Users', 'New Sessions'],
                        behaveLikeLine: true,
                        gridTextColor: '#a7a7a7',
                        smooth: true,
                        resize: true,
                        redraw: true,
                        ymax: Math.ceil(data.overview.chartDataMaxValue / step) * step
                    });
                    setMetrics(data);
                    setGoals(data);
                }
            }
        });
    }

    // Tab switcher
    $('.dashboard-tabs__item').on('click', function () {
        var id = $(this).find('.dashboard-tabs__controller').attr('data-id');
        var url = $(this).find('.dashboard-tabs__controller').attr('data-path');
        switchTab(id, url);
    });

    $("#segment-menu select").change(function() {
        var segmentId = $(this).find('option:selected').attr('data-segment-id');
        var configId = $(this).find('option:selected').attr('data-config-id');
        if (segmentId) {
            if (segmentId != '#') {
                location.href="?segment=" + segmentId + '&config=' + configId;
            } else {
                location.href="?config=" + configId;
            }
        }
    });

    $('.dashboard_update').click(function () {
        var url = $(this).attr('data-path');
        $('.dashboard_update').html('Updating...');
        $('.dashboard_update').attr('disabled', 'disabled');
        $.ajax({
            type: 'get',
            url: url,
            success: function (data) {
                location.reload();
            },
            error: function () {
                location.reload();
            }
        });
    });
});
