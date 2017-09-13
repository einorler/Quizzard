<?php

namespace AppBundle\Controller;

use AppBundle\Checker\AnswerChecker;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\TestQuiz;
use AppBundle\Form\Test\TestQuizType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AppController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('app/index.html.twig');
    }

    /**
     * @Route("/take-quiz/{id}", name="quiz")
     */
    public function quizAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $testQuiz = new TestQuiz($em->getRepository(Quiz::class)->find($id));
        $testQuiz->setUser($this->getUser());

        $form = $this->createForm(TestQuizType::class, $testQuiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $checker = new AnswerChecker();
            $checker->checkAnswers($testQuiz);
            $percentage = $checker->getPercentage($testQuiz);
            $testQuiz->setResult($percentage);
            $em->persist($testQuiz);
            $em->flush();

            $status = $percentage > 50 ? 'success' : 'error';
            $this->addFlash($status, "You have scorred $percentage %!");

            return $this->redirectToRoute('homepage');
        }

        return $this->render('app/quiz.html.twig', [
            'quiz' => $testQuiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/my-quizzes", name="my_quizzes")
     */
    public function myQuizzesAction(Request $request)
    {
        return $this->render('app/my_quizzes.html.twig', []);
    }

    /**
     * @Route("/my-quizzes/taken", name="my_taken_quizzes")
     */
    public function takenQuizzesAction(Request $request)
    {
        $repo = $this->get('doctrine.orm.entity_manager')->getRepository(Quiz::class);
        $quizzes = $repo->getTakenQuizzesByUser($this->getUser());
        $statistics = [];

        foreach ($quizzes as $quiz) {
            $statistics[$quiz->getId()] = $this->get('app.manager.quiz')->getQuizStatistics($quiz, $this->getUser());
        }

        return $this->render('app/taken_quizzes.html.twig', [
            'quizzes' => $quizzes,
            'statistics' => $statistics,
        ]);
    }

    /**
     * @Route("/my-quizzes/created", name="my_created_quizzes")
     */
    public function createdQuizzesAction(Request $request)
    {
        $repo = $this->get('doctrine.orm.entity_manager')->getRepository(Quiz::class);
        $quizzes = $repo->findBy(['createdBy' => $this->getUser()]);
        $statistics = [];

        foreach ($quizzes as $quiz) {
            $statistics[$quiz->getId()] = $this->get('app.manager.quiz')->getQuizStatistics($quiz);
        }

        return $this->render('app/created_quizzes.html.twig', [
            'quizzes' => $quizzes,
            'statistics' => $statistics,
        ]);
    }
}
