<?php

namespace AppBundle\Tests\Form;

use AppBundle\Entity\Quiz;
use AppBundle\Form\QuizType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class QuizFormTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $requestParameters = [
        'quiz' => [
            'title' => 'fooname',
            'questions' => [
                [
                    'text' => 'barbar',
                    'answers' => [
                        [
                            'text' => 'answer 1',
                            'correct' => false
                        ],
                        [
                            'text' => 'answer 2',
                            'correct' => true
                        ],
                        [
                            'text' => 'answer 3',
                            'correct' => false
                        ],
                    ],
                ],
                [
                    'text' => 'acmeacme',
                    'answers' => [
                        [
                            'text' => 'answer 1',
                            'correct' => true
                        ],
                        [
                            'text' => 'answer 2',
                            'correct' => false
                        ],
                        [
                            'text' => 'answer 3',
                            'correct' => false
                        ],
                    ],
                ]
            ]
        ]
    ];

    public function testFormPassing()
    {
        $quiz = new Quiz();

        $form = $this->getContainer()->get('form.factory')->create(QuizType::class, $quiz, ['csrf_protection' => false]);
        $request = new Request([], $this->requestParameters);
        $request->setMethod('POST');

        $form->handleRequest($request);
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertEquals($this->requestParameters['quiz']['title'], $quiz->getTitle());
        $this->assertCount(2, $quiz->getQuestions());
    }

    public function testWrongNumberOfCorrectAnswersProvided()
    {
        $quiz = new Quiz();
        $params = $this->requestParameters;
        $params['quiz']['questions'][0]['answers'][0]['correct'] = true;

        $form = $this->getContainer()->get('form.factory')->create(QuizType::class, $quiz, ['csrf_protection' => false]);
        $request = new Request([], $params);
        $request->setMethod('POST');

        $form->handleRequest($request);
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
        $this->assertCount(1, $form->getErrors(true));
    }

    public function testNoAnswersProvided()
    {
        $quiz = new Quiz();
        $params = $this->requestParameters;
        $params['quiz']['questions'][1]['answers'][0]['correct'] = false;

        $form = $this->getContainer()->get('form.factory')->create(QuizType::class, $quiz, ['csrf_protection' => false]);
        $request = new Request([], $params);
        $request->setMethod('POST');

        $form->handleRequest($request);
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
        $this->assertCount(1, $form->getErrors(true));
    }

    protected function getContainer()
    {
        if ($this->container === null) {
            static::bootKernel([]);
            $this->container = static::$kernel->getContainer();
        }

        return $this->container;
    }
}
