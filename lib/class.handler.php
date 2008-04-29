<?php
class Handler {

    private $request;
    
    function __construct($request) {
        $this->request = $request;
    }

    public static function get($handler = false, $request = false) {
        Log::debug(__CLASS__.': Getting handler "'.$handler.'"');
        $handler = $handler === false || !$handler ? 'Handler' : $handler;
        $request = $request === false ? new Request() : $request;
        if($h = new $handler($request)) return $h;
        else return new Handler($request);
    }

    public function go() {
        $handler_method = 'do'.ucwords($this->getRequest()->getMethod());
        return $this->$handler_method();
    }

    public function getRequest() {
        return $this->request;
    }

    public function doGet() {
       return new Response('Not Found'."\n", Response::NOTFOUND); 
    }

    public function doPost() {
        return new Response('Not Found'."\n", Response::NOTFOUND);
    }

    public function doPut() {
        return new Response('Not Found'."\n", Response::NOTFOUND);
    }

    public function doDelete() {
        return new Response('Not Found'."\n", Response::NOTFOUND);
    }
}
?>
