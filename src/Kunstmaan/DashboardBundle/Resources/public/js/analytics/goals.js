
    $(document).ready(function(){
        resetGoalChart();
    });

    // get goal data for a specific goal
    function getGoalData(goalOverview) {
        // get goal id
        var id = goalOverview.attr('data-goal-id');

        // set active button
        $('.active').attr('class', '');
        $('#goal'+id).attr('class', 'active');

        // get data
        $.get('widget/googleanalytics/getGoalChartData/'+id, function(data) {
            // render chart
            setGoalChartData(data, data.chartData.length <= 31);

            // set title in chart overview
            $('#goal_title').html(data.name);
        });
    }

    // create a list of all goals
    function setGoals(data) {
        // reset the overview list
        $('#goalOverview').html('');
        var html = '';

        // create HTML for each goal
        for(var i = 0; i < data.extra.goals.length; i++) {
            html    +=
                     '<li id="goal'+data.extra.goals[i]['id']+'" data-goal-id="'+data.extra.goals[i]['id']+'" onClick="getGoalData($(this))">'
                    +    '<div>'
                    +        data.extra.goals[i]['name']
                    +    '</div>'
                    +    '<span>'
                    +        data.extra.goals[i]['visits']
                    +    '</span>'
                    +'</li>';
        }

        // add the HTML to the list
        $('#goalOverview').html(html);
    }

    var goalChartData = [];
    var goalChartLabels = [];

    // reset the chart
    function resetGoalChart() {
        $('#goalOverview li:first-child').trigger("click");
    }

    // sets the chart data
    function setGoalChartData(data, showLabels, isDayData) {
        goalChartData = [];
        goalChartLabels = [];

        if (data == null) {
            return;
        }

        var increment = Math.ceil(data.chartData.length / 23);
        var value = 0;
        for (var i = 0; i < data.chartData.length; i+=1) {
            value += parseInt(data.chartData[i].visits);
            if (i%increment == 0) {
                goalChartData.push(value);
                goalChartLabels.push(data.chartData[i].timestamp);
                value = 0;
            }
        }

        initGoalChart();
    }

    // sets chart width and height
    function resizeGoalChart() {
        var chartWidth = $('#js-goal-dashboard-chart').parent().width();
        var chartHeight = $('#js-goal-dashboard-chart').height();
        $('#js-goal-dashboard-chart').attr('width', chartWidth );
        $('#js-goal-dashboard-chart').attr('height', chartHeight );
    }

    // inits the chart
    initGoalChart = function() {
        var barGoalChartData = {
            labels : goalChartLabels,
            datasets : [
                {
                    fillColor : "rgba(41, 151, 206, 0.3)",
                    strokeColor : "rgb(41, 151, 206)",
                    pointColor : "rgb(41, 151, 206)",
                    pointStrokeColor : "#fff",
                    data : goalChartData,
                    scaleShowLabels : true
                }
            ]
        };

        // chart scale values
        Array.prototype.max = function() {
          return Math.max.apply(null, this);
        };
        var max = Math.max.apply(null, goalChartData);
        var steps = max < 10 ? max : 10;

        resizeGoalChart();
        var chart = new Chart(document.getElementById("js-goal-dashboard-chart").getContext("2d")).Line(barGoalChartData,
            {scaleOverride: true, scaleStepWidth: Math.ceil(max/steps), scaleSteps: steps, animation:true});
    };
