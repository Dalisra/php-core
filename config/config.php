<?php

/**
 * TODO: Make the most important configs into CONTANTS/PROPERTIES.
 * Ex. $CORE_CONTROLLERS_PATH, $CORE_LIB_PATH...
 */

$conf = array();
$conf['prod'] = array();
$conf['test'] = array();
$conf['devel'] = array();


/* START SITE STUFF */

/* Full path to the content */
$conf['prod']['path']['full'] = ROOT . DS;
$conf['test']['path']['full'] = ROOT . DS;
$conf['devel']['path']['full'] = ROOT . DS;

/* Full path to the images */
$conf['prod']['path']['images'] = ROOT . DS . ".." . DS . "img";
$conf['test']['path']['images'] = ROOT . DS . ".." . DS . "img";
$conf['devel']['path']['images'] = ROOT . DS . ".." . DS . "img";

/* Path to controllers */
$conf['prod']['path']['controllers'] = $conf['devel']['path']['full'] . 'controllers' . DS;
$conf['test']['path']['controllers'] = $conf['devel']['path']['full'] . 'controllers' . DS;
$conf['devel']['path']['controllers'] = $conf['devel']['path']['full']. 'controllers' . DS;

/* Path to services */
$conf['prod']['path']['services'] = $conf['devel']['path']['full'] . 'services' . DS;
$conf['test']['path']['services'] = $conf['devel']['path']['full'] . 'services' . DS;
$conf['devel']['path']['services'] = $conf['devel']['path']['full']. 'services' . DS;

/* Path to the site models folder */
$conf['prod']['path']['models'] = $conf['prod']['path']['full'] . 'models' . DS;
$conf['test']['path']['models'] = $conf['prod']['path']['full'] . 'models' . DS;
$conf['devel']['path']['models'] = $conf['devel']['path']['full'] . 'models' . DS;

/* Page configurations */
$conf['prod']['showTimer'] = false;
$conf['test']['showTimer'] = true;
$conf['devel']['showTimer'] = true;

/* Show debug messages */
$conf['prod']['enable_debug_msg'] = false;
$conf['test']['enable_debug_msg'] = false;
$conf['devel']['enable_debug_msg'] = true;

/* START CORE STUFF */

/* Path to the library */
$conf['prod']['path']['lib'] = CORE . DS . 'lib' . DS;
$conf['test']['path']['lib'] = CORE . DS . 'lib' . DS;
$conf['devel']['path']['lib'] = CORE . DS . 'lib' . DS;

/* Log4PHP configuration */
$conf['prod']['path']['log4php'] = $conf['prod']['path']['lib'] . 'log4php' . DS . '2.3.0' . DS;
$conf['test']['path']['log4php'] = $conf['prod']['path']['lib'] . 'log4php' . DS . '2.3.0' . DS;
$conf['devel']['path']['log4php'] = $conf['devel']['path']['lib'] . 'log4php' . DS . '2.3.0' . DS;

$conf['prod']['path']['log4php_conf'] = $conf['prod']['path']['full'] . 'config' . DS . 'log4php.xml';
$conf['test']['path']['log4php_conf'] = $conf['prod']['path']['full'] . 'config' . DS . 'log4php.xml';
$conf['devel']['path']['log4php_conf'] = $conf['devel']['path']['full'] . 'config' . DS . 'log4php.xml';

/* Authentication configuration */
$conf['prod']['auth']['table'] = "users";
$conf['test']['auth']['table'] = "users";
$conf['devel']['auth']['table'] = "users";