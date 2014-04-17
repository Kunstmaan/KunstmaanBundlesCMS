
    function setReferrals(data) {
        if (data.referrals.length != 0) {
            // render a list for the top referrals
            $('#data_top_referral_no_data').html('');
            var html = '';
            for(var i = 0; i < data.referrals.length; i++) {
                html    +=
                         '<li class="db-block__list__item">'
                        +    '<p class="db-block__text db-block__text--left">'
                        +        data.referrals[i]['name']
                        +    '</p>'
                        +    '<p class="db-block__stats db-block__stats--right">'
                        +        data.referrals[i]['visits']
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
