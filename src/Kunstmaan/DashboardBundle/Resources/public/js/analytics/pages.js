
    function setPages(data) {
        if (data.pages.length != 0) {
            // render a list for the top pages
            $('#data_top_pages_no_data').html('');
            var html = '';
            for(var i = 0; i < data.pages.length; i++) {
                html    +=
                         '<li class="db-block__list__item">'
                        +    '<p class="db-block__text db-block__text--left">'
                        +        '<a href="'+data.pages[i]['name']+'" target="_blank">'+data.pages[i]['name']+'</a>'
                        +    '</p>'
                        +    '<p class="db-block__stats db-block__stats--right">'
                        +        data.pages[i]['visits']
                        +    '</p>'
                        +'</li>';
            }
            $('#data_top_pages').html('');
            $('#data_top_pages').html(html);
        } else {
            // no available data
            $('#data_top_pages_no_data').html('No data available for Top Pages');
            $('#data_top_pages').html('');
        }
    }
