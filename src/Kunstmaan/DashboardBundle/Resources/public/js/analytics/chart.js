    // sets the chart data
    function setChart(data) {
        // clear the existing chart
        $('#dashboard-chart--audience').html('');

        // render the chart
        new Morris.Area({
            element: 'dashboard-chart--audience',
            lineWidth: 1,
            lineColors: [ '#8ac9e1','#ce77b6', '#ee9c27', '#d13f37'],
            fillOpacity: '.9',
            hideHover: 'auto',
            pointSize: 0,
            data: data.overview.chartData,
            ymax: data.overview.chartDataMaxValue,
            xkey: 'timestamp',
            ykeys: ['pageviews', 'sessions', 'users','newusers'],
            labels: ['Pageviews', 'Sessions', 'Users', 'New Users'],
            behaveLikeLine: true,
            gridTextColor: '#a7a7a7'
        });

        // set the values
        $('#audience-data_visits').html(data.overview.sessions);
        $('#audience-data_visitors').html(data.overview.users);
        $('#audience-data_pageviews').html(data.overview.pageViews);
        $('#audience-data_pages_per_visit').html(data.overview.pagesPerSession);
        $('#audience-data_avg_visit_duration').html(data.overview.avgSessionDuration);
        $('#audience-data_new_users').html(data.overview.newUsers);
    }
