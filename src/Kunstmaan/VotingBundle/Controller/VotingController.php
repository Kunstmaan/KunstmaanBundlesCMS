<?php

namespace Kunstmaan\VotingBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Events;

class VotingController extends Controller
{

    /**
     * @Route("/voting-facebooklike", name="voting_facebooklike")
     * @Template("KunstmaanVotingBundle:Facebook:like-callback")
     */
    public function facebookLikeAction(Request $request)
    {
        $response = $request->get('response');
        $value = $request->get('value');
        $this->get('event_dispatcher')->dispatch(Events::FACEBOOK_LIKE, new FacebookLikeEvent($this->getRequest(), $response, $value));
    }

}