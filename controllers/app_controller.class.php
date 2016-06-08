<?php

class APP_Controller {

    protected $log;

    function __construct() {
        $this->log = Logger::getLogger("com.dalisra.controller");
    }

    function process(){
        $this->displayPageNotFoundError();
    }

    function displayPageNotFoundError(){
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        APP::$smarty->assign("error_msg", "Stien finnes ikke");
        APP::$smarty->assign("error_nr", "404");
        $protocol = "HTTP/1.0";
        if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
            $protocol = "HTTP/1.1";
        }
        header( "$protocol 404 Page Not Found", true, 404 );
        $this->log->error("Displaying 404 error. Url was not found, request: " . print_r($_REQUEST, true));
        APP::$smarty->display('error_pages/404.tpl');
    }

    function displayPageNotImplementedError(){
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        APP::$smarty->assign("error_msg", "Not implemented");
        APP::$smarty->assign("error_nr", "501");
        $protocol = "HTTP/1.0";
        if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
            $protocol = "HTTP/1.1";
        }
        header( "$protocol 501 Not implemented", true, 404 );
        $this->log->error("Displaying 501 error. Missing Smarty template, request: " . print_r($_REQUEST, true));
        APP::$smarty->display('error_pages/501.tpl');
    }
}