<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_quizzes")
 */
class TestQuiz
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Quiz
     *
     * @ORM\ManyToOne(targetEntity="Quiz")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz;

    /**
     * @var TestQuestion[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TestQuestion", mappedBy="quiz", cascade={"persist"})
     */
    private $questions;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $result;

    /**
     * @param Quiz $quiz
     */
    public function __construct(Quiz $quiz)
    {
        $this->questions = new ArrayCollection();

        $this->quiz = $quiz;

        foreach ($quiz->getQuestions() as $question) {
            $this->addQuestion(new TestQuestion($question));
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Quiz
     */
    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    /**
     * @param Quiz $quiz
     */
    public function setQuiz(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    /**
     * @return TestQuestion[]|ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param TestQuestion $question
     * @return TestQuestion[]
     */
    public function addQuestion(TestQuestion $question)
    {
        $this->questions[] = $question;
        $question->setQuiz($this);

        return $this->questions;
    }

    /**
     * @param TestQuestion[] $questions
     */
    public function setQuestions(array $questions)
    {
        foreach ($questions as $question) {
            $this->questions[] = $question;
        }
    }

    /**
     * @param TestQuestion $question
     */
    public function removeQuestion(TestQuestion $question)
    {
        $question->setQuiz(null);
        $this->questions->remove($question);
    }

    /**
     * @return float
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param float $result
     */
    public function setResult(float $result)
    {
        $this->result = $result;
    }
}
