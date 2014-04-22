// function setVisits(data) {
//     // clear the existing chart
//     $('#dashboard-chart--visits').html('');

//     // set the values
//     $('#data_returning_visits .data_visits').html(data.overview.returningVisits);
//     $('#data_returning_visits .data_percentage').html('(' + data.overview.returningVisitsPercentage + '%)');

//     $('#data_new_visits .data_visits').html(data.overview.newVisits);
//     $('#data_new_visits .data_percentage').html('(' + data.overview.newVisitsPercentage + '%)');

//     // render the chart
//     new Morris.Donut({
//         element: 'dashboard-chart--visits',
//         data: [
//             {
//                 label: 'Returning visits',
//                 value: removeNumberFormat(data.overview.returningVisits)
//             },
//             {
//                 label: 'New Visits',
//                 value: removeNumberFormat(data.overview.newVisits)
//             }
//         ],
//         colors: ['#c6c0e9', '#c6f5ca', '#a2d3f3'],
//         formatter: function(y, data) {
//             return '$' + y;
//         }
//     });
// }
