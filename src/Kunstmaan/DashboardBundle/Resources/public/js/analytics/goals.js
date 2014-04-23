    // create a list of all goals
    function setGoals(data) {
        resetGoals();
        // create box for each goal
        for(var i = 0; i < data.goals.length; i++) {
            addGoalBox(data, i);
        }
    }

    function resetGoals() {
        // reset the overview list
        $('#goalOverview').html('');
    }


    function addGoalBox(data, i) {
        // create a goal overview box
        var chart = $('<div class="dashboard-goals-list__chart">');
        $('#goalOverview').append(
            $('<li class="dashboard-goals-list__item">', {'id': 'goal'+data.goals[i]['id'], 'data-goal-id': data.goals[i]['id']}
            ).append(
                $('<span class="dashboard-goals-list__item__title">').html(data.goals[i]['name'])
            ).append(
                $('<span class="dashboard-goals-list__item__number">').html('<strong>' + data.goals[i]['visits'] + '</strong> conversions')
            ).append(
                chart
            )
        );

        // render the chart
        new Morris.Area({
            element: chart,
            lineWidth: 2,
            lineColors: ['#8ac9e1'],
            fillOpacity: '.9',
            hideHover: 'auto',
            pointSize: 0,
            data: data.goals[i].chartData,
            xkey: 'timestamp',
            ykeys: ['conversions'],
            labels: ['Conversions'],
            gridTextSize: 10,
            gridTextColor: '#a7a7a7'
        });
    }


