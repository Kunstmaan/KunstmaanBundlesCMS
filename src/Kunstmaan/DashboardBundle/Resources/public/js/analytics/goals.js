    // create a list of all goals
    function setGoals(data) {
        // reset the overview list
        $('#goalOverview').html('');
        var html = '';
            $('#goalOverview').html('');

        // create HTML for each goal
        for(var i = 0; i < data.extra.goals.length; i++) {

            var overview = document.createElement('li');
            overview.setAttribute('id', 'goal'+data.extra.goals[i]['id']);
            overview.setAttribute('data-goal-id', data.extra.goals[i]['id']);

            var chartCanvas = document.createElement('canvas');
            chartCanvas.setAttribute('id', 'js-goal-dashboard-chart-'+data.extra.goals[i]['id']);
            chartCanvas.setAttribute('class','dashboard-chart');
            chartCanvas.setAttribute('height','100');
            overview.appendChild(chartCanvas);

            var nameDiv = document.createElement('div');
            var nameText = document.createTextNode(data.extra.goals[i]['name'])
            nameDiv.appendChild(nameText);
            overview.appendChild(nameDiv);

            var visitsSpan = document.createElement('span');
            var visitsText = document.createTextNode(data.extra.goals[i]['visits'])
            visitsSpan.appendChild(visitsText);
            overview.appendChild(visitsSpan);

            $('#goalOverview').append(overview);
            initLineChart(chartCanvas, data.extra.goals[i]['chart_data']);

        }

        // add the HTML to the list
        //$('#goalOverview').html(html);
    }

    function initLineChart(canvas, json) {
        var chartData = [];
        var chartLabels = [];
        for (var i = 0; i < json.length; i++) {
            chartData.push(parseInt(json[i].visits));
            chartLabels.push('');
        }
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

        var ctx = canvas.getContext("2d");
        var chart = new Chart(ctx).Line(lineChartData, {scaleOverride: true, scaleStepWidth: Math.ceil(max/steps), scaleSteps: steps, animation:false});
    }



