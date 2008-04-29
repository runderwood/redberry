<?php
class DBDoc {

    const LOAD = 'load';
    const INSERT = 'insert';
    const UPDATE = 'update';
    const DELETE = 'delete';

    private static $valid_callbacks = array('load', 'insert', 'update', 'delete');
    
    function __construct() {
        $args = func_get_args();
        if(isset($args[0])) {
            if(is_object($args[0])) {
                $this->_populateFromStandardClass($args[0]);
            } elseif(is_numeric($args[0])) {
                $this->load($args[0]);
            }
        }
    }

    public static function get() {
        $args = func_get_args();
        $doc_type = array_shift($args);
        $class_name = ucwords($doc_type).'DBDoc';
        Log::debug(__CLASS__.': Getting instance of '.$class_name);
        $reflector = new ReflectionClass($class_name);
        $dbdoc = $reflector->newInstanceArgs($args);
        Log::debug(__CLASS__.': Got new instance: '.print_r($dbdoc, true));
        if($reflector->isInstance($dbdoc)) return $dbdoc;
        else {
            Log::debug(__CLASS__.': Could not get '.$class_name);
            return false;
        }
    }

    private function _populateFromStandardClass($obj) {
        if(is_object($obj)) {
            foreach(get_object_vars($obj) as $k => $v) {
                $this->$k = $v;
            }
        }
        return $obj;
    }

    private function call($op, $args) {
        if(!in_array($op, self::$valid_callbacks) || get_class($this) == 'DBDoc') return false;
        $method = $op.str_replace(__CLASS__, '', get_class($this));
        Log::debug(__CLASS__.'('.get_class($this).'): Calling '.$method);
        return call_user_func_array(array($this, $method), $args);
    }

    private function setCallback($op, $callback) {
        if(in_array($op, self::$valid_callbacks)) $this->{'_'.$op.'_callback'} = $callback;
    }

    public function load() {
        $args = func_get_args();
        if($data = $this->call(self::LOAD, $args)) {
            $this->_populateFromStandardClass($data);
            return $this;
        }
        return false;
    }
    
    public function insert() {
        $args = func_get_args();
        return $this->call(self::INSERT, $args);
    }

    public function update() {
        $args = func_get_args();
        return $this->call(self::UPDATE, $args);
    }

    public function delete() {
        $args = func_get_args();
        return $this->call(self::DELETE, $args);
    }

}
?>
