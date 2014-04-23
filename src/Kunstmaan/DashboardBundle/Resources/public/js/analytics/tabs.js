    $(function() {
        // show first tab
        var tab = $('.dashboard-tabs__item:nth-child(2) > div');

        function switchTab(id, url) {
            $('#data_overview').addClass('dashboard__content--loading');
            $.ajax({
                type: 'get',
                url: url,
                cache: false,
                success: function(data) {
                    $
                    $('.dashboard-tabs__item').removeClass('dashboard-tabs__item--active');
                    $('#tab'+id).addClass('dashboard-tabs__item--active');

                    if (data.overview.sessions === 0) {
                        $('#data_no_overview').css('display', 'block');
                        $('#data_overview').css('display', 'none');
                    } else {
                        $('#data_no_overview').css('display', 'none');
                        $('#data_overview').css('display', 'block');
                        $('#data_overview').removeClass('dashboard__content--loading');
                        // add functions here to add component behaviour
                        // these functions are declared in a per-template js file (public/js/analytics/)
                        setChart(data);
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

