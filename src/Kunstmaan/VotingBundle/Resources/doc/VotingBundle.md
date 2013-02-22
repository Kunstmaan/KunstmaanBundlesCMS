# Data

Each vote has the following :

### Timestamp

The timestamp is filled in at PrePersist.

### Reference

The reference is a value by which the vote kan be grouped together. This can be the URL for Facebook Likes, an unique ID referring to a certain blog post, a certain category, ...

### Meta

This field is available to you to include a meta entity containing additional information (user info, ...).

### IP

The IP address of the user triggering the voting event.

### Value

Value is by default 1.

In most cases the default value of 1 is all you need. But in a few cases you may want to give your vote a specific value. For example, you track Facebook Likes and for each like you commit yourself to donate €0.10 to sharity. This means you can give a vote a value of 10. Let's say you have 283 votes. When requesting the sum of your total vote value, you'll get 2830. Divide it by 100 (100 cents in 1 euro) and you'll get the amount you committed yourself to donate to sharity : €28,3.

# Configuration

You can override the default values for each type of vote in your config.yml

```YAML
kunstmaan_voting:
    actions:
        facebook_like:
            default_value: %kuma_voting_default_value%
        facebook_send:
            default_value: %kuma_voting_default_value%
        linkedin_share:
            default_value: %kuma_voting_default_value%
```

## Custom action and parameters

You can add your own action to it and even add additional parameters to the existing or your own action :

```YAML
kunstmaan_voting:
    actions:
        facebook_like:
            default_value: 2
        facebook_send:
            default_value: %kuma_voting_default_value%
        linkedin_share:
            default_value: 5
        yourownvote:
            default_value: 10
            other_param: "some_value"
```

You can retrieve these values the following way :

```PHP
$actions = $this->container->getParameter('kuma_voting.actions');
if (isset($actions['yourownvote'])) {
    $vote->setValue($actions['yourownvote']['default_value']);
    $other_param = $actions['yourownvote']['other_param'];
}
```

# Standard Up & Down voting

These are the standard up and down votes for your voting needs

## Route

The route "/voting-upvote" and "/voting-downvote" are being used for the vote AJAX requests.

## Template

The 'vote.html.twig' template includes both Up and Down vote button. You can hide either using a parameter, see below.

Inside the template, both votes are inside a div with id "vote". The "reference" parameter will be added as a suffix. So when you use "12345" as your vote reference, the div containing the button will have the id "vote12345" and both javascript functions will also incorporate the reference in their function name.

When the AJAX call has been successfully completed, the div the voting contains will be replaced by the 'voted.html.twig' template. This will hide the vote buttons and give the user feedback his vote has been registered.

## Snippets

To add the vote buttons, add the following include to your template. This adds both Up and Down vote buttons.

```twig
    {% include 'KunstmaanVotingBundle:UpDown:vote.html.twig' %}
```

The following parameters are optional :

### reference (recommended)

Add the reference parameter to your include to link these votes to your object which needs to be voted upon. In this case, the most ideal reference would be an unique identifier you vote should be accounted to (an Entity ID perhaps).

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'reference' : 12345} %}
```

### upValue & DownValue

The default value of the up vote is 1, for the down vote it is -1. This van be changed in your config.xml, but can also be changed here with this parameter.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'upValue' : 1, 'downValue' : -1} %}
```

### hideDownVote & hideUpVote

In case you only need one of either voting possibility, you can use these 2 parameters to hide what you do not need.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'hideDownVote' : true} %}

    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'hideUpVote' : true} %}
```

## Helper

The UpVoteHelper and DownVoteHelper classes provide methods to retrieve the votes by reference. It's also possible to retrieve the count or the combined value of the votes.

Name of the service : "kunstmaan_voting.helper.upvote" and "kunstmaan_voting.helper.downvote"

# Supported services

## Facebook

Make sure the Facebook plugin javascript has been added to your page.

See : https://developers.facebook.com/docs/reference/javascript/

### Facebook Like

The Facebook Like callback is triggered when a user clicks on the Like button on your page. The callback will then start an event through an AJAX call to the VotingController.

The following information is saved when such an event has been triggered :

#### Data

The field "Reference" will hold the URL which has been liked.

#### Route

The route "/voting-facebooklike" is being used for the Facebook Like callback AJAX request.

#### Snippet

Add the following code to your template. This only adds the callback javascript function to your template and does not supply a Like button. You will need to include this piece of code only once to your page, it will work for all Like buttons.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' %}
```

To override the value of the Likes, simply add the value parameter to your include :

```twig
    {% include 'KunstmaanVotingBundle:Facebook:like-callback.html.twig' with {'value' : value} %}
```

#### Helper

The FacebookLikeHelper class provides methods to retrieve the likes by reference. It's also possible to retrieve the count or the combined value of the likes.

Name of the service : "kunstmaan_voting.helper.facebook.like"

### Facebook Send

The Facebook Send callback is triggered when a user sends a message by using the Send button on your page. The callback will be called when the Send has been completed and then starts an event through an AJAX call to the VotingController.

The following information is saved when such an event has been triggered :

#### Data

The field "Reference" will hold the URL which has been sent.

#### Route

The route "/voting-facebooksend" is being used for the Facebook Send callback AJAX request.

#### Snippet

Add the following code to your template. This only adds the callback javascript function to your template and does not supply a Send button. You will need to include this piece of code only once to your page, it will work for all Send buttons.

```twig
    {% include 'KunstmaanVotingBundle:Facebook:send-callback.html.twig' %}
```

To override the value of the Sends, simply add the value parameter to your include :

```twig
    {% include 'KunstmaanVotingBundle:Facebook:send-callback.html.twig' with {'value' : value} %}
```

#### Helper

The FacebookSendHelper class provides methods to retrieve the sends by reference. It's also possible to retrieve the count or the combined value of the sends.

Name of the service : "kunstmaan_voting.helper.facebook.send"

## LinkedIn

### LinkedIn Share

Add the LinkedIn Share button to your page : [LinkedIn plugins](http://developer.linkedin.com/plugins)

Add the 'data-onsuccess="linkedInShareCallback"' attribute to your share tag.

```HTML
    <script type="IN/Share" data-url="http://www.linkedin.com" data-onsuccess="linkedInShareCallback""></script>
```

The LinkedIn Share callback is triggered when a user completes the share action in the LinkedIn dialog window. When that happens, the javascript function "linkedInShareCallback()" will be called.
The function will then start an event through an AJAX call to the VotingController.

The following information is saved when such an event has been triggered :

#### Data

The field "Reference" will hold the URL which has been shared.

#### Route

The route "/voting-linkedinshare" is being used for the LinkedIn Share callback AJAX request.

#### Snippet

Add the following code to your template. This only adds the javascript function from "data-onsuccess" to your template and does not supply a Share button.
You will need to include this piece of code only once to your page, it will work for all Share buttons.

```twig
    {% include 'KunstmaanVotingBundle:LinkedIn:share-callback.html.twig' %}
```

To override the value of the Shares, simply add the value parameter to your include :

```twig
    {% include 'KunstmaanVotingBundle:LinkedIn:share-callback.html.twig' with {'value' : value} %}
```

#### Helper

The LinkedInShareHelper class provides methods to retrieve the shares by reference. It's also possible to retrieve the count or the combined value of the shares.

Name of the service : "kunstmaan_voting.helper.linkedin.share"