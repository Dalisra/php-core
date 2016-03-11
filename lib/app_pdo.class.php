<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vytautas
 * Date: 26.09.13
 * Time: 17:11
 * To change this template use File | Settings | File Templates.
 */


//thanks to  http://culttt.com/2012/10/01/roll-your-own-pdo-php-class/
class APP_PDO extends PDO {

    private $stmt;
    /**
     * Constructor.
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @param $port
     * @param string $engine
     */
    public function __construct($host, $user, $password, $database, $port, $engine = "mysql"){
        $dsn = "$engine:host=$host;port=$port;dbname=$database";
        $options = array(
            PDO::ATTR_PERSISTENT    => true,                     //persistante connection to reuse same connection on request. saves resources.
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION    //enabling exceptions to be thrown, for gracefull error handling.
        );
        parent::__construct($dsn, $user, $password, $options);

    }

}