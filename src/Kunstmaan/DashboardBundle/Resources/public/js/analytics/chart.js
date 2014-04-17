   $(document).ready(function() {
        $(window).resize(function (){
            initChart();
        });
    });


    function setHeader(data) {
    }

    // load dashboard data
    var chartVisitsData = [];
    var chartVisitorsData = [];
    var chartLabels = [];

    // sets the chart data
    function setChart(data) {
        $('#data_visits').html(data.overview.visits);
        $('#data_visitors').html(data.overview.visitors);
        $('#data_pageviews').html(data.overview.pageViews);
        $('#data_pages_per_visit').html(data.overview.pagesPerVisit);
        $('#data_avg_visit_duration').html(data.overview.avgVisitDuration);

        chartVisitsData = [];
        chartVisitorsData = [];
        chartLabels = [];
        var increment = Math.ceil(data.overview.chartData.visits.length / 26);
        var valueVisits = 0;
        var valueVisitors = 0;

        for (var i = 0; i < data.overview.chartData.visits.length; i+=1) {
            valueVisits += removeNumberFormat(data.overview.chartData.visits[i].visits);
            valueVisitors += removeNumberFormat(data.overview.chartData.visitors[i].visits);
            if (i%increment == 0) {
                chartVisitsData.push(valueVisits);
                chartVisitorsData.push(valueVisitors);
                chartLabels.push(data.overview.chartData.visits[i].timestamp);
                valueVisits = 0;
                valueVisitors = 0;
            }
        }
    }

    // sets chart width and height
    function resizeChart() {
        var chartWidth = $('#js-dashboard-chart').parent().width();
        var chartHeight = $('#js-dashboard-chart').height();
        $('#js-dashboard-chart').attr('width', chartWidth );
        $('#js-dashboard-chart').attr('height', chartHeight );
    }

    // inits the chart
    function initChart() {
        var barChartData = {
            labels : chartLabels,
            datasets : [
                {
                    fillColor : "rgba(41, 151, 206, 0.3)",
                    strokeColor : "rgb(41, 151, 206)",
                    pointColor : "rgb(41, 151, 206)",
                    pointStrokeColor : "#fff",
                    data : chartVisitsData,
                    scaleShowLabels : true
                },
                {
                    fillColor : "rgba(41, 200, 150, 0.3)",
                    strokeColor : "rgb(41, 200, 150)",
                    pointColor : "rgb(41, 200, 150)",
                    pointStrokeColor : "#fff",
                    data : chartVisitorsData,
                    scaleShowLabels : true
                }
            ]
        };

        // chart scale values
        Array.prototype.max = function() {
          return Math.max.apply(null, this);
        };
        var maxData = [];
        maxData[0] = Math.max.apply(null, chartVisitsData);
        maxData[1] = Math.max.apply(null, chartVisitorsData);
        var max = Math.max.apply(null, maxData);
        var steps = max < 10 ? max : 10;

        resizeChart();
        var myLine = new Chart(document.getElementById("js-dashboard-chart").getContext("2d")).Line(barChartData,
            {scaleOverride: true, scaleStepWidth: Math.ceil(max/steps), scaleSteps: steps, animation:true});
    };





