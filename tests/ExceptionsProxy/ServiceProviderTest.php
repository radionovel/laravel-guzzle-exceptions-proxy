<?php
declare(strict_types=1);


namespace ExceptionsProxy\Tests;

use ExceptionsProxy\Tests\Exceptions\MutedException;
use ExceptionsProxy\Tests\Exceptions\WrapException;
use ExceptionsProxy\Tests\Exceptions\WrappedException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ServiceProviderTest extends TestCase
{
    public function testReportingException()
    {
        Log::spy()->expects('error')->once();
        report(new \RuntimeException());
    }

    public function testDontReportException()
    {
        Log::spy()->expects('error')->never();
        report(new MutedException());
    }

    public function testWrappedException()
    {
        $this->expectException(WrappedException::class);

        /** @var ExceptionHandler $handler */
        $handler = $this->app->get(ExceptionHandler::class);
        $handler->render(new Request(), new WrapException());
    }

    public function testNotWrappedException()
    {
        /** @var ExceptionHandler $handler */
        $handler = $this->app->get(ExceptionHandler::class);
        $response = $handler->render(new Request(), new \RuntimeException());
        $this->assertInstanceOf(Response::class, $response);
    }
}
