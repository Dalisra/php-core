<?php

class Timer
{
    var $start;
    function __construct(){$this->start=microtime(1);}
    function stop($precision=5){ return sprintf('%01.'.(int)$precision.'f',microtime(1)-$this->start); }
}

function isValidInteger($item){
    if($item && $item != '' && $item == (int)$item){
        return true;
    }
    return false;
}

/**
 * Autoloading classes from controllers in both core and site and lib folders.
 */
spl_autoload_register(function ($class_name) {
    $folderNames = [
        APP::$conf['path']['controllers'],
        APP::$conf['path']['services'],
        APP::$conf['path']['models'],
        APP::$conf['path']['lib']
    ];
    $fileName = strtolower($class_name) . '.class.php';
    foreach($folderNames as $folderName){
        if(file_exists($folderName.$fileName)){
            include_once $folderName.$fileName;
        }
    }
});

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
function endsWith($haystack, $needle)
{
    substr($haystack, -strlen($needle))===$needle;
}

