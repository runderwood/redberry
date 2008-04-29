<?php

$base_path = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/'));
$include_path = ini_get('include_path');

ini_set('include_path', 
    $include_path.':'
    .$base_path.':'
    .$base_path.'/lib:'
    .$base_path.'/lib/handlers:'
    .$base_path.'/lib/db:'
    .$base_path.'/lib/db/dbdocs:'
    .$base_path.'/lib/db/dbdoclists'

);

function __autoload($classname) {
    $matches = array();
    preg_match('/^[A-Za-z0-9_]+(Handler|DBDoc|DBDocList)$/', $classname, $matches);
    if(isset($matches[1])) { $prefix = strtolower($matches[1]); $classname = str_replace($matches[1], '', $classname); }
    else { $prefix = 'class'; }
    $filename = "$prefix.".strtolower($classname).'.php';
    require_once($filename);
}

Config::load('../conf/conf.redberry.xml');
Log::setLogFile(Config::get('log_dir').'/redberry_'.date('Ymd').'.log');
Log::start();
Pathmanager::loadPaths();
?>
