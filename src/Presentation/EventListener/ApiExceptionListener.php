<?php

declare(strict_types=1);

namespace App\Presentation\EventListener;

use App\Presentation\Http\ApiResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

// priority between RouterListener (-64) and Symfony's ErrorListener (-128): the latter stops
// propagation once it sets a response, so we must return ours before it runs
#[AsEventListener(event: KernelEvents::EXCEPTION, priority: -100)]
final readonly class ApiExceptionListener
{
    public function __construct(
        #[Autowire('%kernel.debug%')] private bool $debug = false,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $http = $exception instanceof HttpExceptionInterface ? $exception : null;

        $validation = ($http?->getPrevious() ?? $exception);
        if ($validation instanceof ValidationFailedException) {
            $errors = [];
            foreach ($validation->getViolations() as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath() ?: null,
                    'message' => (string) $violation->getMessage(),
                ];
            }

            $event->setResponse(ApiResponse::error(422, $errors));

            return;
        }

        $status = $http?->getStatusCode() ?? 500;
        $message = $http?->getMessage() ?: 'Internal Server Error';
        if ($this->debug && $status >= 500) {
            $message = $exception->getMessage();
        }

        $event->setResponse(ApiResponse::error($status, [['field' => null, 'message' => $message]]));
    }
}
