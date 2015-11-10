<?php

namespace Kunstmaan\FixturesBundle\Builder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\FixturesBundle\Populator\Populator;
use Kunstmaan\MenuBundle\Entity\BaseMenuItem;

class MenuItemBuilder implements BuilderInterface
{
    public function canBuild(Fixture $fixture)
    {
        if ($fixture->getEntity() instanceof BaseMenuItem) {
            return true;
        }

        return false;
    }

    public function preBuild(Fixture $fixture)
    {
        $parameters = $fixture->getParameters();

        if (isset($parameters['page']) && $parameters['page'] instanceof Fixture) {
            $additionalEntities = $parameters['page']->getAdditionalEntities();
            $properties = $fixture->getProperties();

            if (isset($properties['menu']) &&
                $properties['menu']->getLocale() &&
                isset($additionalEntities['translationNode_' . $properties['menu']->getLocale()])) {
                $fixture->getEntity()->setType(BaseMenuItem::TYPE_PAGE_LINK);
                $fixture->getEntity()->setNodeTranslation($additionalEntities['translationNode_' . $properties['menu']->getLocale()]);
            }
        }

        return;
    }

    public function postBuild(Fixture $fixture)
    {
        return;
    }

    public function postFlushBuild(Fixture $fixture)
    {
        return;
    }
}
