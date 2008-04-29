<?php
class Request {

    const REWRITE_ARTIFACT = 'index.php?q=';

    const GET = 'get';
    const POST = 'post';
    const PUT = 'put';
    const DELETE = 'delete';


    private static $implemented_methods = array(
        self::GET,
        self::POST,
        self::PUT,
        self::DELETE
    );

    private $uri = '';
    private $uri_tokens = array();
    private $method = self::GET;
    private $parameters = array();
    private $data = array();

    function __construct($uri = false, $method = false) {
        $this->setUriTokens($uri === false ? self::parse_uri($_SERVER['REQUEST_URI']) : self::parse_uri($uri));
        $this->setUri($uri === false ? $_SERVER['REQUEST_URI'] : $uri);
        $this->setMethod($method === false ? strtolower($_SERVER['REQUEST_METHOD']) : $method);
    }

    public function getUri() {
        return $this->uri;
    }

    public function setUri($uri) {
        $this->uri = $uri;
        return $this->getUri();
    }

    public function getUriTokens() {
        return $this->uri_tokens;
    }

    public function setUriTokens($tokens) {
        $this->uri_tokens = $tokens;
        return $this->getUriTokens();
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method = self::GET) {
        $this->method = in_array($method, self::$implemented_methods) ? $method : self::GET;
        if($this->getMethod() == self::PUT) $this->setParameters(self::getRawParameters());
        if(in_array($this->getMethod(), array(self::GET, self::POST))) {
            $param_superglobal = '_'.strtoupper($this->getMethod());
            $this->setParameters($$param_superglobal);
        }
        return $this->getMethod();
    }

    public function setParameters($params) {
        $this->parameters = $params;
        return $this->getParameters();
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function hasParameter($key = false) {
        $parameters = $this->getParameters();
        if($key !== false && isset($parameters[$key])) return $parameters[$key];
        else return false;
    }

    public function getParameter($key = false) {
        return $this->hasParameter($key);
    }

    static function getRawRequestBody() {
        return file_get_contents('php://input');
    }

    static function getRawParameters() {
        if(!isset($_REQUEST['_PARAMS'])) {
            $input = self::getRawRequestBody();
            $put_params = array();
            $kv_pairs = explode('&', $input);
            if(is_array($kv_pairs)) {
                foreach($kv_pairs as $kv_pair) {
                    list($key, $value) = explode('=', $kv_pair);
                    $params[urldecode($key)] = urldecode($value);
                }
            }
            $_REQUEST['_PARAMS'] = $params;
        }

        return $_REQUEST['_PARAMS'];
    }

    static function parse_uri($uri = '') {
        $uri = str_replace(self::REWRITE_ARTIFACT, '', $uri);
        $tokens = explode('/', $uri);
        $valid_tokens = array();
        foreach($tokens as $token) {
            if($token) $valid_tokens[] = $token;
        }
        return $valid_tokens;
    }
}
?>
