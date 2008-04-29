<?php
class DB extends MySQLi {

    protected static $host = 'localhost';
    protected static $db = 'db';
    protected static $user = 'user';
    protected static $password = 'pass';

    private static $connection;

    private static $query_count = 0;
    
    function __construct() {
        self::$host = Config::get('db_host');
        self::$db = Config::get('db_name');
        self::$user = Config::get('db_user');
        self::$password = Config::get('db_pass');
        parent::__construct(self::$host, self::$user, self::$password, self::$db);
    }

    public static function get_connection() {
        if(!self::$connection) {
            self::$connection = @new DB();
        }

        return self::$connection;
    }

    public static function getConnection() {
        return self::get_connection();
    }

    public static function query_escape() {
        $args = func_get_args();
        $query = array_shift($args);
        foreach($args as $a => $arg) $args[$a] = preg_replace('/([^\\\\]??)\?/', '$1\?', $arg);
        foreach($args as $a => $arg) {
            $query = preg_replace('/([^\\\\])\?/', '$1'."'".@self::get_connection()->real_escape_string($arg)."'", $query, 1);
        }
        $query = preg_replace('/\\\\\?/', '?', $query);
        return $query;
    }

    public static function q() {
        $args = func_get_args();
        $query = call_user_func_array(array('self', 'query_escape'), $args);
        $db = self::get_connection();
        return @$db->query($query);
    }

    public static function error() {
        return self::get_connection()->error;
    }

    public static function getOne() {
        //todo
        $args = func_get_args();
        $result = call_user_func_array(array('self', 'q'), $args);
        return is_object($result) ? $result->fetch_object() : false;
    }

    public static function fetchOne() {
        $args = func_get_args();
        return call_user_func_array(array('self', 'getOne'), $args);
    }

    public static function getAll() {
        //todo
        $args = func_get_args();
        $result = call_user_func_array(array('self', 'q'), $args);
        $rows = array();
        if($result) {
            while($row = $result->fetch_object()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public static function fetchAll() {
        $args = func_get_args();
        return call_user_func_array(array('self', 'getAll'), $args);
    }

    public static function getPaged() {
        //do sql_cal_found_rows stuff here.
    }

    public static function getLastInsertId() {
        $result = DB::q('select last_insert_id()');
        if($row = $result->fetch_array()) return $row[0];
        else return false;
    }
}
?>
