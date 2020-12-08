<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as AbstractUser;
use Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictions;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

abstract class BaseUser extends AbstractUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The doctrine metadata is set dynamically in Kunstmaan\AdminBundle\EventListener\MappingListener
     */
    protected $groups;

    /**
     * @ORM\Column(type="string", name="admin_locale", length=5, nullable=true)
     */
    protected $adminLocale;

    /**
     * @ORM\Column(type="boolean", name="password_changed", nullable=true)
     */
    protected $passwordChanged;

    /**
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    protected $googleId;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=true)
     */
    protected $createdAt;

    /**
     * @var string|null
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    protected $createdBy;

    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return BaseUser
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the groupIds for the user.
     *
     * @return array
     */
    public function getGroupIds()
    {
        $groups = $this->groups;

        $groupIds = [];
        if (\count($groups) > 0) {
            /* @var $group GroupInterface */
            foreach ($groups as $group) {
                $groupIds[] = $group->getId();
            }
        }

        return $groupIds;
    }

    /**
     * Gets the groups the user belongs to.
     *
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Get adminLocale
     *
     * @return string
     */
    public function getAdminLocale()
    {
        return $this->adminLocale;
    }

    /**
     * Set adminLocale
     *
     * @param string $adminLocale
     *
     * @return BaseUser
     */
    public function setAdminLocale($adminLocale)
    {
        $this->adminLocale = $adminLocale;

        return $this;
    }

    /**
     * is passwordChanged
     *
     * @return bool
     */
    public function isPasswordChanged()
    {
        return $this->passwordChanged;
    }

    /**
     * Set passwordChanged
     *
     * @param bool $passwordChanged
     *
     * @return User
     */
    public function setPasswordChanged($passwordChanged)
    {
        $this->passwordChanged = $passwordChanged;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param mixed $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('username', new NotBlank());
        $metadata->addPropertyConstraints(
            'plainPassword',
            [
                new NotBlank(['groups' => ['Registration']]),
                new PasswordRestrictions(['groups' => ['Registration', 'Default']]),
            ]
        );
        $metadata->addPropertyConstraint('email', new NotBlank());
        $metadata->addPropertyConstraint('email', new Email());
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'username',
            'message' => 'errors.user.loginexists',
        ]));
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'email',
            'message' => 'errors.user.emailexists',
        ]));
    }

    /**
     * Return class name of form type used to add & edit users
     *
     * @return string
     */
    abstract public function getFormTypeClass();

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->isEnabled();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}
