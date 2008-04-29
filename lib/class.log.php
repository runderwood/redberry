<?php
class LogMessage {

    const DATE_FORMAT = 'Y.m.d H:i:s';
    const PREFIX_BODY = true;

    private $timestamp;
    private $body;
    private $priority;

    function __construct($body, $priority = LOG::NOTICE, $timestamp = false) {
        $this->body = $body;
        $this->priority = $priority;
        $this->timestamp = $timestamp === false ? time() : $timestamp;
    }

    public function getBody() {
        return $this->body;
    }

    public function getPriority() {
        return $this->priority;
    }

    public function getPriorityString() {
        switch($this->getPriority()) {
            case Log::ERROR:
                return 'ERROR';
                break;
            case Log::WARNING:
                return 'WARNING';
                break;
            case Log::NOTICE:
                return 'NOTICE';
                break;
            case Log::DEBUG:
                return 'DEBUG';
                break;
        }
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function getDate() {
        return date(self::DATE_FORMAT, $this->getTimestamp());
    }

    function __toString() {
        $string = self::PREFIX_BODY ? Log::MESSAGE_PREFIX : '';
        $string .= $this->getDate().', '.$this->getPriorityString().': '.$this->getBody();
        return $string;
    }
}

class Log {

    const MESSAGE_PREFIX = 'REDBERRY:';
   
    const ERROR = 1;
    const WARNING = 2;
    const NOTICE = 3;
    const DEBUG = 4;

    const STDOUT = 0;
    const FILE = 1;
    const DB = 2;
    
    private static $buffer = array();
    private static $log_level = self::ERROR;
    private static $buffered = false;
    private static $output_method = self::FILE;
    private static $log_file = '../log/redberry.log';
    private static $on = false;

    public static function start() {
        self::$on = true;
    }

    public static function stop() {
        self::$on = false;
    }

    public static function getLogLevel() {
        return self::$log_level;
    }

    public static function setLogLevel($level) {
        self::$log_level = $level;
    }

    public static function setLogMethod($method) {
        self::$output_method = $method;
    }

    public static function getLogMethod() {
        return self::$output_method;
    }

    public static function setLogFile($path) {
        self::$log_file = $path;
    }

    public static function getLogFile() {
        return self::$log_file;
    }

    public static function write($message, $level = Log::NOTICE) {
        if(!self::$on) return false;
        if(self::$buffered) self::$buffer[] = new LogMessage($message, $level, time());
        else self::out((string) new LogMessage($message, $level, time()) . "\n");
        return true;
    }

    public static function out($message) {
        switch(self::$output_method) {
            case self::FILE:
                if(is_writeable(substr(self::$log_file, 0, strrpos(self::$log_file, '/')))) {
                    $fh = fopen(self::$log_file, 'a');
                    fwrite($fh, $message);
                } else {
                    self::setLogMethod(self::STDOUT);
                    Log::error('Could not write to logfile: '.self::$log_file);
                }
                break;
            case self::DB:
            case self::STDOUT:
            default:
                print $message;
                return true;
                break;
        }
    }

    public static function error($message) {
        return self::write($message, self::ERROR);
    }

    public static function warning($message) {
        return self::write($message, self::WARNING);
    }

    public static function notice($message) {
        return self::write($message, self::NOTICE);
    }

    public static function debug($message) {
        return self::write($message, self::DEBUG);
    }

    public static function flush($filter = true) {
        foreach(self::$buffer as $m => $msg) {
            if(!$filter || ($msg->getPriority() <= self::$log_level)) print "$msg.\n";
        }
    }

    public static function buffer($buffering = true) {
        self::$buffered = $buffering;
        return self::$buffered;
    }
}
?>
