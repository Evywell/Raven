<?php


namespace Raven\Framework\Network\Exception;

class ForbiddenException extends NetworkException
{

    public function __construct(string $resource = "/")
    {
        parent::__construct(sprintf("You don't have permission to access %s on this server.", $resource), 403, null);
    }

}