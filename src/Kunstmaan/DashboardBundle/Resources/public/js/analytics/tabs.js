    $(document).ready(function() {
        // show first tab
        var tab = $('.dashboard-tabs__item:nth-child(2) > div');

        function switchTab(id, url) {
            resetGoals();
            data = getData(id);

            if(data.responseCode === 200) {
                $('.dashboard-tabs__item').removeClass('dashboard-tabs__item--active');
                $('#tab'+id).addClass('dashboard-tabs__item--active');

                if (data.overview.sessions === 0) {
                    $('#data_no_overview').css('display', 'block');
                    $('#data_overview').css('display', 'none');
                } else {
                    $('#data_no_overview').css('display', 'none');
                    $('#data_overview').css('display', 'block');

                    setChart(data);
                    setTimeout(function() {
                        setGoals(data);
                    }, 500);
                }
            }
        }

        var cache = {};
        function getData(id) {
            if (cache.id) {
                console.log('from cache');
                return cache.id;
            } else {
                $.get('widget/googleanalytics/getOverview/' + id, function(data) {
                    console.log(cached);
                    cache.id = data;
                    return data;
                });
            }
        }

        switchTab(tab.attr('data-id'), tab.attr('data-path'));

        // Tab switcher
        $('.dashboard-tabs__item').on('click', function(){
            var id = $(this).find('.dashboard-tabs__controller').attr('data-id');
            var url = $(this).find('.dashboard-tabs__controller').attr('data-path');
            switchTab(id, url);
        });
    });

