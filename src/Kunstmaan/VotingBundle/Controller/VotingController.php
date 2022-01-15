<?php

namespace Kunstmaan\VotingBundle\Controller;

use Kunstmaan\VotingBundle\Event\Events;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class VotingController
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $this->upgradeEventDispatcher($eventDispatcher);
    }

    /**
     * @Route("/voting-upvote", name="voting_upvote")
     * @Template("@KunstmaanVoting/UpDown/voted.html.twig")
     */
    public function upVoteAction(Request $request)
    {
        $reference = $request->query->get('reference');
        $value = $request->query->get('value');
        $this->eventDispatcher->dispatch(new UpVoteEvent($request, $reference, $value), Events::VOTE_UP);
    }

    /**
     * @Route("/voting-downvote", name="voting_downvote")
     * @Template("@KunstmaanVoting/UpDown/voted.html.twig")
     */
    public function downVoteAction(Request $request)
    {
        $reference = $request->query->get('reference');
        $value = $request->query->get('value');
        $this->eventDispatcher->dispatch(new DownVoteEvent($request, $reference, $value), Events::VOTE_DOWN);
    }

    /**
     * @Route("/voting-facebooklike", name="voting_facebooklike")
     */
    public function facebookLikeAction(Request $request)
    {
        $response = $request->query->get('response');
        $value = $request->query->get('value');
        $this->eventDispatcher->dispatch(new FacebookLikeEvent($request, $response, $value), Events::FACEBOOK_LIKE);
    }

    /**
     * @Route("/voting-facebooksend", name="voting_facebooksend")
     */
    public function facebookSendAction(Request $request)
    {
        $response = $request->query->get('response');
        $value = $request->query->get('value');
        $this->eventDispatcher->dispatch(new FacebookSendEvent($request, $response, $value), Events::FACEBOOK_SEND);
    }

    /**
     * @Route("/voting-linkedinshare", name="voting_linkedinshare")
     */
    public function linkedInShareAction(Request $request)
    {
        $reference = $request->query->get('reference');
        $value = $request->query->get('value');
        $this->eventDispatcher->dispatch(new LinkedInShareEvent($request, $reference, $value), Events::LINKEDIN_SHARE);
    }

    /**
     * NEXT_MAJOR remove when sf4.4 support is dropped.
     */
    private function upgradeEventDispatcher(EventDispatcherInterface $eventDispatcher): EventDispatcherInterface
    {
        // On Symfony 5.0+, the legacy proxy is a no-op and it is deprecated in 5.1+
        // Detecting the parent class of GenericEvent (which changed in 5.0) allows to avoid using the deprecated no-op API.
        if (is_subclass_of(GenericEvent::class, Event::class)) {
            return $eventDispatcher;
        }

        // BC layer for Symfony 4.4 where we need to apply the decorating proxy in case of non-upgraded dispatcher.
        return LegacyEventDispatcherProxy::decorate($eventDispatcher);
    }
}
