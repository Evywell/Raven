<?php


namespace Raven\Framework\Network;


class ServerBag extends ParameterBag
{

    public function getHeaders()
    {
        $headers = [];
        $contentHeaders = ['CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'];
        foreach ($this->parameters as $key => $parameter) {
            if(strpos($key, 'HTTP_') === 0) {
                $headers[substr($key, 5)] = $parameter;
            } elseif (in_array($key, $contentHeaders)) {
                $headers[$key] = $parameter;
            }
        }
        return $headers;
    }

}