<?php

namespace AppBundle\Tests\Checker;

use AppBundle\Checker\AnswerChecker;
use AppBundle\Entity\TestQuiz;
use AppBundle\Tests\Faker;
use PHPUnit\Framework\TestCase;

class AnswerCheckerTest extends TestCase
{
    private $quiz = [
        'foo' => [
            'bar' => false,
            'acme' => false,
            'goo' => true,
        ],
        'bar' => [
            'lamp' => true,
            'test' => false,
            'foo' => false,
        ],
    ];

    public function testCheckAnswers()
    {
        /** @var TestQuiz $quiz */
        $quiz = $this->initializeTest()[0];

        $this->assertFalse($quiz->getQuestions()[0]->isCorrect());
        $this->assertTrue($quiz->getQuestions()[1]->isCorrect());
    }

    public function testGetCorrectAnswersCount()
    {
        /** @var AnswerChecker $checker */
        list($quiz, $checker) = $this->initializeTest();

        $this->assertEquals(1, $checker->getCorrectAnswersCount($quiz));
    }

    public function testGetPercentage()
    {
        /** @var AnswerChecker $checker */
        list($quiz, $checker) = $this->initializeTest();

        $this->assertEquals(50.00, $checker->getPercentage($quiz));
    }

    private function initializeTest()
    {
        $faker = new Faker();
        $quiz = $faker->getTestQuiz('foo', $this->quiz);
        $checker = new AnswerChecker();
        $this->checkHalfCorrectValues($quiz);
        $checker->checkAnswers($quiz);

        return [$quiz, $checker];
    }

    private function checkHalfCorrectValues(TestQuiz $quiz)
    {
        $quiz->getQuestions()[0]->getAnswers()[0]->setChecked(true);
        $quiz->getQuestions()[1]->getAnswers()[0]->setChecked(true);
    }
}
