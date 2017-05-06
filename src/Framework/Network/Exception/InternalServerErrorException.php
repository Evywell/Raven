<?php


namespace Raven\Framework\Network\Exception;


use Exception;

class InternalServerErrorException extends NetworkException
{

    public function __construct($message = "Internal Server Error", $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}