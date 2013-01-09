# Facebook

Make sure the Facebook plugin javascript has been added to your page.

See : https://developers.facebook.com/docs/reference/javascript/

## Facebook Like

The Facebook Like callback is triggered when a user clicks on the Like button on your page. The callback will then start an event through an AJAX call to the VotingController.

The following information is saved when such an event has been triggered :

* Reference : the URL which has been liked
* IP : The IP address of the user who liked
* Value : The value this particular like is worth, default value is set to 1 and can be overridden by any integer value
* Timestamp : The timestamp of the like (automatically filled in on PrePersist)

### Route

The route "/voting-facebooklike" is being used for the Facebook Like callback AJAX request

### Snippet

Add the following code to your template.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' %}
```

To override the value of the Likes, simply add the value parameter to your include :

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'value' : value} %}
```

### Helper

The FacebookLikeHelper class supplies the methods to retrieve the likes by reference. It's also possible to retrieve the count or the combined value of the likes.