
    function setVisits(data) {
        // clear the existing chart
        $('#js-visits-dashboard-chart').html('');

        // set the values
        $('#data_returning_visits .data_visits').html(data.overview.returningVisits);
        $('#data_returning_visits .data_percentage').html('(' + data.overview.returningVisitsPercentage + '%)');

        $('#data_new_visits .data_visits').html(data.overview.newVisits);
        $('#data_new_visits .data_percentage').html('(' + data.overview.newVisitsPercentage + '%)');

        // render the chart
        new Morris.Donut({
          element: 'js-visits-dashboard-chart',
          data: [
            {label: "Returning visits", value: removeNumberFormat(data.overview.returningVisits)},
            {label: "New Visits", value: removeNumberFormat(data.overview.newVisits)}
          ],
          colors: ['#2997ce', '#74B58E', '#F39A2E']
        });

    }
