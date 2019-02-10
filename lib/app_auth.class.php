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
    var $apiKey;

    var $log;

    function __construct() {
        $this->log = Logger::getLogger("com.dalisra.auth");

        if(isset($_SERVER["HTTPS_PRIVATE_TOKEN"])) {
            $this->apiKey = $_SERVER["HTTPS_PRIVATE_TOKEN"];
        }
        elseif(isset($_SERVER["HTTP_PRIVATE_TOKEN"])){
            $this->apiKey = $_SERVER["HTTP_PRIVATE_TOKEN"];
        }

        $this->log->debug("Initializing auth.. with apikey: " . $this->apiKey);
        $this->getUser();
    }

    function login($username, $password) {
        $params['from'] = APP::$conf['auth']['table'];
        $params['where'] = array('username' => $username, 'active'=>1);
        $params['limit'] = 1;
        $reply = APP::$db->getData($params);
        if ($reply && isset($reply[0]) && password_verify($password, $reply[0]['password'])) {
            // good user/pass
            $user = $reply[0];
            if(isset($user['logins'])) {
                $user['logins'] += 1;
            }
            $apiKey = random_str(32);
            APP::$db->updateDataById($params['from'], array('apiKey'=>$apiKey), $reply[0]['id']);
            $this->apiKey = $apiKey;
            return true;
        }

        return false;
    }

    function logout() {
        if($this->isLoggedIn && $this->user){
            APP::$db->updateDataById(APP::$conf['auth']['table'], array("apiKey"=>null), $this->user['id']);
            return true;
        }
        return false;
    }

    function getUser(){
        if($this->apiKey && strlen($this->apiKey) > 0){
            $this->log->debug("Used api key found.");
            $params['from'] = APP::$conf['auth']['table'];
            $params['where'] = array("apiKey"=> $this->apiKey, 'active'=>1);
            $params['limit'] = 1;
            $reply = APP::$db->getData($params);
            if($reply && isset($reply[0])){
                $this->log->debug("User with api key found in database");
                $this->isLoggedIn = true;
                $this->user = $reply[0];
                unset($this->user["password"]);
                unset($this->user["apiKey"]);
                return;
            }
        }

        $this->isLoggedIn = false;
        $this->user = null;
    }
}