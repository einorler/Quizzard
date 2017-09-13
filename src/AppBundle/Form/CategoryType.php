<?php

namespace AppBundle\Form;

use AppBundle\Form\Subscriber\CategorySubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    private $categorySubscriber;

    public function __construct(CategorySubscriber $categorySubscriber)
    {
        $this->categorySubscriber = $categorySubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, [
                'label' => 'Title',
                'required' => true,
                'constraints' => [new NotBlank(), new Length(['min' => 5])],
            ])
            ->add('parent', EntityType::class, [
                'label' => 'Parent',
                'class' => 'AppBundle\Entity\Category',
                'choice_label' => 'text',
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
        ;

        $builder->addEventSubscriber($this->categorySubscriber);
    }
}
