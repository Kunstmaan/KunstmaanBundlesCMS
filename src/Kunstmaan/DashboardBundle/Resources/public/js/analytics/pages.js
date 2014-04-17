
    function setPages(data) {
        if (data.extra.pages.length != 0) {
            $('#data_top_pages_no_data').html('');
            var html = '';
            for(var i = 0; i < data.extra.pages.length; i++) {
                html    +=
                         '<li class="db-block__list__item">'
                        +    '<p class="db-block__text db-block__text--left">'
                        +        '<a href="'+data.extra.pages[i]['name']+'" target="_blank">'+data.extra.pages[i]['name']+'</a>'
                        +    '</p>'
                        +    '<p class="db-block__stats db-block__stats--right">'
                        +        data.extra.pages[i]['visits']
                        +    '</p>'
                        +'</li>';
            }
            $('#data_top_pages').html('');
            $('#data_top_pages').html(html);
        } else {
            $('#data_top_pages_no_data').html('No data available for Top Pages');
            $('#data_top_pages').html('');
        }
    }
