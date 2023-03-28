<?php
declare(strict_types=1);


namespace ExceptionsProxy\Tests\Exceptions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GuzzleProxyException extends HttpException
{
    public function __construct(ClientException $exception, Request $request)
    {
        parent::__construct($exception->getCode(), $exception->getMessage());
    }
}
