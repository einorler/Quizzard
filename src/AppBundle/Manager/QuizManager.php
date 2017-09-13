<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Quiz;
use AppBundle\Entity\TestQuiz;
use Doctrine\ORM\EntityManagerInterface;

class QuizManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \AppBundle\Doctrine\ORM\QuizRepository
     */
    private $quizRepository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $testQuizRepository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->quizRepository = $em->getRepository(Quiz::class);
        $this->testQuizRepository = $em->getRepository(TestQuiz::class);
    }

    /**
     * @param Quiz $quiz
     *
     * @return array
     */
    public function getQuizStatistics(Quiz $quiz): array
    {
        $statistics = [];
        $tests = $this->testQuizRepository->findBy(['quiz' => $quiz]);
        $statistics['count'] = count($tests);

        if (0 === $statistics['count']) {
            return $statistics;
        }

        $statistics['max'] = 0;
        $statistics['min'] = $tests[0]->getResult();
        $sum = 0;

        foreach ($tests as $test) {
            $sum += $test->getResult();
            $test->getResult() > $statistics['max'] && $statistics['max'] = $test->getResult();
            $test->getResult() < $statistics['min'] && $statistics['min'] = $test->getResult();
        }

        $statistics['average'] = round($sum / $statistics['count'], 2);

        return $statistics;
    }
}
