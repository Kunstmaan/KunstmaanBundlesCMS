   $(document).ready(function() {
        $(window).resize(function (){
            initChart();
        });
    });

    // load dashboard data
    var chartData = [];
    var chartLabels = [];

    // sets the chart data
    function setChart(data) {
        chartData = [];
        chartLabels = [];
        var increment = Math.ceil(data.overview.chartData.length / 26);
        var value = 0;
        for (var i = 0; i < data.overview.chartData.length; i+=1) {
            value += parseInt(data.overview.chartData[i].visits);
            if (i%increment == 0) {
                chartData.push(value);
                chartLabels.push(data.overview.chartData[i].timestamp);
                value = 0;
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
                    data : chartData,
                    scaleShowLabels : true
                }
            ]
        };

        // chart scale values
        Array.prototype.max = function() {
          return Math.max.apply(null, this);
        };
        var max = Math.max.apply(null, chartData);
        var steps = max < 10 ? max : 10;

        resizeChart();
        var myLine = new Chart(document.getElementById("js-dashboard-chart").getContext("2d")).Line(barChartData,
            {scaleOverride: true, scaleStepWidth: Math.ceil(max/steps), scaleSteps: steps, animation:true});
    };





