<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Quiz;
use AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="category_index")
     */
    public function indexAction(Request $request)
    {
        return $this->render(':Category:index.html.twig', [
            'categories' => $this
                ->get('doctrine.orm.entity_manager')
                ->getRepository(Category::class)
                ->findBy([], ['left' => 'ASC'])
        ]);
    }

    /**
     * @Route("/categories/new", name="category_create")
     */
    public function createAction(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $isSubmitted = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('doctrine.orm.entity_manager')->persist($category);
            $this->get('doctrine.orm.entity_manager')->flush();
            $this->addFlash('success', "Quiz creation was successfully.");

            $isSubmitted = true;
        }

        return $isSubmitted ? $this->redirectToRoute('category_index') : $this->render(':Category:create.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'action' => 'Create'
        ]);
    }

    /**
     * @Route("/categories/{id}", name="category_show")
     */
    public function showAction(Request $request, $id)
    {
        $category = $this->get('doctrine.orm.entity_manager')->getRepository(Category::class)->find($id);

        if (null === $category) {
            $this->addFlash('error', "Category not found.");

            $this->redirectToRoute('category_index');
        }

        $repository = $this->get('doctrine.orm.entity_manager')->getRepository(Quiz::class);

        $quizzes = $repository
            ->createQueryBuilder('q')
            ->join('q.categories', 'c')
            ->where('c = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();

        return $this->render(':Category:show.html.twig', [
            'category' => $category,
            'quizzes' => $quizzes,
        ]);
    }

}
