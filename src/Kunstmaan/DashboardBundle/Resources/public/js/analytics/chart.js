    // sets the chart data
    function setChart(data) {
        // clear the existing chart
        $('#js-dashboard-chart').html('');

        // create a data array
        var chartData = [];
        for (var i = 0; i < data.overview.chartData.visits.length; i+=1) {
            chartData.push({
                x : data.overview.chartData.visits[i].timestamp,
                visits : removeNumberFormat(data.overview.chartData.visits[i].visits),
                visitors : removeNumberFormat(data.overview.chartData.visitors[i].visits)
            });
        }

        // render the chart
        new Morris.Area({
            element: 'js-dashboard-chart',
            data: chartData,
            xkey: 'x',
            ykeys: ['visits', 'visitors'],
            labels: ['Visits', 'Unique visitors']
        });

        // set the values
        $('#data_visits').html(data.overview.visits);
        $('#data_visitors').html(data.overview.visitors);
        $('#data_pageviews').html(data.overview.pageViews);
        $('#data_pages_per_visit').html(data.overview.pagesPerVisit);
        $('#data_avg_visit_duration').html(data.overview.avgVisitDuration + ' seconds');
    }






