<?php

namespace Kunstmaan\TranslatorBundle\Service\Stasher;

use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * StasherInterface
 * Implement into classes to store translations on specific locations
 */
interface StasherInterface
{

    /**
     * Returns an array of all domains and their locale's from the stash
     * Should look like: array[0] => array('locale' => 'nl', 'name' => 'domainname')
     * @return array
     */
    public function getTranslationDomainsByLocale();

    /**
     * All domain object from stash
     * @return array
     */
    public function getAllDomains();

    /**
     * Get all translationgroups by a given domain
     * @param  string          $domain the name of domain
     * @return ArrayCollection with Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup items
     */
    public function getTranslationGroupsByDomain($domain);

    /**
     * Upgrade stash data from the given TranslationGroups
     * @param  ArrayCollection $groups
     * @return boolean         if success
     */
    public function updateTranslationGroups(ArrayCollection $groups);

    /**
     * Get all translations (in all languages) for a specified keyword/domain
     *
     * @return ArrayCollection of TranslationGroup objects
     **/
    public function getTranslationGroupByKeywordAndDomain($keyword, $domain);

    /**
     * Get an array of translations from a given locale and domain
     * @param  string $locale
     * @param  string $domain
     * @return array
     */
    public function getTranslationsByLocaleAndDomain($locale, $domain);

    /**
     * Creates and returnes the translationDomain by the given name
     *
     * @return \Kunstmaan\TranslatorBundle\Entity\TranslationDomain
     **/
    public function createTranslationDomain($name);

    /**
     * Returns the domain object
     * @param  string                                                         $name
     * @return Kunstmaan\TranslatorBundle\Model\Translation\TranslationDomain
     */
    public function getDomainByName($name);

    /**
     * Persist an entity
     * @param  Object $entity
     * @return Object
     */
    public function persist($entity);

    /**
     * Flush (an entity)
     * @param Object $entity
     */
    public function flush($entity = null);

}
