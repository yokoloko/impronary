<?php
namespace ImproBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="ImproBundle\Repository\WordRepository")
 * @ORM\Table(name="word")
 */
class Word
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $definition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Blacklist", mappedBy="word")
     */
    protected $blacklists;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $retired;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->retired = false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Word
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set definition
     *
     * @param string $definition
     *
     * @return Word
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get definition
     *
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Word
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add blacklist
     *
     * @param \ImproBundle\Entity\blacklist $blacklist
     *
     * @return Word
     */
    public function addBlacklist(\ImproBundle\Entity\Blacklist $blacklist)
    {
        $this->blacklists[] = $blacklist;

        return $this;
    }

    /**
     * Remove blacklist
     *
     * @param \ImproBundle\Entity\blacklist $blacklist
     */
    public function removeBlacklist(\ImproBundle\Entity\Blacklist $blacklist)
    {
        $this->blacklists->removeElement($blacklist);
    }

    /**
     * Get blacklists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlacklists()
    {
        return $this->blacklists;
    }

    public function isBlacklisted()
    {
        foreach ($this->blacklists as $blacklist) {
            if ($blacklist->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set retired
     *
     * @param boolean $retired
     *
     * @return Word
     */
    public function setRetired($retired)
    {
        $this->retired = $retired;

        return $this;
    }

    /**
     * Get retired
     *
     * @return boolean
     */
    public function getRetired()
    {
        return $this->retired;
    }
}
