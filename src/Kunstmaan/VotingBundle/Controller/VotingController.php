<?php

namespace Kunstmaan\VotingBundle\Controller;

use Kunstmaan\VotingBundle\Event\Events;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VotingController extends Controller
{
    /**
     * @Route("/voting-upvote", name="voting_upvote")
     * @Template("@KunstmaanVoting/UpDown/voted.html.twig")
     */
    public function upVoteAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::VOTE_UP, new UpVoteEvent($request, $reference, $value));
    }

    /**
     * @Route("/voting-downvote", name="voting_downvote")
     * @Template("@KunstmaanVoting/UpDown/voted.html.twig")
     */
    public function downVoteAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::VOTE_DOWN, new DownVoteEvent($request, $reference, $value));
    }

    /**
     * @Route("/voting-facebooklike", name="voting_facebooklike")
     */
    public function facebookLikeAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::FACEBOOK_LIKE, new FacebookLikeEvent($request, $response, $value));
    }

    /**
     * @Route("/voting-facebooksend", name="voting_facebooksend")
     */
    public function facebookSendAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::FACEBOOK_SEND, new FacebookSendEvent($request, $response, $value));
    }

    /**
     * @Route("/voting-linkedinshare", name="voting_linkedinshare")
     */
    public function linkedInShareAction(Request $request)
    {
        $reference = $request->get('reference');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::LINKEDIN_SHARE, new LinkedInShareEvent($request, $reference, $value));
    }
}
