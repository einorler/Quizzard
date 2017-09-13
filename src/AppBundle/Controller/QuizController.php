<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Quiz;
use AppBundle\Form\QuizType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuizController extends Controller
{

    /**
     * @Route("/quiz/new", name="quiz_create")
     */
    public function createAction(Request $request)
    {
        $quiz = new Quiz();

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
        $isSubmitted = $this->handleSubmission($quiz, $form, 'create');

        return $isSubmitted ? $this->redirectToRoute('homepage') : $this->render(':Quiz:create.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
            'action' => 'Create'
        ]);
    }

    /**
     * @Route("/quiz/edit/{id}", name="quiz_update")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $quiz = $em->getRepository(Quiz::class)->find($id);

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);
        $isSubmitted = $this->handleSubmission($quiz, $form, 'update');

        return $isSubmitted ? $this->redirectToRoute('homepage') : $this->render(':Quiz:create.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
            'action' => 'Update'
        ]);
    }

    private function handleSubmission(Quiz $quiz, Form $form, string $operation): bool
    {
        $em = $this->get('doctrine.orm.entity_manager');

        if ($form->isSubmitted() && $form->isValid()) {
            $quiz->setCreatedBy($this->getUser());
            $em->persist($quiz);
            $em->flush();
            $this->addFlash('success', "Quiz {$operation}d successfully.");

            return true;
        }

        return false;
    }
}
