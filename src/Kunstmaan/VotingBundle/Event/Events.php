<?php

namespace Kunstmaan\VotingBundle\Event;

class Events
{

    /**
     * The onFacebookLike will be triggered through a callback from the Facebook API when a Like has been registered
     *
     * @var string
     */
    const FACEBOOK_LIKE = "kunstmaan_voting.facebookLike";

    /**
     * The onFacebookSend will be triggered through a callback from the Facebook API when a Send has been registered
     *
     * @var string
     */
    const FACEBOOK_SEND = "kunstmaan_voting.facebookSend";
}