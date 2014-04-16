
    function setReferrals(data) {
        if (data.extra.referrals.length != 0) {
            $('#data_top_referral_no_data').html('');
            var html = '';
            for(var i = 0; i < data.extra.referrals.length; i++) {
                html    +=
                         '<li class="db-block__list__item">'
                        +    '<p class="db-block__text db-block__text--left" id="data_overview_top_referral_first">'
                        +        data.extra.referrals[i]['name']
                        +    '</p>'
                        +    '<p class="db-block__stats db-block__stats--right" id="data_overview_top_referral_first_value">'
                        +        data.extra.referrals[i]['visits']
                        +    '</p>'
                        +'</li>';
            }
            $('#referrals').html('');
            $('#referrals').html(html);
        } else {
            $('#data_top_referral_no_data').html('No data available for Top Referrals');
            $('#referrals').html('');
        }
    }
