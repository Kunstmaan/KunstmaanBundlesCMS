
    function setSearches(data) {
        if (data.extra.searches.length != 0) {
            $('#data_top_search_no_data').html('');
            var html = '';
            for(var i = 0; i < data.extra.searches.length; i++) {
                html    +=
                         '<li class="db-block__list__item">'
                        +    '<p class="db-block__text db-block__text--left" id="data_overview_top_search_first">'
                        +        data.extra.searches[i]['name']
                        +    '</p>'
                        +    '<p class="db-block__stats db-block__stats--right" id="data_overview_top_search_first_value">'
                        +        data.extra.searches[i]['visits']
                        +    '</p>'
                        +'</li>';
            }
            $('#searches').html('');
            $('#searches').html(html);
        } else {
            $('#data_top_search_no_data').html('No data available for Top Search');
            $('#searches').html('');
        }
    }
