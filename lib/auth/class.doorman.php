<?php

class DoormanFatalException extends Exception {
    function __construct($message, $code = 0) {
        @header(Doorman::HDR_UNAUTHORIZED);
        error_log($message);
        exit;
    }
}

class Doorman {
    
    const USER_TABLE = 'http_users';
    const USER_ROLES_TABLE = 'http_users_roles';

    const HDR_UNAUTHORIZED = 'HTTP/1.0 401 Unauthorized';

    private $realm;
    private $current_http_user;
    private $user_roles;
    
    function __construct($realm = 'doorman_realm') {
        if(headers_sent()) {
            throw new DoormanFatalException('Headers sent, cannot initialize Doorman for realm '.$realm);
        }
        $this->realm = $realm;
        if($this->getHttpUser() === false) {
            header('WWW-Authenticate: Basic realm="'.$this->getRealm().'"');
            header(self::HDR_UNAUTHORIZED);
            echo 'Please login.';
            exit;
        } else {
            $this->getUserRoles();
        }
        
    }

    public function getRealm() {
        return $this->realm;
    }

    public function setRealm($realm) {
        $this->realm = $realm;
        return $this->getRealm();
    }

    public function getHttpUser() {
        if(!isset($this->current_http_user)) {
            $this->setHttpUser();
        }

        return $this->current_http_user ? $this->current_http_user : false;
    }

    public function setHttpUser($user = false) {
        if($user === false) $user = $_SERVER['PHP_AUTH_USER'];
        $this->current_http_user = $user;
    }

    public function getUserRoles() {
        if(empty($this->user_roles)) {
            // fetch from db here.
            $this->setUserRoles(array('user', 'administrator'));
        }

        return $this->user_roles;
    }

    public function setUserRoles($roles = array()) {
        $this->user_roles = is_array($roles) ? $roles : array($roles);
        return $this->user_roles;
    }

    public function addRole($role) {
        $this->user_roles[] = $role;
        return $this->getUserRoles();
    }

    public static function authenticate($username, $password) {
        if($user_id = db::fetchOne('select id from users where username = ? and password = ? limit 1', $username, $password)) {
            return new User($user_id);
        } else return false;
    }

}
?>
