<?php

namespace Kunstmaan\FixturesBundle\Provider;

use Kunstmaan\FixturesBundle\Loader\Fixture;

class Node
{
    /**
     * @param string $fixtureId
     * @param array  $fixtures
     *
     * @return \Kunstmaan\NodeBundle\Entity\Node|null
     */
    public function getNode($fixtureId, array $fixtures)
    {
        /** strip @ */
        $fixtureId = substr($fixtureId, 1);

        if (!array_key_exists($fixtureId, $fixtures)) {
            return null;
        }

        /** @var Fixture $fixture */
        $fixture = $fixtures[$fixtureId];
        $additionalEntities = $fixture->getAdditionalEntities();

        if (array_key_exists('rootNode', $additionalEntities)) {
            return $additionalEntities['rootNode'];
        }

        return null;
    }
}
