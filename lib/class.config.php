<?php
class Config {

    private static $store = array();

    public static function set($key, $value) {
        Log::debug(__CLASS__.": Setting $key to $value");
        self::$store[$key] = $value;
        return self::get($key);
    }

    public static function get($key) {
        return isset(self::$store[$key]) ? self::$store[$key] : null;
    }

    public static function load($file) {
        switch($ext = strtolower(substr($file, -3))) {
            case 'xml':
            case 'php':
                return call_user_func(array('self', 'load_'.$ext), $file);
                break;
        }
        return false;
    }

    public static function load_php($file) {
        Log::debug(__CLASS__.": Loading config from $file.");
        if(is_readable($file)) {
            require_once($file);
        }
        return false;
    }

    public static function load_xml($file) {
        Log::debug(__CLASS__.": Loading XML from $file.");
        if(is_readable($file)) {
            $xml = new SimpleXMLElement(file_get_contents($file));
            return self::process_xml($xml);
        }
        return false;
    }

    public static function process_xml($xml) {
        if(!is_object($xml)) {
            try {
                $xml = new SimpleXMLElement($xml);
            } catch(Exception $e) {
                return false;
            }
        }
        foreach($xml->property as $property) {
            Config::set((string)$property['name'], (string)$property);
        }
        return true;
    }

    public static function get_all() {
        return self::$store;
    }
}
?>
