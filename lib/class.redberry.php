<?php
class Redberry {
    
    public static function call($handler, $request = false) {
        Log::debug(__CLASS__.": call('$handler', ...')");
        if(class_exists($handler)) {
            $request = is_object($request) ? $request : new Request();
            $h = new $handler($request);
        } else {
            return new Response('NO FIND PAGE, MANG.', Response::NOTFOUND);
        }
    }

    public static function do_404() {
        print new Response('', Response::NOTFOUND);
        exit;
    }
}
?>
