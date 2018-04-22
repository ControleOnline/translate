<?php

namespace Language\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Table(name="language", uniqueConstraints={@ORM\UniqueConstraint(name="language", columns={"language"})})
 * @ORM\Entity
 */
class Language
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=10, nullable=false)
     */
    private $language;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean", nullable=false)
     */
    private $locked;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Entity\LanguageCountry", mappedBy="language")
     */
    private $languageCountry;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Entity\TranslateMenu", mappedBy="lang")
     */
    private $translateMenu;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Entity\TranslateMenuPage", mappedBy="lang")
     */
    private $translateMenuPage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->languageCountry = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translateMenu = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translateMenuPage = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set language
     *
     * @param string $language
     * @return Language
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Language
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Add languageCountry
     *
     * @param \Entity\LanguageCountry $languageCountry
     * @return Language
     */
    public function addLanguageCountry(\Entity\LanguageCountry $languageCountry)
    {
        $this->languageCountry[] = $languageCountry;

        return $this;
    }

    /**
     * Remove languageCountry
     *
     * @param \Entity\LanguageCountry $languageCountry
     */
    public function removeLanguageCountry(\Entity\LanguageCountry $languageCountry)
    {
        $this->languageCountry->removeElement($languageCountry);
    }

    /**
     * Get languageCountry
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLanguageCountry()
    {
        return $this->languageCountry;
    }

    /**
     * Add translateMenu
     *
     * @param \Entity\TranslateMenu $translateMenu
     * @return Language
     */
    public function addTranslateMenu(\Entity\TranslateMenu $translateMenu)
    {
        $this->translateMenu[] = $translateMenu;

        return $this;
    }

    /**
     * Remove translateMenu
     *
     * @param \Entity\TranslateMenu $translateMenu
     */
    public function removeTranslateMenu(\Entity\TranslateMenu $translateMenu)
    {
        $this->translateMenu->removeElement($translateMenu);
    }

    /**
     * Get translateMenu
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslateMenu()
    {
        return $this->translateMenu;
    }

    /**
     * Add translateMenuPage
     *
     * @param \Entity\TranslateMenuPage $translateMenuPage
     * @return Language
     */
    public function addTranslateMenuPage(\Entity\TranslateMenuPage $translateMenuPage)
    {
        $this->translateMenuPage[] = $translateMenuPage;

        return $this;
    }

    /**
     * Remove translateMenuPage
     *
     * @param \Entity\TranslateMenuPage $translateMenuPage
     */
    public function removeTranslateMenuPage(\Entity\TranslateMenuPage $translateMenuPage)
    {
        $this->translateMenuPage->removeElement($translateMenuPage);
    }

    /**
     * Get translateMenuPage
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslateMenuPage()
    {
        return $this->translateMenuPage;
    }
}
