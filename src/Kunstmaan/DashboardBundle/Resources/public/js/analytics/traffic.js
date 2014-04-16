
    function setTraffic(data) {
        $('#data_overview_traffic_direct').html(data.overview.trafficDirect + " (" + data.extra.trafficDirectPercentage + "%)");
        $('#data_overview_traffic_referral').html(data.overview.trafficReferral + " (" + data.extra.trafficReferralPercentage + "%)");
        $('#data_overview_traffic_searchEngine').html(data.overview.trafficSearchEngine + " (" + data.extra.trafficSearchEnginePercentage + "%)");
    }
