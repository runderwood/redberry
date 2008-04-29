<?php
class PathManager {

    const STORE_SIMPLE = 'simple';
    const STORE_FILE = 'file';
    const STORE_DB = 'db';
    
    private static $path_store = self::STORE_FILE;

    private static $paths = array();

    public static function loadPaths() {
        
        return call_user_func(array('self', 'loadPathsFrom'.ucwords(self::$path_store)));
    }

    public static function loadPathsFromFile() {
        if(is_readable(Config::get('paths_file'))) {
            Log::debug(__CLASS__.': Loading paths from XML in '.Config::get('paths_file'));
            $paths_xml = new SimpleXMLElement(file_get_contents(Config::get('paths_file')));
            foreach($paths_xml->path as $path) { 
                self::addPath((string)$path['name'], (string)$path->handler['name']);
            }
            return true;
        } else {
            Log::debug(__CLASS__.': Could not load XML '.Config::get('paths_file'));
        }
        return false;
    }

    public static function findHandler($uri) {
        $uri_tokens = Request::parse_uri($uri);
        $test_path = '/'.implode('/', $uri_tokens);
        while($test_path !== '') {
            Log::debug(__CLASS__.": Testing $uri against $test_path...");
            if(!isset(self::$paths[$test_path])) $test_path = strstr(substr($test_path, 1), '/') ? substr($test_path, 0, strrpos($test_path, '/')) : '/';
            else return self::$paths[$test_path]['handler'];
            if($test_path === '/') $test_path = '';
        }
        Log::debug(__CLASS__.": No handler found for uri $uri");
        return false;
    }

    public static function setPaths($paths) {
        if(is_array($paths)) self::$paths = $paths;
        return self::$paths;
    }

    public static function addPath($path, $handler) {
        self::$paths[$path] = array('handler' => $handler);
    }

    public static function getPaths() {
        return self::$paths;
    }
}
?>
