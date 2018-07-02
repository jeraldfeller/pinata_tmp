<?php

namespace App\FaqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\CsrfIntentionTrait;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Faq.
 *
 * @ORM\Table(name="app_faq")
 * @ORM\Entity
 */
class Faq
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
     * @ORM\Column(name="question", type="string", length=255)
     */
    protected $question;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text")
     */
    protected $answer;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="smallint")
     */
    protected $rank;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="faqs")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $category;

    public function __construct()
    {
        $this->rank = 9999;
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
     * Set question.
     *
     * @param string $question
     *
     * @return Faq
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answer.
     *
     * @param string $answer
     *
     * @return Faq
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return Faq
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

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
     * Set category.
     *
     * @param Category $category
     *
     * @return $this
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
