<?php

/**
 * Description of CMS
 *
 * @author Vytautas
 */
class APP {

  	/**
   	* @var APP_Page
   	*/
  	static $page;
	/**
	 *
	 * @var APP_Request
	 */
	static $request;

	/**
	 *
	 * @var APP_DB
	 */
	static $db;

	/**
	 *
	 * @var APP_Auth
	 */
	static $auth;

	/**
	 * Siteconfiguration
	 * @var array
	 */
	static $conf;

	/**
	 * Tells us what device user is using.
	 * @var string
	 */
	static $style;

    /**
     * Logger
     * @var
     */
    static $log;

    /**
     * Controller
     * @var App_Controller
     */
    static $controller;
}

?>
