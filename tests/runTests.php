<?php

define('DS', DIRECTORY_SEPARATOR);
define('CORE', dirname(dirname(dirname(__FILE__))) . DS . 'core');
define('ROOT', dirname(dirname(__FILE__)));


//load all configuration before processing request.
require CORE . DS . 'lib' . DS . 'init.class.php';
$init = new Init();
$init->initialize();

/**
 * Run First test
 */

$model = new App_Model();
$model->getByName();