
    function setVisits(data) {
        $('#data_returning_visits .data_visits').html(data.overview.returningVisits);
        $('#data_returning_visits .data_percentage').html('(' + data.overview.returningVisitsPercentage + '%)');

        $('#data_new_visits .data_visits').html(data.overview.newVisits);
        $('#data_new_visits .data_percentage').html('(' + data.overview.newVisitsPercentage + '%)');

        var chartData = [
            {
                value: removeNumberFormat(data.overview.returningVisits),
                color:"rgba(41, 151, 206, 0.6)"
            },
            {
                value : removeNumberFormat(data.overview.newVisits),
                color : "rgba(41, 200, 150, 0.6)"
            }
        ]

        var myLine = new Chart(document.getElementById("js-visits-dashboard-chart").getContext("2d")).Pie(chartData);

    }
