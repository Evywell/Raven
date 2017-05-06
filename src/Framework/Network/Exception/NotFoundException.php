<?php


namespace Raven\Framework\Network\Exception;


use Exception;

class NotFoundException extends NetworkException
{

    public function __construct($message = "La page demandée n'existe pas", $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}