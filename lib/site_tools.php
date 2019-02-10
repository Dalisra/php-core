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

/**
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}