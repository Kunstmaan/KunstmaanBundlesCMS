<?php

namespace Kunstmaan\MediaBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\MediaBundle\AdminList\ItemAction\MediaDeleteItemAction;
use Kunstmaan\MediaBundle\AdminList\ItemAction\MediaEditItemAction;
use Kunstmaan\MediaBundle\AdminList\ItemAction\MediaSelectItemAction;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Kunstmaan\MediaBundle\Helper\RemoteAudio\RemoteAudioHandler;
use Kunstmaan\MediaBundle\Helper\RemoteSlide\RemoteSlideHandler;
use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * The admin list configurator for the Media entity
 */
class MediaAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var Folder
     */
    private $folder;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param EntityManager $em The entity manager
     * @param MediaManager $mediaManager The media manager
     * @param Folder $folder The current folder
     * @param Request $request The request object
     */
    public function __construct(
        EntityManager $em,
        MediaManager $mediaManager,
        Folder $folder,
        Request $request
    )
    {
        parent::__construct($em);

        $this->setAdminType(new MediaType($mediaManager, $em));
        $this->folder = $folder;
        $this->request = $request;
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'Name', true);
        $this->addField('contentType', 'Type', true);
        $this->addField('updatedAt', 'Date', true);
        $this->addField('filesize', 'Filesize', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'Name');
        $this->addFilter('contentType', new ORM\StringFilterType('contentType'), 'Type');
        $this->addFilter('updatedAt', new ORM\NumberFilterType('updatedAt'), 'Date');
        $this->addFilter('filesize', new ORM\NumberFilterType('filesize'), 'Filesize (in bytes)');
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        return array(
            'path' => $this->request->get('_route'),
            'params' => array('folderId' => $this->folder->getId())
        );
    }

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return false;
    }

    /**
     * Configure if it's possible to delete the given $item
     *
     * @param object|array $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return 250;
    }

    /**
     * Add item actions buttons
     */
    public function buildItemActions()
    {
        if ($this->request->get('_route') == 'KunstmaanMediaBundle_chooser_show_folder') {
            $this->addItemAction(new MediaSelectItemAction());
        } else {
            $this->addItemAction(new MediaEditItemAction());
            $this->addItemAction(new MediaDeleteItemAction($this->request->getRequestUri()));
        }
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanMediaBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Media';
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder->andWhere('b.folder = :folder')
            ->setParameter('folder', $this->folder->getId())
            ->andWhere('b.deleted = 0')
            ->orderBy('b.updatedAt', 'DESC');

        if ($this->request->get('_route') == 'KunstmaanMediaBundle_chooser_show_folder') {
            $type = $this->request->query->get('type');
            if ($type) {
                switch ($type) {
                    case 'file':
                        $queryBuilder->andWhere('b.location = :location')
                            ->setParameter('location', 'local');
                        break;
                    case 'image':
                        $queryBuilder->andWhere('b.contentType LIKE :ctype')
                            ->setParameter('ctype', '%image%');
                        break;
                    case RemoteAudioHandler::TYPE:
                        $queryBuilder->andWhere('b.contentType = :ctype')
                            ->setParameter('ctype', RemoteAudioHandler::CONTENT_TYPE);
                        break;
                    case RemoteSlideHandler::TYPE:
                        $queryBuilder->andWhere('b.contentType = :ctype')
                            ->setParameter('ctype', RemoteSlideHandler::CONTENT_TYPE);
                        break;
                    case RemoteVideoHandler::TYPE:
                        $queryBuilder->andWhere('b.contentType = :ctype')
                            ->setParameter('ctype', RemoteVideoHandler::CONTENT_TYPE);
                        break;
                }
            }
        }
    }
}
