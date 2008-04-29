<?php

class DoormanFatalException extends Exception {
    function __construct($message, $code = 0) {
        @header(Doorman::HDR_UNAUTHORIZED);
        error_log($message);
        exit;
    }
}

class DoormanGuest {

    private $username; // nickname
    private $roles = array();
    private $permissions = array();
    private $email;
    private $id;

    function __construct($username, $roles = array()) {
        $this->setUsername($username);
        $this->setRoles($roles); 
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this->getUsername();
    }

    public function getUsername() {
        return $this->username;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this->getEmail();
    }

    public function getEmail() {
        return $this->email;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this->getId();
    }

    public function setRoles($roles) {
        if(!is_array($roles)) $this->addRole($roles);
        else $this->roles = $roles;
        return $this->getRoles();
    }

    public function getRoles() {
        return $this->roles;
    }

    public function addRole($role) {
        if(!in_array($role, $this->getRoles())) $this->roles[] = $role;
        return $this->getRoles();
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function setPermissions($permissions = array()) {
        $this->permissions = $permissions;
        return $this->getPermissions();
    }

    public function addPermission($permission) {
        if(!in_array($permission, $this->getPermissions())) $this->permissions[] = $permission;
        return $this->getPermissions();
    }

    public function isA($roles) {
        $roles = is_array($roles) ? $roles : array($roles);
        return count(array_intersect($roles, $this->getRoles())) > 0;
    }

    public function can($permissions) {
        $permissions = is_array($permissions) ? $permissions : array($permissions);
        return count(array_intersect($permissions, $this->getPermissions())) > 0;
    }
}

class Doorman {

    const STORE_SIMPLE = 'simple';
    const STORE_DB = 'db';
    const STORE_FILE = 'file';
    
    const USER_TABLE = 'http_users';
    const USER_ROLES_TABLE = 'http_users_roles';

    const HDR_UNAUTHORIZED = 'HTTP/1.0 401 Unauthorized';

    const SALT = 'doormanisthemanofthedoor';

    private static $realm = 'redberry';
    private static $user_store = self::STORE_DB;
    private static $authenticated_user;

    // workaround for CGI PHP instance -- no auth hooks.
    static function getAuthorizationTokens() {
        $user = '';
        $passwd = '';
        if(isset($_GET['a'])) {
            list($user, $passwd) = explode(':', base64_decode(trim(str_replace(array('Basic', '=='), '', $_GET['a']))));
        }
        return array($user, $passwd);
    }

    static function getHttpUser() {
        if(!isset($_SERVER['PHP_AUTH_USER'])) list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = self::getAuthorizationTokens();
        return $_SERVER['PHP_AUTH_USER'];
    }

    static function getHttpPassword() {
        if(!isset($_SERVER['PHP_AUTH_PW'])) self::getHttpUser();
        return $_SERVER['PHP_AUTH_PW'];
    }

    static function challenge() {
        $response = new Response('Please provide your credentials.', Response::UNAUTHORIZED);
        $response->setHeader('WWW-Authenticate', 'Basic realm="'.self::$realm.'"');
        die($response);
    }

    static function authenticate($user = false, $passwd = false) {
        $user = $user === false ? self::getHttpUser() : $user;
        $passwd = $passwd === false ? self::getHttpPassword() : $passwd;
        $auth_func = 'authenticate_against_'.self::$user_store;
        Log::debug(__CLASS__.': Checking credentials provided for user '.$user);
        return self::$auth_func($user, $passwd);
    }

    static function authenticate_against_simple($user, $passwd) {
        return isset(self::$users[$user]) && self::$users[$user]['password'] === $passwd; 
    }

    static function authenticate_against_db($username, $passwd) {
        $sql = 'select u.id as id, u.email, u.nickname, r.name as role, v.name as verb, s.name as scope from users u, users_roles ur, roles r,'
                .' roles_permissions rp, permission_verbs v, permission_scopes s where u.email like ? and u.password like ? '
                .'and u.id=ur.user_id and ur.role_id=r.id and r.id=rp.role_id and rp.verb_id=v.id and rp.scope_id=s.id';
        if(is_array($result = DB::fetchAll($sql, $username, $passwd)) && !empty($result)) {
            $user = new DoormanGuest($result[0]->nickname);
            $user->setEmail($username);
            $user->setId($result[0]->id);
            $roles = array();
            $perms = array();
            foreach($result as $row) {
                $user->addRole($row->role);
                $user->addPermission($row->verb.' '.$row->scope);
            }
            Log::debug(__CLASS__.': Authenticated user '.$username.': '.implode(', ', $user->getRoles()));
            self::$authenticated_user = $user;
            return self::getAuthenticatedUser();
        }
        return false;
    }

    static function getAuthenticatedUser() {
        return self::$authenticated_user;
    }

    static function allow($allow) {
        if(!is_object($user = self::getAuthenticatedUser())) {
            $user = self::authenticate();
            if(!$user) self::challenge();
        }
        if(!is_array($allow)) $allow = array($allow);
        if($user->isA($allow) || $user->can($allow) || $user->isA('superuser')) return true;
        else die(new Response('You don\'t have permission to access this resource.', Response::FORBIDDEN));
    }

}
?>
