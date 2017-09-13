<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_questions")
 */
class TestQuestion
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * @var TestQuiz
     *
     * @ORM\ManyToOne(targetEntity="TestQuiz")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $correct;

    /**
     * @var Answer[]|ArrayCollection
     */
    private $answers;

    public function __construct(Question $question)
    {
        $this->setQuestion($question);
        $this->setAnswers($question->getAnswers());
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;
    }

    /**
     * @return TestQuiz
     */
    public function getQuiz(): ?TestQuiz
    {
        return $this->quiz;
    }

    /**
     * @param TestQuiz $quiz
     */
    public function setQuiz(?TestQuiz $quiz)
    {
        $this->quiz = $quiz;
    }

    /**
     * @return boolean
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }

    /**
     * @param boolean $correct
     */
    public function setCorrect(bool $correct)
    {
        $this->correct = $correct;
    }

    /**
     * @return Answer[]|ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param Answer[]|ArrayCollection $answers
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }
}
