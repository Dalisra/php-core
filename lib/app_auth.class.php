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

    function login($username, $password) {
        $params['from'] = APP::$conf['auth']['table'];
        $params['where'] = array('username' => $username, 'active'=>1);
        $params['limit'] = 1;
        $reply = APP::$db->getData($params);
        if ($reply && isset($reply[0]) && password_verify($password, $reply[0].password)) {
            // good user/pass
            $_SESSION["login"] = true;
            $_SESSION["user"] = $reply[0];
            $this->refreshFromSession();
            APP::$request->addSuccess("Login successfull.");
            if(isset($_SESSION["user"]['logins'])) {
                $_SESSION["user"]['logins'] += 1;
                APP::$db->updateDataById($params['from'], $_SESSION["user"], $reply[0]['id']);
            }
        } else {
            // wrong user/pass
            $_SESSION["login"] = false;
            APP::$request->addError("Username and password combination is wrong. Try again.");
        }

        return $this->isLoggedIn;
    }

    function logout() {
        //TODO:save user info from session to database
        unset($_SESSION['login']);
        unset($_SESSION['user']);
        $this->refreshFromSession();
        APP::$request->addSuccess("You have been logged out.");

        return $this->isLoggedIn;
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