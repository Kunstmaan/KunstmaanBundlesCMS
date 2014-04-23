    // sets the chart data
    function setChart(data) {
        // clear the existing chart
        $('#dashboard-chart--audience').html('');

        var top = 0;

        // create a data array
        var chartData = [];
        for (var i = 0; i < data.overview.chartData.sessions.length; i+=1) {
            var pageviewVisits = removeNumberFormat(data.overview.chartData.pageviews[i].visits);

            if (top < pageviewVisits) {
                top = pageviewVisits;
            }

            chartData.push({
                x: data.overview.chartData.sessions[i].timestamp,
                sessions: removeNumberFormat(data.overview.chartData.sessions[i].visits),
                users: removeNumberFormat(data.overview.chartData.users[i].visits),
                pageviews: pageviewVisits,
                newusers: removeNumberFormat(data.overview.chartData.newusers[i].visits)
            });
        }

        // render the chart
        new Morris.Area({
            element: 'dashboard-chart--audience',
            lineWidth: 1,
            lineColors: [ '#8ac9e1','#ce77b6', '#ee9c27', '#d13f37'],
            fillOpacity: '.9',
            hideHover: 'auto',
            pointSize: 0,
            data: chartData,
            xkey: 'x',
            ykeys: ['pageviews', 'sessions', 'users','newusers'],
            ymax:top,
            labels: ['Pageviews', 'Sessions', 'Users', 'New Users'],
            behaveLikeLine: true,
            gridTextColor: '#a7a7a7',
            smooth: true
        });

        // set the values
        $('#audience-data_visits').html(data.overview.sessions);
        $('#audience-data_visitors').html(data.overview.users);
        $('#audience-data_pageviews').html(data.overview.pageViews);
        $('#audience-data_pages_per_visit').html(data.overview.pagesPerSession);
        $('#audience-data_avg_visit_duration').html(data.overview.avgSessionDuration + ' <small>sec</small>');
        $('#audience-data_new_users').html(data.overview.newUsers);
    }
