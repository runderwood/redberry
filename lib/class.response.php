<?php
class Response {

    const OK = '200 OK';
    const CREATED = '201 Created';
    const ACCEPTED = '202 Accepted';
    const MOVED = '301 Moved Permanently';
    const LOCATED = '302 Located';
    const BADREQUEST = '400 Bad Request';
    const UNAUTHORIZED = '401 Unauthorized';
    const FORBIDDEN = '403 Forbidden';
    const NOTFOUND = '404 Not Found';
    const METHODNOTALLOWED = '405 Method Not Allowed';
    const INTERNALSERVERERROR = '500 Internal Server Error';
    const NOTIMPLEMENTED = '501 Not Implemented';

    const HTTPVER = 'HTTP/1.1';
    
    private $headers = array();
    private $body = '';
    private $code = self::OK;
    private $mime_type = 'text/html;charset=utf-8';

    function __construct($body = '', $code = self::OK, $headers = array()) {
        $this->body = $body;
        $this->code = $code;
        $this->setHeader(self::HTTPVER, $this->getCode());
        $this->setHeader('Content-Type', $this->getMimeType());
        if(is_array($headers)) {
            foreach($headers as $k => $v) {
                $this->setHeader($k, $v);
            }
        }
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this->getBody();
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
        $this->setHeader(self::HTTPVER, $code);
        return $this->getCode();
    }

    public function getMimeType() {
        return $this->mime_type;
    }

    public function setMimeType($type = 'text/html;charset=utf-8') {
        $this->mime_type = $type;
        $this->setHeader('Content-Type', $type);
        return $this->getMimeType();
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setHeaders($headers = array()) {
        $this->headers = $headers;
        return $this->getHeaders;
    }

    public function getHeader($key) {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this->getHeader($key);
    }

    private function sendHeaders() {
        foreach($this->getHeaders() as $k => $v) {
            $k = $k == self::HTTPVER ? "$k" : "$k:";
            header("$k $v");
        }
    }

    function __toString() {
        $this->sendHeaders();
        return $this->getBody();
    }
}
?>
