# LinkedIn

## LinkedIn Share

Add the LinkedIn Share button to your page : [LinkedIn plugins](http://developer.linkedin.com/plugins)

Add the 'data-onsuccess="linkedInShareCallback"' attribute to your share tag.

```HTML
    <script type="IN/Share" data-url="http://www.linkedin.com" data-onsuccess="linkedInShareCallback""></script>
```

The LinkedIn Share callback is triggered when a user completes the share action in the LinkedIn dialog window. When that happens, the javascript function "linkedInShareCallback()" will be called.
The function will then start an event through an AJAX call to the VotingController.

The following information is saved when such an event has been triggered :

### Data

The field "Reference" will hold the URL which has been shared.

### Route

The route "/voting-linkedinshare" is being used for the LinkedIn Share callback AJAX request.

### Snippet

Add the following code to your template. This only adds the javascript function from "data-onsuccess" to your template and does not supply a Share button.
You will need to include this piece of code only once to your page, it will work for all Share buttons.

```twig
    {% include 'KunstmaanVotingBundle:LinkedIn:share-callback.html.twig' %}
```

To override the value of the Shares, simply add the value parameter to your include :

```twig
    {% include 'KunstmaanVotingBundle:LinkedIn:share-callback.html.twig' with {'value' : value} %}
```

### Helper

The LinkedInShareHelper class provides methods to retrieve the shares by reference. It's also possible to retrieve the count or the combined value of the shares.

Name of the service : "kunstmaan_voting.helper.linkedin.share"