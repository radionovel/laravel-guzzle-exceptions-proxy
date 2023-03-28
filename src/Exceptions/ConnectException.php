<?php
declare(strict_types=1);


namespace ExceptionsProxy\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ConnectException extends HttpException
{
    public function __construct()
    {
        parent::__construct(500, 'Service connection error');
    }
}