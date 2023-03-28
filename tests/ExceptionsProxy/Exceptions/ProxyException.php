<?php
declare(strict_types=1);


namespace ExceptionsProxy\Tests\Exceptions;

class ProxyException extends \Exception
{

    public function __construct()
    {
        parent::__construct('proxy exception');
    }
}