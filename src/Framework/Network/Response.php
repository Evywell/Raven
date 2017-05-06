<?php


namespace Raven\Framework\Network;


class Response
{

    /**
     * @var string
     */
    private $content;
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var array
     */
    private $headers;
    /**
     * @var string
     */
    private $protocolVersion;

    /**
     * StatusText from Symfony\Component\HttpFoundation\Response
     * @var array
     */
    public static $statusText = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];

    public static $protocolAvailable = ['1.0', '1.1'];

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = new HeaderBag($headers);
        $this->protocolVersion = '1.1';
    }

    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * @return $this
     */
    public function sendHeaders()
    {
        // Headers already sent
        if(headers_sent()) {
            return $this;
        }

        foreach ($this->headers->all() as $key => $value) {
            header(sprintf("%s: %s", $key, $value), false, $this->statusCode);
        }

        header(sprintf('HTTP/%s %s %s', $this->protocolVersion, $this->statusCode, $this->getStatusText($this->statusCode)), true, $this->statusCode);

        return $this;
    }

    /**
     * @return $this
     */
    public function sendContent()
    {
        echo $this->content;
        return $this;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $protocolVersion
     * @return $this
     */
    public function setProtocolVersion(string $protocolVersion)
    {
        if($this->checkProtocol($protocolVersion)) {
            $this->protocolVersion = $protocolVersion;
        }
        return $this;
    }

    /**
     * @param int $statusCode
     * @return Response
     */
    public function setStatusCode(int $statusCode): Response
    {
        if($this->checkStatus($statusCode)) {
            $this->statusCode = $statusCode;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param string $content
     * @return Response
     */
    public function setContent(string $content): Response
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param int $code
     * @return bool|mixed
     */
    public function getStatusText(int $code)
    {
        if(!$this->checkStatus($code)) {
            return false;
        }
        return self::$statusText[$code];
    }

    /**
     * Check the status code validity
     * @param int $code
     * @return bool
     */
    private function checkStatus(int $code)
    {
        if(!array_key_exists($code, self::$statusText)) {
            throw new \InvalidArgumentException(sprintf("The code does not exist. Valid status code are : %s", implode(', ', array_keys(self::$statusText))));
        }
        return true;
    }

    private function checkProtocol(string $protocolVersion) {
        if(!array_key_exists($protocolVersion, self::$protocolAvailable)) {
            throw new \InvalidArgumentException(sprintf("The Protocol Version is not available. Please, us those protocols: %s", implode(', ', self::$protocolAvailable)));
        }
        return true;
    }

    /**
     * @return HeaderBag
     */
    public function getHeaders(): HeaderBag
    {
        return $this->headers;
    }

}