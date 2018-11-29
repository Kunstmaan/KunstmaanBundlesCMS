<?php

namespace Kunstmaan\AdminBundle\Toolbar;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Exception;
use Kunstmaan\AdminBundle\Helper\Toolbar\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionDataCollector extends AbstractDataCollector
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getAccessRoles()
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @return array
     */
    public function collectData()
    {
        $model = $this->em->getRepository(Exception::class)->findExceptionStatistics();
        if (isset($model['cp_all'], $model['cp_sum'])) {
            return [
                'data' => $model,
            ];
        } else {
            return [];
        }
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (false === $this->isEnabled()) {
            $this->data = false;
        } else {
            $this->data = $this->collectData();
        }
    }

    /**
     * Gets the data for template
     *
     * @return array The request events
     */
    public function getTemplateData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kuma_exception';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    public function reset()
    {
        $this->data = [];
    }
}
