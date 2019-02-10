<?php

class APP_Controller {

    protected $log;

    function __construct() {
        $this->log = Logger::getLogger("com.dalisra.controller");
    }

    function process(){
        return $this->displayPageNotFoundError();
    }

    function displayPageNotFoundError(){
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        $protocol = "HTTP/1.0";
        if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
            $protocol = "HTTP/1.1";
        }
        header( "$protocol 404 Page Not Found", true, 404 );
        $this->log->error("Displaying 404 error. Url was not found, request: " . print_r($_REQUEST, true));
        return array("code"=>404, "message"=>"404 Not Found");
    }

    function displayPageNotImplementedError(){
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        $protocol = "HTTP/1.0";
        if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
            $protocol = "HTTP/1.1";
        }
        header( "$protocol 501 Not implemented", true, 501 );
        $this->log->error("Displaying 501 error. Not implemented function: " . print_r($_REQUEST, true));
        return array("code"=>501, "message"=>"501 Not Implemented");
    }

    function displayNoAccessError(){
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        $protocol = "HTTP/1.0";
        if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
            $protocol = "HTTP/1.1";
        }
        header( "$protocol 401 Not authorized", true, 501 );
        $this->log->error("Displaying 401 error. Not authorized: " . print_r($_REQUEST, true));
        return array("code"=>401, "message"=>"401 Not Authorized");
    }
}