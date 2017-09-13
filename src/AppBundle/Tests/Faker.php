<?php

namespace AppBundle\Tests;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\TestQuiz;

final class Faker
{
    public function getTestQuiz(string $title, array $questions): TestQuiz
    {
        return new TestQuiz($this->getQuiz($title, $questions));
    }

    public function getQuiz(string $title, array $questions): Quiz
    {
        $quiz = new Quiz();
        $quiz->setTitle($title);

        foreach ($questions as $text => $config) {
            $quiz->addQuestion($this->getQuestion($text, $config));
        }

        return $quiz;
    }

    public function getQuestion($text, $answers): Question
    {
        $question = new Question();
        $question->setText($text);

        foreach ($answers as $text => $correct) {
            $question->addAnswer($this->getAnswer($text, $correct));
        }

        return $question;
    }

    public function getAnswer($text, $correct): Answer
    {
        $answer = new Answer();
        $answer->setText($text);
        $answer->setCorrect($correct);

        return $answer;
    }
}
