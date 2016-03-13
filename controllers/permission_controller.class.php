<?php

// TODO: What is a cool name for this controller? Access_C, Privacy_C, Permission_C ??
class Permission_Controller {

    /**
     * Overriding standard method with custom action.
     */
    function process(){
        if( !APP::$auth->isLoggedIn) APP::$request->jump("login");
        else $this->processWithPermission();
    }

    /**
     * Controllers extending this class must use an implementation of the processPrivate function as the default
     * "process" action.
     */
    function processWithPermission() {}
}