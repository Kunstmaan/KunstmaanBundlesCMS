    // sets the chart data
    function setChart(data) {
        // clear the existing chart
        $('#js-dashboard-chart').html('');

        // create a data array
        var chartData = [];
        for (var i = 0; i < data.overview.chartData.visits.length; i+=1) {
            chartData.push({
                x : data.overview.chartData.visits[i].timestamp,
                sessions : removeNumberFormat(data.overview.chartData.visits[i].visits),
                users : removeNumberFormat(data.overview.chartData.visitors[i].visits)
            });
        }

        // render the chart
        new Morris.Area({
            element: 'js-dashboard-chart',
            lineWidth: 1,
            lineColors: ['#59ace2', '#8175c7'],
            fillOpacity: '.4',
            hideHover: 'auto',
            pointSize: 0,
            data: chartData,
            xkey: 'x',
            ykeys: ['sessions', 'users'],
            labels: ['Sessions', 'Users'],
            behaveLikeLine: true
        });

        // set the values
        $('#data_visits').html(data.overview.visits);
        $('#data_visitors').html(data.overview.visitors);
        $('#data_pageviews').html(data.overview.pageViews);
        $('#data_pages_per_visit').html(data.overview.pagesPerVisit);
        $('#data_avg_visit_duration').html(data.overview.avgVisitDuration + ' seconds');
    }






