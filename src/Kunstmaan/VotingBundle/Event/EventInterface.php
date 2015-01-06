<?php
namespace Kunstmaan\VotingBundle\Event;


interface EventInterface {

    public function getRequest();

    public function getReference();

    public function getValue();
}
