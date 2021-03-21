<?php

namespace Kunstmaan\AdminBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictions;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

abstract class BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $username;

    /**
     * Next Major: Remove attribute
     *
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $usernameCanonical;

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
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $email;

    /**
     * Next Major: Remove attribute
     *
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $emailCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=100, nullable=true)
     */
    protected $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    protected $roles;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

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
        $this->groups = new ArrayCollection();
        $this->roles = [];
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
     * {@inheritdoc}
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames()
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * @deprecated since KunstmaanAdminBundle 5.9
     */
    public function getUsernameCanonical()
    {
        // NEXT_MAJOR remove method
        @trigger_error(sprintf('Using method %s from class %s is deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.', __METHOD__, BaseUser::class), E_USER_DEPRECATED);

        return $this->usernameCanonical;
    }

    /**
     * @deprecated since KunstmaanAdminBundle 5.9
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        // NEXT_MAJOR remove method
        @trigger_error(sprintf('Using method %s from class %s is deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.', __METHOD__, BaseUser::class), E_USER_DEPRECATED);

        $this->usernameCanonical = $usernameCanonical;
    }

    /**
     * @deprecated since KunstmaanAdminBundle 5.9
     */
    public function getEmailCanonical()
    {
        // NEXT_MAJOR remove method
        @trigger_error(sprintf('Using method %s from class %s is deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.', __METHOD__, BaseUser::class), E_USER_DEPRECATED);

        return $this->emailCanonical;
    }

    /**
     * @deprecated since KunstmaanAdminBundle 5.9
     */
    public function setEmailCanonical($emailCanonical)
    {
        // NEXT_MAJOR remove method
        @trigger_error(sprintf('Using method %s from class %s is deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.', __METHOD__, BaseUser::class), E_USER_DEPRECATED);

        $this->emailCanonical = $emailCanonical;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    /**
     * Sets the super admin status.
     *
     * @param bool $boolean
     *
     * @return static
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(self::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(self::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function setPasswordRequestedAt(DateTime $date = null)
    {
        //TODO: check if this propery is usefull?
        // NEXT_MAJOR remove method
        @trigger_error(sprintf('Using method %s from class %s is deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.', __METHOD__, BaseUser::class), E_USER_DEPRECATED);
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTime $lastLogin = null)
    {
        $this->lastLogin = $lastLogin;

        return $this;
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

    /**
     * NEXT_MAJOR remove method
     *
     * @deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * NEXT_MAJOR remove method
     *
     * @deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * NEXT_MAJOR remove method
     *
     * @deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0.
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->password,
            $this->salt,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        [
            $this->password,
            $this->salt,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
        ] = $data;
    }
}
