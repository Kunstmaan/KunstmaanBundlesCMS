<?php

namespace Kunstmaan\VotingBundle\Event;

final class Events
{
    /**
     * The onFacebookLike will be triggered through a callback from the Facebook API when a Like has been registered
     *
     * @var string
     */
    public const FACEBOOK_LIKE = 'kunstmaan_voting.facebookLike';

    /**
     * The onFacebookSend will be triggered through a callback from the Facebook API when a Send has been registered
     *
     * @var string
     */
    public const FACEBOOK_SEND = 'kunstmaan_voting.facebookSend';

    /**
     * The onLinkedInShare will be triggered through a callback from the LinkedIn Javascript API when a Share has been completed
     *
     * @var string
     */
    public const LINKEDIN_SHARE = 'kunstmaan_voting.linkedInShare';

    public const VOTE_UP = 'kunstmaan_voting.upVote';

    public const VOTE_DOWN = 'kunstmaan_voting.downVote';
}
