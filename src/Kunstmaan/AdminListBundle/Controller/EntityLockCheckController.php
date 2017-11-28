<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\EntityManagerTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\TranslatorTrait;
use Kunstmaan\AdminListBundle\Service\EntityVersionLockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * EntityLockCheckController
 */
class EntityLockCheckController extends AbstractController
{
    use EntityManagerTrait,
        TranslatorTrait;

    /**
     * EntityLockCheckController constructor.
     *
     * @param EntityManagerInterface|null $entityManager
     * @param TranslatorInterface|null    $translator
     */
    public function __construct(EntityManagerInterface $entityManager = null, TranslatorInterface $translator = null)
    {
        $this->setEntityManager($entityManager);
        $this->setTranslator($translator);
    }

    /**
     * @Route(
     *      "check/{id}/{repository}",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanAdminListBundle_entity_lock_check"
     * )
     * @param Request $request
     * @param $id
     * @param $repository
     * @return JsonResponse
     */
    public function checkAction(Request $request, $id, $repository)
    {
        $entityIsLocked = false;
        $message = '';

        /** @var LockableEntityInterface $entity */
        $entity = $this->getEntityManager()->getRepository($repository)->find($id);

        try {
            /** @var EntityVersionLockService $entityVersionLockservice */
            $entityVersionLockService = $this->container->get('kunstmaan_entity.admin_entity.entity_version_lock_service');

            $entityIsLocked = $entityVersionLockService->isEntityLocked($this->getUser(), $entity);

            if ($entityIsLocked) {
                $user = $entityVersionLockService->getUsersWithEntityVersionLock($entity, $this->getUser());
                $message = $this->getTranslator()->trans('kuma_admin_list.edit.flash.locked', array('%user%' => implode(', ', $user)));
            }

        } catch (AccessDeniedException $ade) {}

        return new JsonResponse(['lock' => $entityIsLocked, 'message' => $message]);
    }
}
