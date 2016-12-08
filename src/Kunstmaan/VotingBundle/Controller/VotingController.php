<?php

namespace Kunstmaan\VotingBundle\Controller;

use Kunstmaan\VotingBundle\Event\Events;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;

use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VotingController extends Controller
{

    /**
     * @Route("/voting-upvote", name="voting_upvote")
     * @Template("KunstmaanVotingBundle:UpDown:voted.html.twig")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function upVoteAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::VOTE_UP, new UpVoteEvent($request, $reference, $value));

        return;
    }

    /**
     * @Route("/voting-downvote", name="voting_downvote")
     * @Template("KunstmaanVotingBundle:UpDown:voted.html.twig")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function downVoteAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::VOTE_DOWN, new DownVoteEvent($request, $reference, $value));

        return;
    }

    /**
     * @Route("/voting-facebooklike", name="voting_facebooklike")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function facebookLikeAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::FACEBOOK_LIKE, new FacebookLikeEvent($request, $response, $value));
    }

    /**
     * @Route("/voting-facebooksend", name="voting_facebooksend")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function facebookSendAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::FACEBOOK_SEND, new FacebookSendEvent($request, $response, $value));
    }

    /**
     * @Route("/voting-linkedinshare", name="voting_linkedinshare")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function linkedInShareAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::LINKEDIN_SHARE, new LinkedInShareEvent($request, $reference, $value));
    }

}
