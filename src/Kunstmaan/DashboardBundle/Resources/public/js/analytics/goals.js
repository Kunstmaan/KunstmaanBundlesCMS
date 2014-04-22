    // create a list of all goals
    function setGoals(data) {
        // reset the overview list
        $('#goalOverview').html('');

        // create box for each goal
        for(var i = 0; i < data.goals.length; i++) {
        // for(var i = 0; i < 1; i++) {
            // create a goal overview box
            var chart = $('<div class="dashboard-goals-list__chart"></div>');
            $('#goalOverview').append(
                $('<li class="dashboard-goals-list__item">', {'id': 'goal'+data.goals[i]['id'], 'data-goal-id': data.goals[i]['id']}
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
                gridTextSize: 10,
                gridTextColor: '#ccc'
            });
        }
    }


