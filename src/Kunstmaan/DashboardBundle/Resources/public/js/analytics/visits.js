
    function setVisits(data) {
        $('#returningVisits .data_visits').html(data.overview.returningVisits);
        $('#returningVisits .data_percentage').html(data.extra.returningVisitsPercentage);

        $('#newVisits .data_visits').html(data.overview.newVisits);
        $('#newVisits .data_percentage').html(data.extra.newVisitsPercentage);

        $('#bounceVisits').html(data.overview.bounceRate + '%');
    }
