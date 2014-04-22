
    // function setTraffic(data) {
    //     // clear the existing chart
    //     $('#dashboard-chart--traffic').html('');

    //     // set the values
    //     $('#data_desktop_traffic .data_visits').html(data.overview.desktopTraffic);
    //     $('#data_desktop_traffic .data_percentage').html('(' + data.overview.desktopTrafficPercentage + '%)');

    //     $('#data_mobile_traffic .data_visits').html(data.overview.mobileTraffic);
    //     $('#data_mobile_traffic .data_percentage').html('(' + data.overview.mobileTrafficPercentage + '%)');

    //     $('#data_tablet_traffic .data_visits').html(data.overview.tabletTraffic);
    //     $('#data_tablet_traffic .data_percentage').html('(' + data.overview.tabletTrafficPercentage + '%)');

    //     // set the chart data
    //     // var chartData = [
    //     //     {
    //     //         value: removeNumberFormat(data.overview.desktopTraffic),
    //     //         color:"rgba(41, 151, 206, 0.6)"
    //     //     },
    //     //     {
    //     //         value : removeNumberFormat(data.overview.mobileTraffic),
    //     //         color : "rgba(41, 200, 150, 0.6)"
    //     //     },
    //     //     {
    //     //         value : removeNumberFormat(data.overview.tabletTraffic),
    //     //         color : "rgba(41, 50, 200, 0.6)"
    //     //     }
    //     // ]

    //     // render the chart
    //     new Morris.Donut({
    //       element: 'dashboard-chart--traffic',
    //       data: [
    //         {label: 'Desktop visitors', value: removeNumberFormat(data.overview.desktopTraffic)},
    //         {label: 'Mobile visitors', value: removeNumberFormat(data.overview.mobileTraffic)},
    //         {label: 'Tablet visitors', value: removeNumberFormat(data.overview.tabletTraffic)}
    //       ],
    //       colors: ['#c6c0e9', '#c6f5ca', '#a2d3f3']
    //     });
    // }
