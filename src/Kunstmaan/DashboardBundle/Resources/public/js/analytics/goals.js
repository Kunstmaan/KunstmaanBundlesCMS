    // create a list of all goals
    function setGoals(data) {
        // reset the overview list
        $('#goalOverview').html('');

        // create box for each goal
        for(var i = 0; i < data.goals.length; i++) {
        // for(var i = 0; i < 1; i++) {
            // create a goal overview box
            var chart = $('<div>', {'class': 'dashboard-chart', 'style': 'height:150px;width:100%'});
            $('#goalOverview')
            .append(
                $('<li>', {'id': 'goal'+data.goals[i]['id'], 'data-goal-id': data.goals[i]['id']}
                ).append(
                    chart
                ).append(
                    $('<div>').html(data.goals[i]['name'])
                ).append(
                    $('<span>').html(data.goals[i]['visits'])
                )
            );

            // create a data array
            var chartData = [];
            for (var j = 0; j < data.goals[i].chartData.length; j+=1) {
                chartData.push({
                    x : data.goals[i].chartData[j].timestamp,
                    visits : parseInt(data.goals[i].chartData[j].visits)
                });
            }

            // render the chart
            new Morris.Line({
                element: chart,
                lineWidth: 1,
                lineColors: ['#59ace2'],
                fillOpacity: '.4',
                hideHover: 'auto',
                pointSize: 0,
                data: chartData,
                xkey: 'x',
                ykeys: ['visits'],
                labels: ['Users'],
                axes: false,
                grid: false
            });
        }
    }

    // function initLineChart(canvas, json) {
    //     // data arrays for the chart
    //     var chartData = [];
    //     var chartLabels = [];

    //     // fill the data arrays
    //     for (var i = 0; i < json.length; i++) {
    //         chartData.push(parseInt(json[i].visits));
    //         chartLabels.push('');
    //     }

    //     // create linechart data
    //     var lineChartData = {
    //         labels : chartLabels,
    //         datasets : [
    //             {
    //                 fillColor : "rgba(41, 151, 206, 0.3)",
    //                 strokeColor : "rgb(41, 151, 206)",
    //                 pointColor : "rgb(41, 151, 206)",
    //                 pointStrokeColor : "#fff",
    //                 data : chartData,
    //                 scaleShowLabels : false
    //             }
    //         ]
    //     };

    //     // chart scale values
    //     Array.prototype.max = function() {
    //       return Math.max.apply(null, this);
    //     };
    //     var max = Math.max.apply(null, chartData);
    //     var steps = max < 5 ? max : 5;

    //     // render line chart
    //     var ctx = canvas.getContext("2d");
    //     var chart = new Chart(ctx).Line(lineChartData, {scaleOverride: true, scaleStepWidth: Math.ceil(max/steps), scaleSteps: steps, animation:false});
    // }



