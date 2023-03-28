<?php
declare(strict_types=1);


namespace ExceptionsProxy;

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
        $this->mergeConfigFrom(__DIR__ . '/config/exceptions.php', 'exceptions');
    }

    public function boot(): void
    {
        /** @var \Illuminate\Contracts\Debug\ExceptionHandler $handler */
        $handler = $this->app->get(ExceptionHandler::class);

        $this->proxyExceptions($handler);
        $this->wrapExceptions($handler);
    }
    
    /**
     * @param $handler
     *
     * @return void
     */
    private function muteProxyExceptions($handler): void
    {
        if (method_exists($handler, 'ignore')) {
            $proxyExceptions = $this->app['config']['exceptions']['dont_report'];
            foreach ($proxyExceptions as $exception) {
                $handler->ignore($exception);
            }
        }
    }

    /**
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $handler
     *
     * @return void
     */
    private function proxyExceptions(ExceptionHandler $handler): void
    {
        $this->muteProxyExceptions($handler);

        if (method_exists($handler, 'renderable')) {
            $handler->renderable(function (\Throwable $exception, Request $request) {
                $proxyExceptions = $this->app['config']['exceptions']['proxy'];
                if (!in_array(get_class($exception), $proxyExceptions)) {
                    return null;
                }

                return $this->exceptionToResponse($exception);
            });
        }
    }

    /**
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $handler
     *
     * @return void
     */
    private function wrapExceptions(ExceptionHandler $handler): void
    {
        if (method_exists($handler, 'renderable')) {
            $handler->renderable(function (\Throwable $exception, Request $request) {
                $wrappers = $this->app['config']['exceptions']['wrapper'];

                foreach ($wrappers as $class => $wrapper) {
                    if (is_a($exception, $class) && class_exists($wrapper)) {
                        throw new $wrapper($exception, $request);
                    }
                }

                return null;
            });
        }
    }

    /**
     * @param  \GuzzleHttp\Exception\ClientException  $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function exceptionToResponse(\Throwable $exception): JsonResponse
    {
        if (method_exists($exception, 'getResponse')) {
            $response = $exception->getResponse();
            return new JsonResponse(
                $response->getBody()->getContents(),
                $response->getStatusCode(),
                $response->getHeaders(),
                0,
                true
            );
        }

        return new JsonResponse(
            ['message' => $exception->getMessage()],
            500
        );
    }
}