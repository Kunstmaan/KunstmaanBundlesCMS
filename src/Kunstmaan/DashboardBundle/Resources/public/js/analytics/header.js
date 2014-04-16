
    function setHeader(data) {
        $('#data_overview_visits').html(data.overview.visits + " Visitors");
        $('#data_overview_pageviews').html("(" + data.overview.pageViews + " pageviews)");
    }
