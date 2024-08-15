<?php

declare(strict_types=1);

namespace Kunstmaan\MediaBundle\AdminList\FilterType;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\AbstractORMFilterType;
use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Component\HttpFoundation\Request;

class SubFolderFilterType extends AbstractORMFilterType
{
    public function __construct(
        private Folder $folder,
        string $columnName
    ) {
        parent::__construct($columnName);
    }

    public function bindRequest(Request $request, array &$data, $uniqueId): void
    {
        $data['value'] = $request->query->get('filter_value_' . $uniqueId);
    }

    public function apply(array $data, $uniqueId): void
    {
        if (!isset($data['value']) || $data['value'] !== 'true') {
            return;
        }

        $folders = $this->folder->fetchChildIds();
        $folders[] = $this->folder->getId();

        $this->queryBuilder->setParameter('folders', $folders);
    }

    public function getTemplate(): string
    {
        return '@KunstmaanAdminList/FilterType/booleanFilter.html.twig';
    }
}
