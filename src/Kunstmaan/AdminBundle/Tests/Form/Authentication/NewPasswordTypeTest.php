<?php

namespace Kunstmaan\AdminBundle\Tests\Form\Authentication;

use Kunstmaan\AdminBundle\Form\Authentication\NewPasswordType;
use Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictions;
use Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictionsValidator;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;

class NewPasswordTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validatorFactory = new class extends ConstraintValidatorFactory {
            public function getInstance(Constraint $constraint): \Symfony\Component\Validator\ConstraintValidatorInterface
            {
                if ($constraint instanceof PasswordRestrictions) {
                    return new PasswordRestrictionsValidator(null, null, null, 8, null);
                }

                return parent::getInstance($constraint);
            }
        };

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory($validatorFactory)
            ->getValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testValidPasswordIsAccepted(): void
    {
        $form = $this->factory->create(NewPasswordType::class);
        $form->submit(['plainPassword' => ['first' => 'LongEnough1!', 'second' => 'LongEnough1!']]);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertSame('LongEnough1!', $form->get('plainPassword')->getData());
    }

    public function testPasswordRestrictionsAreEnforced(): void
    {
        $form = $this->factory->create(NewPasswordType::class);
        $form->submit(['plainPassword' => ['first' => 'short', 'second' => 'short']]);

        $this->assertFalse($form->isValid());
        $this->assertSame(PasswordRestrictions::INVALID_MIN_LENGTH_ERROR, $form->getErrors(true)[0]->getCause()->getCode());
    }

    public function testEmptyPasswordIsRejected(): void
    {
        $form = $this->factory->create(NewPasswordType::class);
        $form->submit(['plainPassword' => ['first' => '', 'second' => '']]);

        $this->assertFalse($form->isValid());
        $this->assertCount(1, $form->getErrors(true));
    }

    public function testNonMatchingPasswordsAreRejected(): void
    {
        $form = $this->factory->create(NewPasswordType::class);
        $form->submit(['plainPassword' => ['first' => 'LongEnough1!', 'second' => 'LongEnough2!']]);

        $this->assertFalse($form->isValid());
    }
}
