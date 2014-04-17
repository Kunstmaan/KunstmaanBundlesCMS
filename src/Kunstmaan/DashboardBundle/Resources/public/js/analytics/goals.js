    // create a list of all goals
    function setGoals(data) {
        // reset the overview list
        $('#goalOverview').html('');

        // create box for each goal
        for(var i = 0; i < data.goals.length; i++) {
            // using DOM here because chart.js doesn't find these added elements if just rendered with HTML
            var overview = document.createElement('li');
            overview.setAttribute('id', 'goal'+data.goals[i]['id']);
            overview.setAttribute('data-goal-id', data.goals[i]['id']);

            var chartCanvas = document.createElement('canvas');
            chartCanvas.setAttribute('id', 'js-goal-dashboard-chart-'+data.goals[i]['id']);
            chartCanvas.setAttribute('class','dashboard-chart');
            chartCanvas.setAttribute('height','100');
            overview.appendChild(chartCanvas);

            var nameDiv = document.createElement('div');
            var nameText = document.createTextNode(data.goals[i]['name'])
            nameDiv.appendChild(nameText);
            overview.appendChild(nameDiv);

            var visitsSpan = document.createElement('span');
            var visitsText = document.createTextNode(data.goals[i]['visits'])
            visitsSpan.appendChild(visitsText);
            overview.appendChild(visitsSpan);

            // add elements to DOM
            $('#goalOverview').append(overview);

            // init the chart for a goal box
            initLineChart(chartCanvas, data.goals[i]['chartData']);
        }
    }

    function initLineChart(canvas, json) {
        // data arrays for the chart
        var chartData = [];
        var chartLabels = [];

        // fill the data arrays
        for (var i = 0; i < json.length; i++) {
            chartData.push(parseInt(json[i].visits));
            chartLabels.push('');
        }

        // create linechart data
        var lineChartData = {
            labels : chartLabels,
            datasets : [
                {
                    fillColor : "rgba(41, 151, 206, 0.3)",
                    strokeColor : "rgb(41, 151, 206)",
                    pointColor : "rgb(41, 151, 206)",
                    pointStrokeColor : "#fff",
                    data : chartData,
                    scaleShowLabels : false
                }
            ]
        };

        // chart scale values
        Array.prototype.max = function() {
          return Math.max.apply(null, this);
        };
        var max = Math.max.apply(null, chartData);
        var steps = max < 5 ? max : 5;

        // render line chart
        var ctx = canvas.getContext("2d");
        var chart = new Chart(ctx).Line(lineChartData, {scaleOverride: true, scaleStepWidth: Math.ceil(max/steps), scaleSteps: steps, animation:false});
    }



