<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vytautas
 * Date: 28.01.14
 * Time: 12:09
 * To change this template use File | Settings | File Templates.
 */

class Init {
    //Tells what environment we are in.
    var $env;

    function initialize($environment = "devel"){
        $this->env = $environment;
        //include site tools
        $this->includeSiteTools();
        $this->startSession();
        $this->initializeAppClass();
        $this->initializeConfig();
        $this->initializeLogger();
        $this->initializeDBConnection();
        $this->initializeSmarty();
        $this->initializeRequestClass();
        $this->initializeAuthentication();
        $this->initializeBasket();
        $this->initializeGlobals();
        $this->checkIfWeHaveDBConnection();
        //use this to test smarty configuration
        //APP::$smarty->testInstall();
        //exit;
    }

    private function includeSiteTools(){
        require 'site_tools.php'; //include simple tools
        $GLOBALS['timer'] = new Timer(); //start timer to record how long it takes to process the request
    }

    private function startSession(){
        ob_start();
        session_start();
        //Setting default time zone for the server.
        date_default_timezone_set("CET");
    }

    private function initializeAppClass(){
        //Adding a class to keep the global variables (such as $smarty, $config, $db etc)
        require 'app.class.php';
    }

    private function initializeConfig(){
        require CORE . DS . 'config' . DS . 'config.php'; // Produces $conf variable and sets default values in it.
        require ROOT . DS . 'config' . DS . 'config.php'; // Overrides or adds new values to $conf variable.
        
        //setting this config object in as global variable.
        APP::$conf = $conf[$this->env];
    }

    private function initializeLogger(){
        include(APP::$conf['path']['log4php'] . "Logger.php");
        Logger::configure(APP::$conf['path']['log4php_conf']);
        APP::$log = Logger::getLogger("com.dalisra");
    }

    private function initializeDBConnection(){
        require 'app_db.class.php';
        $dbConfFilePath = ROOT . DS . 'config' . DS . 'db.php';
        if(file_exists($dbConfFilePath)) {
            require $dbConfFilePath;
            $db_conf = $db_conf[$this->env];
            APP::$db = new APP_DB($db_conf['host'], $db_conf['user'], $db_conf['password'], $db_conf['database'], $db_conf['port'], $db_conf['prefix']);
            unset($db_conf);
        }
    }

    private function initializeSmarty(){
        /* Smarty setup  - Do not edit anything here, do it in config file instead! */
        define('SMARTY_DIR', APP::$conf['path']['smarty']);
        require APP::$conf['path']['smarty'].'Smarty.class.php';
        APP::$smarty = new Smarty();
        APP::$smarty->setTemplateDir(APP::$conf['smarty']['templates']);
        APP::$smarty->setCompileDir(APP::$conf['smarty']['templates_c']);
        APP::$smarty->setCacheDir(APP::$conf['smarty']['cache']);
        APP::$smarty->setConfigDir(APP::$conf['smarty']['config']);
        APP::$smarty->addPluginsDir(APP::$conf['smarty']['plugins']);
    }

    private function initializeRequestClass(){
        require 'app_request.class.php';
        APP::$request = new APP_Request();
    }

    private function checkIfWeHaveDBConnection(){
        if(isset(APP::$db)){
            if (!APP::$db->connected){
                //TODO: return a 503 error - 503 Service Unavailable
                // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
                APP::$smarty->assign("error_msg", "Feil har oppstått. Vennligst prøv igjen!");
                APP::$smarty->assign("error_nr", "001");
                if(APP::$conf["enable_debug_msg"]){
                    APP::$smarty->assign("debug_msg", APP::$db->db_error);
                }
                $protocol = "HTTP/1.0";
                if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
                    $protocol = "HTTP/1.1";
                }
                header( "$protocol 503 Service Unavailable", true, 503 );
                header( "Retry-After: 60" ); //60 seconds.
                APP::$log->error("Displaying 503 error. No connection to database.");
                APP::$smarty->display('error_pages/503.tpl');
                APP::$request->quit();
            }
        
        }
        //TODO: decide what to do if APP:$db is not set? It means no db config file.. no db needed or error?
        
    }

    private function initializeAuthentication(){
        require 'app_auth.class.php';
        APP::$auth = new APP_Auth();

        if (isset($_REQUEST['pre_act'])) {
            APP::$log->debug("Got pre_act: " . $_REQUEST['pre_act']);
            if ($_REQUEST['pre_act'] == "do_login") {
                usleep(500000);
                if (isset($_POST['login'])) {
                    list($u, $p) = $_POST["login"];
                    APP::$auth->login($u, $p);
                    APP::$request->jump();
                }
                else{
                    APP::$request->addError("Username and password can not be empty.");
                    APP::$request->jump();
                }
            } elseif ($_REQUEST['pre_act'] == "do_logout") {
                APP::$auth->logout();
                APP::$request->jump();
            } else {
                APP::$request->addError("Unknown Command");
            }
        }
        APP::$smarty->assign("isLoggedIn", APP::$auth->isLoggedIn);
    }

    private function initializeBasket(){
        require 'app_basket.class.php';
        APP::$basket = new APP_Basket();
    }

    private function initializeGlobals(){

        /** DEBUG info */
        APP::$smarty->assign("debug_compile_time", APP::$conf['smarty']['debug_compile_time']);

        /** URLS */
        APP::$smarty->assign("site_url", APP::$conf["url"]["site"]);
        APP::$smarty->assign("domain_url", APP::$conf["url"]["domain"]);
        APP::$smarty->assign("full_url", APP::$request->full_url);
        APP::$smarty->assign("request", $_REQUEST);

        //TODO: those should not be needed.
        APP::$smarty->assign("url", APP::$request->url);
        APP::$smarty->assign("url_path", APP::$request->path_arr);

        APP::$smarty->assign("images_path", APP::$conf['path']['images']);
        
        APP::$smarty->assign("messages", APP::$request->getMessages());
        APP::$request->removeMessages();
        
        /*
         * setting environment variable so that design can do some stuff depending on environment
         * Example could be displaying google tag manager or google maps only in test and production.
         */
        APP::$smarty->assign("env", $this->env);
    }
}