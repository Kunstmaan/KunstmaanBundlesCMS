<?php

namespace Kunstmaan\AdminListBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminListBundle\Entity\LockableEntityInterface;
use Kunstmaan\AdminListBundle\Service\EntityVersionLockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EntityLockCheckController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * You can override this method to return the correct entity manager when using multiple databases ...
     *
     * @return \Doctrine\Persistence\ObjectManager|object
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @Route(
     *      "check/{id}/{repository}",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanAdminListBundle_entity_lock_check"
     * )
     *
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
                $message = $this->container->get('translator')->trans('kuma_admin_list.edit.flash.locked', ['%user%' => implode(', ', $user)]);
            }
        } catch (AccessDeniedException $ade) {
        }

        return new JsonResponse(['lock' => $entityIsLocked, 'message' => $message]);
    }

    public static function getSubscribedServices(): array
    {
        return [
                'kunstmaan_entity.admin_entity.entity_version_lock_service' => EntityVersionLockService::class,
                'translator' => TranslatorInterface::class,
            ] + parent::getSubscribedServices();
    }
}
