<?php

namespace Kunstmaan\VotingBundle\Controller;

use Kunstmaan\VotingBundle\Event\Events;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as LegacyEventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class VotingController
{
    /** @var LegacyEventDispatcherInterface|EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct($eventDispatcher)
    {
        // NEXT_MAJOR Add "Symfony\Contracts\EventDispatcher\EventDispatcherInterface" typehint when sf <4.4 support is removed.
        if (!$eventDispatcher instanceof EventDispatcherInterface && !$eventDispatcher instanceof LegacyEventDispatcherInterface) {
            throw new \InvalidArgumentException(sprintf('The "$eventDispatcher" parameter should be instance of "%s" or "%s"', EventDispatcherInterface::class, LegacyEventDispatcherInterface::class));
        }

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/voting-upvote", name="voting_upvote")
     * @Template("@KunstmaanVoting/UpDown/voted.html.twig")
     */
    public function upVoteAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->dispatch(new UpVoteEvent($request, $reference, $value), Events::VOTE_UP);
    }

    /**
     * @Route("/voting-downvote", name="voting_downvote")
     * @Template("@KunstmaanVoting/UpDown/voted.html.twig")
     */
    public function downVoteAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->dispatch(new DownVoteEvent($request, $reference, $value), Events::VOTE_DOWN);
    }

    /**
     * @Route("/voting-facebooklike", name="voting_facebooklike")
     */
    public function facebookLikeAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->dispatch(new FacebookLikeEvent($request, $response, $value), Events::FACEBOOK_LIKE);
    }

    /**
     * @Route("/voting-facebooksend", name="voting_facebooksend")
     */
    public function facebookSendAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->dispatch(new FacebookSendEvent($request, $response, $value), Events::FACEBOOK_SEND);
    }

    /**
     * @Route("/voting-linkedinshare", name="voting_linkedinshare")
     */
    public function linkedInShareAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->dispatch(new LinkedInShareEvent($request, $reference, $value), Events::LINKEDIN_SHARE);
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($this->eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }
}
