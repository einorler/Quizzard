<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Quiz;
use AppBundle\Entity\TestQuiz;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;

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
     * @param UserInterface|null $user
     *
     * @return array
     */
    public function getQuizStatistics(Quiz $quiz, UserInterface $user = null): array
    {
        $statistics = [
            'count' => 0,
            'max' => 0,
            'min' => 0,
            'average' => 0,
        ];
        $findBy = ['quiz' => $quiz];

        if (null !== $user) {
            $findBy['user'] = $user;
        }

        $tests = $this->testQuizRepository->findBy($findBy);

        if (0 === count($tests)) {
            return $statistics;
        }

        $statistics['count'] = count($tests);
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
