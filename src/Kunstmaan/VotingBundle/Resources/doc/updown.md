# Up & Down vote

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