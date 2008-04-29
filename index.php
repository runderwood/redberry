<?php
require_once('inc/inc.redberry.php');
$request = new Request();
print(Handler::get(PathManager::findHandler($request->getUri()))->go());
?>
