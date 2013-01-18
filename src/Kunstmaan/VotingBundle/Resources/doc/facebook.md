# Facebook

Make sure the Facebook plugin javascript has been added to your page.

See : https://developers.facebook.com/docs/reference/javascript/

## Facebook Like

The Facebook Like callback is triggered when a user clicks on the Like button on your page. The callback will then start an event through an AJAX call to the VotingController.

The following information is saved when such an event has been triggered :

### Data

The field "Reference" will hold the URL which has been liked.

### Route

The route "/voting-facebooklike" is being used for the Facebook Like callback AJAX request.

### Snippet

Add the following code to your template. This only adds the callback javascript function to your template and does not supply a Like button. You will need to include this piece of code only once to your page, it will work for all Like buttons.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' %}
```

To override the value of the Likes, simply add the value parameter to your include :

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'value' : value} %}
```

### Helper

The FacebookLikeHelper class provides methods to retrieve the likes by reference. It's also possible to retrieve the count or the combined value of the likes.

Name of the service : "kunstmaan_voting.helper.facebook.like"

## Facebook Send

The Facebook Send callback is triggered when a user sends a message by using the Send button on your page. The callback will be called when the Send has been completed and then starts an event through an AJAX call to the VotingController.

The following information is saved when such an event has been triggered :

### Data

The field "Reference" will hold the URL which has been sent.

### Route

The route "/voting-facebooksend" is being used for the Facebook Send callback AJAX request.

### Snippet

Add the following code to your template. This only adds the callback javascript function to your template and does not supply a Send button. You will need to include this piece of code only once to your page, it will work for all Send buttons.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:send-callback.html.twig' %}
```

To override the value of the Sends, simply add the value parameter to your include :

```twig
    {% include 'KunstmaanVotingBundle:Facebook:send-callback.html.twig' with {'value' : value} %}
```

### Helper

The FacebookSendHelper class provides methods to retrieve the sends by reference. It's also possible to retrieve the count or the combined value of the sends.

Name of the service : "kunstmaan_voting.helper.facebook.send"