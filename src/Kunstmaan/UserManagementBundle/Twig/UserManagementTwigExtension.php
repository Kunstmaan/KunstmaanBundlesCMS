<?php

namespace Kunstmaan\UserManagementBundle\Twig;

use Kunstmaan\UserManagementBundle\Form\UserDeleteForm;
use Symfony\Component\Form\FormFactory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class UserManagementTwigExtension extends AbstractExtension
{
    /** @var FormFactory */
    private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_user_delete_form', [$this, 'getUserDeleteForm']),
        ];
    }

    public function getUserDeleteForm($item)
    {
        return $this->formFactory->create(UserDeleteForm::class, $item)->createView();
    }
}
