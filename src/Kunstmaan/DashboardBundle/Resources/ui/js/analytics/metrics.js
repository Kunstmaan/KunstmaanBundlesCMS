// sets the metrics data
function setMetrics(data) {
    $('#audience-data_visits').html(data.overview.sessions);
    $('#audience-data_visitors').html(data.overview.users);
    $('#audience-data_pageviews').html(data.overview.pageViews);
    $('#audience-data_pages_per_visit').html(data.overview.pagesPerSession);
    $('#audience-data_avg_visit_duration').html(data.overview.avgSessionDuration);
    $('#audience-data_new_users').html(data.overview.newUsers);

    if (data.goals.length == 0) {
        $('.dashboard-chart-list').removeClass('dashboard-chart-list__sidebar');
    } else {
        $('.dashboard-chart-list').addClass('dashboard-chart-list__sidebar');
    }
}
