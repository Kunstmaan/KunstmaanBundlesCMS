
    function setCampaigns(data) {
        if (data.extra.campaigns.length != 0) {
            $('#data_top_campaigns_no_data').html('');
            var html = '';
            for(var i = 0; i < data.extra.campaigns.length; i++) {
                html    +=
                         '<li class="db-block__list__item">'
                        +    '<p class="db-block__text db-block__text--left">'
                        +        data.extra.campaigns[i]['name']
                        +    '</p>'
                        +    '<p class="db-block__stats db-block__stats--right">'
                        +        data.extra.campaigns[i]['visits']
                        +    '</p>'
                        +'</li>';
            }
            $('#campaigns').html('');
            $('#campaigns').html(html);
        } else {
            $('#data_top_search_no_data').html('No data available for Top Search');
            $('#campaigns').html('');
        }
    }
