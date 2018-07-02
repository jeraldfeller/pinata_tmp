<?php

namespace App\FaqBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Category.
 *
 * @ORM\Table(name="app_faq_category")
 * @ORM\Entity(repositoryClass="App\FaqBundle\Repository\CategoryRepository")
 */
class Category
{
    use TimestampableTrait;
    use CsrfIntentionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="smallint")
     */
    protected $rank;

    /**
     * @var Faq[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Faq", mappedBy="category", cascade={"persist"})
     */
    protected $faqs;

    public function __construct()
    {
        $this->rank = 9999;
        $this->faqs = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return Category
     */
    public function setRank($rank)
    {
        $this->rank = (int) $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Add faq.
     *
     * @param Faq $faq
     */
    public function addFaq(Faq $faq)
    {
        if (!$this->faqs->contains($faq)) {
            $faq->setCategory($this);
            $this->faqs->add($faq);
        }
    }

    /**
     * Remove faq.
     *
     * @param Faq $faq
     */
    public function removeFaq(Faq $faq)
    {
        if ($this->faqs->contains($faq)) {
            $faq->setCategory(null);
            $this->faqs->removeElement($faq);
        }

        return $this;
    }

    /**
     * Get faqs.
     *
     * @return Faq[]|ArrayCollection
     */
    public function getFaqs()
    {
        return $this->faqs;
    }
}
