<?php

namespace AppBundle\Form;

use AppBundle\Form\Subscriber\EnsureSingleAnswerSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuizType extends AbstractType
{
    /**
     * @var EnsureSingleAnswerSubscriber
     */
    private $ensureSingleAnswerSubscriber;

    public function __construct(EnsureSingleAnswerSubscriber $ensureSingleAnswerSubscriber)
    {
        $this->ensureSingleAnswerSubscriber = $ensureSingleAnswerSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => true,
                'constraints' => [new NotBlank(), new Length(['min' => 5])],
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Categories',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.left', 'ASC');
                },
                'class' => 'AppBundle\Entity\Category',
                'multiple' => true,
                'choice_label' => 'text',
                'required' => false,
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => QuestionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'block_name' => 'question_block',
                'required' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
                'by_reference' => false,
            ])
            ->add('submit', SubmitType::class)
        ;

        $builder->addEventSubscriber($this->ensureSingleAnswerSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Quiz',
        ));
    }
}
