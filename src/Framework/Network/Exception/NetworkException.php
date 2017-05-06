<?php


namespace Raven\Framework\Network\Exception;


use Exception;
use Raven\Framework\RavenFrameworkException;

class NetworkException extends RavenFrameworkException
{

    private $templatePath = "../app/Error/Template";

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = $this->getErrorTemplate($message);
    }

    private function getErrorTemplate($message = "")
    {
        $template = $this->templatePath . DIRECTORY_SEPARATOR . $this->getCode() . "Exception.php";
        if(file_exists($template)) {
            return sprintf(file_get_contents($template), $this->getCode(), $message);
        }
        return $this->getMessage();
    }

}