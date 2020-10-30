<?php

namespace Kunstmaan\VotingBundle\Controller;

use Kunstmaan\VotingBundle\Event\Events;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VotingController extends Controller
{
    /**
     * @Route("/voting-upvote", name="voting_upvote")
     * @Template("@KunstmaanVoting/UpDown/voted.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function downVoteAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->dispatch(new DownVoteEvent($request, $reference, $value), Events::VOTE_DOWN);
    }

    /**
     * @Route("/voting-facebooklike", name="voting_facebooklike")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function facebookLikeAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->dispatch(new FacebookLikeEvent($request, $response, $value), Events::FACEBOOK_LIKE);
    }

    /**
     * @Route("/voting-facebooksend", name="voting_facebooksend")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function facebookSendAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->dispatch(new FacebookSendEvent($request, $response, $value), Events::FACEBOOK_SEND);
    }

    /**
     * @Route("/voting-linkedinshare", name="voting_linkedinshare")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function linkedInShareAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->dispatch(new LinkedInShareEvent($request, $reference, $value), Events::LINKEDIN_SHARE);
    }

    /**
     * @param object $event
     * @param string $eventName
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        $eventDispatcher = $this->container->get('event_dispatcher');
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $eventDispatcher->dispatch($eventName, $event);
    }
}
