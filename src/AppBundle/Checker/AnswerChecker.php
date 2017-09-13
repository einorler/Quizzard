<?php

namespace AppBundle\Checker;

use AppBundle\Entity\TestQuiz;

class AnswerChecker
{
    public function checkAnswers(TestQuiz $quiz)
    {
        foreach ($quiz->getQuestions() as $question) {
            $correct = 0;

            foreach ($question->getAnswers() as $answer) {
                $answer->isCorrect() && $answer->isChecked() && $correct++;
            }

            1 === $correct ? $question->setCorrect(true) : $question->setCorrect(false);
        }
    }

    public function getCorrectAnswersCount(TestQuiz $quiz)
    {
        $correct = 0;

        foreach ($quiz->getQuestions() as $question) {
            $question->isCorrect() && $correct++;
        }

        return $correct;
    }

    public function getPercentage(TestQuiz $quiz)
    {
        return round($this->getCorrectAnswersCount($quiz) / count($quiz->getQuestions()) * 100, 2);
    }
}
