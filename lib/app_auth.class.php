<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vytautas
 * Date: 28.01.14
 * Time: 14:12
 * To change this template use File | Settings | File Templates.
 */

class APP_Auth {
    var $isLoggedIn;
    var $user;

    function __construct() {
        $this->refreshFromSession();
    }

    function login() {
        list($u, $p) = $_POST["login"];

        $params['from'] = APP::$conf['auth']['table'];
        $params['where'] = array('username' => $u, 'password' => md5($p));
        $params['limit'] = 1;
        $reply = APP::$db->getData($params);
        if ($reply && isset($reply[0])) {
            // good user/pass
            $_SESSION["login"] = true;
            $_SESSION["user"] = $reply[0];
            $this->refreshFromSession();
            $item = array();
            $item['logins'] = $reply[0]['logins'] + 1;
            APP::$db->updateDataById($params['from'], $item, $reply[0]['id']);
            APP::$request->setMessage("Login successfull.");
        } else {
            // wrong user/pass
            $_SESSION["login"] = false;
            APP::$request->setError("Username and password combination is wrong. Try again.");
            APP::$request->jump($_REQUEST['url']);
        }
    }

    function logout() {
        //TODO:save user info from session to database
        unset($_SESSION['login']);
        unset($_SESSION['user']);
        $this->refreshFromSession();
        APP::$request->setMessage("You have been logged out.");
        if(isset($_REQUEST['redir'])){
            APP::$request->jump($_REQUEST['redir']);
        }else{
            APP::$request->jump();
        }
    }

    function refreshFromSession(){
        if (isset($_SESSION['login']) && isset($_SESSION['user'])) {
            $this->isLoggedIn = $_SESSION['login'];
            $this->user = $_SESSION['user'];
        } else {
            $this->isLoggedIn = false;
            $this->user = null;
        }
    }
}