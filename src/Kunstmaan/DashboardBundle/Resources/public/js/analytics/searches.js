
    function setSearches(data) {
        if (data.searches.length != 0) {
            // render a list for the top searches
            $('#data_top_search_no_data').html('');
            var html = '';
            for(var i = 0; i < data.searches.length; i++) {
                html    +=
                         '<li class="db-block__list__item">'
                        +    '<p class="db-block__text db-block__text--left">'
                        +        data.searches[i]['name']
                        +    '</p>'
                        +    '<p class="db-block__stats db-block__stats--right">'
                        +        data.searches[i]['visits']
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
