<?php

namespace AppBundle\Doctrine\ORM;

use AppBundle\Entity\Quiz;
use AppBundle\Entity\TestQuiz;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use FOS\UserBundle\Model\UserInterface;

class QuizRepository extends EntityRepository
{
    /**
     * @param UserInterface $user
     *
     * @return Quiz[]
     */
    public function getTakenQuizzesByUser(UserInterface $user)
    {
        return $this
            ->createQueryBuilder('q')
            ->join(TestQuiz::class, 'tq', Join::WITH, 'q.id = tq.quiz')
            ->andWhere('tq.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
