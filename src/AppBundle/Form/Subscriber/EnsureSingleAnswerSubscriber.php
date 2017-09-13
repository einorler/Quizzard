<?php

namespace AppBundle\Form\Subscriber;

use AppBundle\Entity\Quiz;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EnsureSingleAnswerSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::SUBMIT => 'onSubmit'];
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        /** @var Quiz $quiz */
        $quiz = $event->getData();

        if (0 === count($quiz->getQuestions())) {
            $event->getForm()->addError(
                new FormError('There must be at least one question in your quiz')
            );

            return;
        }

        foreach ($quiz->getQuestions() as $question) {
            $correct = 0;

            foreach ($question->getAnswers() as $answer) {
                $answer->isCorrect() && $correct++;
            }

            if (1 !== $correct) {
                $event->getForm()->addError(
                    new FormError('There must be one correct answer in every question')
                );

                return;
            }
        }
    }
}
