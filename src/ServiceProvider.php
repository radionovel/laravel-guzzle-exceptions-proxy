<?php
declare(strict_types=1);


namespace GuzzleExceptionsProxy;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\HttpFoundation\Request;


class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function register(): void
    {
        /** @var \Illuminate\Contracts\Debug\ExceptionHandler $handler */
        $handler = $this->app->get(ExceptionHandler::class);

        $this->muteClientException($handler);
        $this->makeRenderableClientException($handler);
    }

    /**
     * @param $handler
     *
     * @return void
     */
    private function muteClientException($handler): void
    {
        if (method_exists($handler, 'ignore')) {
            $handler->ignore(ClientException::class);
        }
    }

    /**
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $handler
     *
     * @return void
     */
    private function makeRenderableClientException(ExceptionHandler $handler): void
    {
        if (method_exists($handler, 'renderable')) {
            $handler->renderable(function (ClientException $exception, Request $request) {
                return $this->clientExceptionToResponse($exception);
            });
        }
    }

    /**
     * @param  \GuzzleHttp\Exception\ClientException  $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function clientExceptionToResponse(ClientException $exception): JsonResponse
    {
        $response = $exception->getResponse();
        return new JsonResponse(
            $response->getBody()->getContents(),
            $response->getStatusCode(),
            $response->getHeaders(),
            0,
            true
        );
    }
}