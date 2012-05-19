<?php
namespace Kunstmaan\SearchBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * IndexableInterface
 */
interface IndexableInterface
{

    /**
     * @param ContainerInterface $container The container
     * @param object             $entity    The object to index
     */
    function getContentForIndexing(ContainerInterface $container, $entity);
}
