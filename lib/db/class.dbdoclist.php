<?php
class DBDocList {

    private $list = array();
    private $parameters;
    private $doctype;

    public $query;
    
    function __construct() {
        $this->doctype = isset($this->doctype) ? $this->doctype : strtolower(str_replace(__CLASS__, '', get_class($this)));
        $args = func_get_args();
        $this->query = array_shift($args);
        $this->parameters = $args;
        array_unshift($args, $this->getQuery());
        Log::debug(get_class($this).': Attempting to fetch list using query: '.$this->getQuery());
        if($results = call_user_func_array(array('DB', 'getAll'), $args)) {
            foreach($results as $row) {
                $this->list[] = DBDoc::get($doctype, $row);
            }
            Log::debug(get_class($this).': Built list with '.count($this->getList()).' items.');
        } else {
            Log::warning(get_class($this).': Created empty new '.get_class($this).': looks like the query failed: '.DB::error());
        }
    }

    public function getList() {
        if(is_array($this->list)) return $this->list;
        else return array();
    }

    private function getQuery() {
        return $this->query;
    }

    public static function get() {
        $args = func_get_args();
        $type = array_shift($args);
        $classname = ucwords($type).__CLASS__;
        Log::debug(__CLASS__.': Getting new '.$classname);
        $reflector = new ReflectionClass($classname);
        $dbdoclist = $reflector->newInstanceArgs($args);
        if($reflector->isInstance($dbdoclist)) return $dbdoclist;
        else {
            Log::warning(__CLASS__.': Could not get new '.$classname);
            return false;
        }
    }
}
?>
