<?php
declare(strict_types=1);


namespace ExceptionsProxy\Tests\Exceptions;

class WrappedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Test server error');
    }
}