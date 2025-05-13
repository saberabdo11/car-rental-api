<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 400;

        $message = $exception->getMessage() ?: 'Une erreur est survenue.';

        $event->setResponse(new JsonResponse([
            'success' => false,
            'message' => $message,
            'code' => $statusCode,
        ], $statusCode));
    }
}
