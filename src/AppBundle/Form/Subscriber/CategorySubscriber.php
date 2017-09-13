<?php

namespace AppBundle\Form\Subscriber;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CategorySubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::SUBMIT => 'onSubmit'];
    }

    public function onSubmit(FormEvent $event)
    {
        /** @var Category $category */
        $category = $event->getData();
        $list = $this->em->getRepository(Category::class)->findBy([], ['left' => 'ASC']);

        if (null === $parent = $category->getParent()) {
            $this->configureAsLast($category, $list);

            return;
        }

        $this->insertToTree($parent, $category, $list);
    }

    private function configureAsLast(Category $category, $list)
    {
        $this->setCategoryValues($category, ['Level' => 0, 'Left' => 1, 'Right' => 2]);

        if (isset($list[0])) {
            $lastRight = $this->findHighestRight($list);
            $category->setLeft($lastRight + 1);
            $category->setRight($lastRight + 2);
        }
    }

    /**
     * @param Category   $parent
     * @param Category   $category
     * @param Category[] $list
     */
    private function insertToTree($parent, $category, $list)
    {
        $parentLeft = $parent->getLeft();
        $values = ['Level' => $parent->getLevel() + 1, 'Left' => $parentLeft + 1, 'Right' => $parentLeft + 2];
        $this->setCategoryValues($category, $values);
        $this->reorganizeTreeAfterInsertion($category, $list, $parentLeft);
    }

    /**
     * @param Category[] $list
     * @return int
     */
    private function findHighestRight($list): int
    {
        $highest = 0;

        foreach ($list as $category) {
            $category->getRight() > $highest && $highest = $category->getRight();
        }

        return $highest;
    }

    /**
     * @param Category   $category
     * @param Category[] $list
     * @param int        $parentLeft
     */
    private function reorganizeTreeAfterInsertion($category, $list, $parentLeft)
    {
        foreach ($list as $instance) {
            $this->handleLeftInsertion($instance, $parentLeft);
            $this->handleRightInsertion($category, $instance);
            $this->em->persist($instance);
        }
    }

    private function handleLeftInsertion(Category $instance, int $parentLeft)
    {
        if ($instance->getLeft() > $parentLeft) {
            $instance->setLeft($instance->getLeft() + 2);
        }
    }

    private function handleRightInsertion(Category $category, Category $instance)
    {
        if ($instance->getRight() >= $category->getRight() - 1) {
            $instance->setRight($instance->getRight() + 2);
        }
    }

    private function setCategoryValues(Category $category, array $values)
    {
        foreach ($values as $key => $value) {
            $category->{"set$key"}($value);
        }
    }
}
