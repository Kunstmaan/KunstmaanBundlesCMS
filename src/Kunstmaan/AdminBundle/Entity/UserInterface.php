<?php

namespace Kunstmaan\AdminBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, \Serializable
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Returns the user unique id.
     */
    public function getId();

    /**
     * @param string $username
     *
     * @return static
     */
    public function setUsername($username);

    /**
     * @param string|null $salt
     *
     * @return static
     */
    public function setSalt($salt);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return static
     */
    public function setEmail($email);

    /**
     * @return string|null
     */
    public function getPlainPassword();

    /**
     * @param string $password
     *
     * @return static
     */
    public function setPlainPassword($password);

    /**
     * Sets the hashed password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPassword($password);

    /**
     * @return bool
     */
    public function isSuperAdmin();

    /**
     * @param bool $boolean
     *
     * @return static
     */
    public function setEnabled($boolean);

    /**
     * @param bool $boolean
     *
     * @return static
     */
    public function setSuperAdmin($boolean);

    /**
     * @return string|null
     */
    public function getConfirmationToken();

    /**
     * @param string|null $confirmationToken
     *
     * @return static
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * Sets the last login time.
     *
     * @return static
     */
    public function setLastLogin(?\DateTime $time = null);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @return static
     */
    public function setRoles(array $roles);

    /**
     * @param string $role
     *
     * @return static
     */
    public function addRole($role);

    /**
     * @param string $role
     *
     * @return static
     */
    public function removeRole($role);

    /**
     * Gets the groups granted to the user.
     *
     * @return \Traversable
     */
    public function getGroups();

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return array
     */
    public function getGroupNames();

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return bool
     */
    public function hasGroup($name);

    /**
     * @return static
     */
    public function addGroup(GroupInterface $group);

    /**
     * @return static
     */
    public function removeGroup(GroupInterface $group);
}
