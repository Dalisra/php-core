<?php

/**
 * TODO: Make the most important configs into CONTANTS/PROPERTIES.
 * Ex. $CORE_CONTROLLERS_PATH, $CORE_LIB_PATH...
 * /

$conf = array();
$conf['prod'] = array();
$conf['test'] = array();
$conf['devel'] = array();


/* START SITE STUFF */

/* Routing options */
$conf['prod']['routing']['enabled'] = false;
$conf['test']['routing']['enabled'] = false;
$conf['devel']['routing']['enabled'] = true;

$conf['prod']['routing']['defaultController'] = 'index';
$conf['test']['routing']['defaultController'] = 'index';
$conf['devel']['routing']['defaultController'] = 'index';

$conf['prod']['routing']['defaultAction'] = '';
$conf['test']['routing']['defaultAction'] = '';
$conf['devel']['routing']['defaultAction'] = '';
/* End Routing options */

/* Full path to the content */
$conf['prod']['path']['full'] = ROOT . DS;
$conf['test']['path']['full'] = ROOT . DS;
$conf['devel']['path']['full'] = ROOT . DS;

/* Full path to the images */
$conf['prod']['path']['images'] = $conf['prod']['path']['full'] . 'webroot' . DS . 'images';
$conf['test']['path']['images'] = $conf['prod']['path']['full'] . 'webroot' . DS . 'images';
$conf['devel']['path']['images'] = $conf['devel']['path']['full'] . 'webroot' . DS . 'images';

/* Path to controllers */
$conf['prod']['path']['controllers'] = $conf['devel']['path']['full'] . 'controllers' . DS;
$conf['test']['path']['controllers'] = $conf['devel']['path']['full'] . 'controllers' . DS;
$conf['devel']['path']['controllers'] = $conf['devel']['path']['full']. 'controllers' . DS;

/* Path to services */
$conf['prod']['path']['services'] = $conf['devel']['path']['full'] . 'services' . DS;
$conf['test']['path']['services'] = $conf['devel']['path']['full'] . 'services' . DS;
$conf['devel']['path']['services'] = $conf['devel']['path']['full']. 'services' . DS;

/* Path to the view / templates
 * !OBS remember to override smarty path for templates if you override this one!
 * $conf['evn']['smarty']['templates'] depends on this!
 * TODO: find out a better way for this to work.
 */
$conf['prod']['path']['views'] = $conf['prod']['path']['full'] . 'views' . DS;
$conf['test']['path']['views'] = $conf['prod']['path']['full'] . 'views' . DS;
$conf['devel']['path']['views'] = $conf['devel']['path']['full'] . 'views' . DS;

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
$conf['true']['enable_debug_msg'] = false;
$conf['devel']['enable_debug_msg'] = true;


/* START CORE STUFF */

/* Path to the library */
$conf['prod']['path']['lib'] = CORE . DS . 'lib' . DS;
$conf['test']['path']['lib'] = CORE . DS . 'lib' . DS;
$conf['devel']['path']['lib'] = CORE . DS . 'lib' . DS;

/* Path to the core controllers */
$conf['prod']['path']['core']['controllers'] = CORE . DS . 'controllers' . DS;
$conf['test']['path']['core']['controllers'] = CORE . DS . 'controllers' . DS;
$conf['devel']['path']['core']['controllers'] = CORE . DS . 'controllers' . DS;

/* Path to the core views */
$conf['prod']['path']['core']['views'] = CORE . DS . 'views' . DS;
$conf['test']['path']['core']['views'] = CORE . DS . 'views' . DS;
$conf['devel']['path']['core']['views'] = CORE . DS . 'views' . DS;

/* Path to the core models */
$conf['prod']['path']['core']['models'] = CORE . DS . 'models' . DS;
$conf['test']['path']['core']['models'] = CORE . DS . 'models' . DS;
$conf['devel']['path']['core']['models'] = CORE . DS . 'models' . DS;

/* Smarty configuration */
$conf['prod']['path']['smarty'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'Smarty-3.1.29' . DS;
$conf['test']['path']['smarty'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'Smarty-3.1.29' . DS;
$conf['devel']['path']['smarty'] = $conf['devel']['path']['lib'] . 'Smarty' . DS . 'Smarty-3.1.29' . DS;

$conf['prod']['smarty']['templates_c'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'templates_c' . DS;
$conf['test']['smarty']['templates_c'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'templates_c' . DS;
$conf['devel']['smarty']['templates_c'] = $conf['devel']['path']['lib'] . 'Smarty' . DS . 'templates_c' . DS;

$conf['prod']['smarty']['templates'] = [$conf['prod']['path']['views'], $conf['prod']['path']['core']['views']];
$conf['test']['smarty']['templates'] = [$conf['prod']['path']['views'], $conf['test']['path']['core']['views']];
$conf['devel']['smarty']['templates'] = [$conf['devel']['path']['views'], $conf['devel']['path']['core']['views']];

$conf['prod']['smarty']['cache'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'cache' . DS;
$conf['test']['smarty']['cache'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'cache' . DS;
$conf['devel']['smarty']['cache'] = $conf['devel']['path']['lib'] . 'Smarty' . DS . 'cache' . DS;

$conf['prod']['smarty']['config'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'config' . DS;
$conf['test']['smarty']['config'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'config' . DS;
$conf['devel']['smarty']['config'] = $conf['devel']['path']['lib'] . 'Smarty' . DS . 'config' . DS;

$conf['prod']['smarty']['plugins'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'plugins' . DS;
$conf['test']['smarty']['plugins'] = $conf['prod']['path']['lib'] . 'Smarty' . DS . 'plugins' . DS;
$conf['devel']['smarty']['plugins'] = $conf['devel']['path']['lib'] . 'Smarty' . DS . 'plugins' . DS;

$conf['prod']['smarty']['debug_compile_time'] = false;
$conf['test']['smarty']['debug_compile_time'] = true;
$conf['devel']['smarty']['debug_compile_time'] = true;

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