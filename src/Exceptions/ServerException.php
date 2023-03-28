<?php
declare(strict_types=1);


namespace ExceptionsProxy\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ServerException extends HttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Initial service error');
    }
}