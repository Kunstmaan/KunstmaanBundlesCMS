// create a list of all goals
function setGoals(data) {
    var disableGoals = $('#disable-goals').attr('data-disable-goals');
    if (disableGoals) {
        return;
    }

    $('.dashboard-goals-list').attr('style', 'display:none;');
    $('#goalOverview' + data.overview.id).attr('style', 'display:block;');

    // create box for each goal
    for (var i = 0; i < data.goals.length; i++) {
        addGoalBox(data, i);
    }
}

function addGoalBox(data, i) {
    $('#goal' + data.goals[i]['id'] + ' .dashboard-goals-list__chart').html('');
    var chart = $('#goal' + data.goals[i]['id'] + ' .dashboard-goals-list__chart');

    $('#goal' + data.goals[i]['id'] + ' .dashboard-goals-list__item__title').html(data.goals[i]['name']);
    $('#goal' + data.goals[i]['id'] + ' .dashboard-goals-list__item__number').html('<strong>' + data.goals[i]['visits'] + '</strong>');

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
        gridTextColor: '#a7a7a7',
        resize: true,
        redraw: true
    });
}
