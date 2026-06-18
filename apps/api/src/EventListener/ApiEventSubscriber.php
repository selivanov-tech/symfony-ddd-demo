<?php

namespace Api\EventListener;

use Api\Service\ExceptionTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ExceptionTransformer $exceptionTransformer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [
                ['transformResponseToJson', 0],
            ],
            KernelEvents::EXCEPTION => [
                ['transformExceptionToJson', 10],
                ['logException', 0],
                ['notifyException', -10],
            ],
        ];
    }

    public function transformResponseToJson(ResponseEvent $event): void
    {
        $event->getResponse()->headers->set('Content-Type', 'application/json');
    }

    public function transformExceptionToJson(ExceptionEvent $event): void
    {
        $event->setResponse(
            $this->exceptionTransformer->transform($event->getThrowable())
        );
    }

    public function logException(ExceptionEvent $event): void
    {
        // todo: implement monolog logger
    }

    public function notifyException(ExceptionEvent $event): void
    {
        // todo: send to ... based on the most critical condition ...
    }
}
