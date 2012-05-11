<?php

namespace Kunstmaan\SearchBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;

use Kunstmaan\AdminBundle\Entity\PageInterface;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Kunstmaan\ViewBundle\Entity\SearchPage;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SearchedFor
 * 
 * @ORM\Entity(repositoryClass="Kunstmaan\SearchBundle\Repository\SearchedForRepository")
 * @ORM\Table(name="searchedfor")
 * @ORM\HasLifecycleCallbacks()
 */
class SearchedFor extends AbstractEntity
{

    /**
     * @ORM\Column(type="string")
     */
    protected $query;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdat;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\ViewBundle\Entity\SearchPage")
     * @ORM\JoinColumn(name="searchpage", referencedColumnName="id")
     */
    protected $searchpage;

    /**
     * @param string        $query      The search query
     * @param PageInterface $searchpage The search page
     */
    public function __construct($query, PageInterface $searchpage)
    {
        $this->setQuery($query);
        $this->setSearchpage($searchpage);
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return PageInterface
     */
    public function getSearchpage()
    {
        return $this->searchpage;
    }

    /**
     * @param PageInterface $searchpage
     */
    public function setSearchpage($searchpage)
    {
        $this->searchpage = $searchpage;
    }

    /**
     * @param DateTime $created
     */
    public function setCreatedAt($created)
    {
        $this->createdat = $created;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdat;
    }
}
