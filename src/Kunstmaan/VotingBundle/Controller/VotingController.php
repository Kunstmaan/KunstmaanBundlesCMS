<?php

namespace Kunstmaan\VotingBundle\Controller;

use Kunstmaan\VotingBundle\Event\Events;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class VotingController extends AbstractController
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/voting-upvote", name="voting_upvote")
     */
    public function upVoteAction(Request $request): Response
    {
        $reference = $request->query->get('reference');
        $value = $request->query->get('value');
        $this->eventDispatcher->dispatch(new UpVoteEvent($request, $reference, $value), Events::VOTE_UP);

        return $this->render('@KunstmaanVoting/UpDown/voted.html.twig');
    }

    /**
     * @Route("/voting-downvote", name="voting_downvote")
     */
    public function downVoteAction(Request $request): Response
    {
        $reference = $request->query->get('reference');
        $value = $request->query->get('value');
        $this->eventDispatcher->dispatch(new DownVoteEvent($request, $reference, $value), Events::VOTE_DOWN);

        return $this->render('@KunstmaanVoting/UpDown/voted.html.twig');
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
}
