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
?>