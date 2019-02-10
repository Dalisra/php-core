<?php
/**
 * Class APP_Request
 *
 * @author vytautas
 */
class APP_Request {

    var $url;
    var $log;
    var $path_arr;
    var $full_url;
    var $controller_url;
    var $controller_action;
    var $consumedUrlPaths = [];
    var $unConsumedUrlPaths = [];

    function __construct() {
        $this->log = Logger::getLogger("com.dalisra.request");
        $this->processUrl();
        $this->log->debug("URL is:" . $this->url);
    }

    function processUrl() {
        //Remove first slash from the url.
        $this->full_url = $_SERVER['REQUEST_URI'];
        $this->log->debug("Setting full url to: " . $this->full_url);
        
        //Remove all query parameters from full url (only interested in first part):
        $this->url = explode('?', $_SERVER['REQUEST_URI'])[0];
        
        //Remove site url (the part that we already know).
        $this->log->debug("Url of the request is: " . $this->url);
        $this->log->debug("Removing site url from it: " . APP::$conf['url']['site']);
        $siteUrlLength = strlen(APP::$conf['url']['site']);
        $this->url = substr($this->url,$siteUrlLength);
        $this->log->debug("New Url is: " . $this->url);
        $this->path_arr = explode('/', $this->url);
        $this->unConsumedUrlPaths = $this->path_arr;
        $this->log->debug("Path exploded is: " . json_encode($this->path_arr));
    }

    function consumeNextPath(){
        if(!isset($this->unConsumedUrlPaths)) $this->unConsumedUrlPaths = [];
        if(!empty($this->unConsumedUrlPaths)){


            return $this->consumedUrlPaths[] = array_shift($this->unConsumedUrlPaths);
        }

        return null;
    }

    function processRequest(){
        $this->log->debug("processRequest() method has been called, starting process..");
        //TODO: implement url_mapping
        if(isset($this->path_arr[0])){
            $controllerName = strtolower($this->path_arr[0]);
            //check if its index
            if($controllerName == ''){
                $controllerName = 'index';
            }
            $this->log->debug("Controller name that we will be calling is: " . $controllerName);
            //lets find out if we have any controller
            $controllerFile = APP::$conf['path']['controllers'] . $controllerName . '.class.php';
            if(file_exists($controllerFile)){ //controller exists we try to load it
                $this->log->debug("Controller file exists on disk, we try to load it!");
                require_once($controllerFile);
                $controllerClass = ucwords($controllerName) . "_Controller";
                APP::$controller = new $controllerClass();
                
                //check if we are calling a speciffic process function
                $this->log->debug("Trying to find out if we should call speccific function in controller");
                $this->controller_url = $this->consumeNextPath();
                if(isset($this->path_arr[1]) && strlen($this->path_arr[1]) > 0){
                    $this->log->debug("path_arr nr 1 is defined, trying to load specific function: " . $this->path_arr[1]);
                    $processName = "process".ucwords($this->path_arr[1]);
                    if(method_exists(APP::$controller, $processName)){
                        $this->controller_action = $this->consumeNextPath();
                        return APP::$controller->$processName();
                    }else{
                        //$this->log->debug("$controllerClass does not have function $processName, redirecting user to correct url: " . $this->path_arr[0]);
                        //$this->jump($this->path_arr[0]);
                        return APP::$controller->process();
                    }
                }else{ //call the default process function
                    return APP::$controller->process();
                }
                

            }else{ //controller does not exists, we display 404 error with parent controller
                $this->log->debug("Controller does not exist, we load default controller and display 404 error.");
                require_once APP::$conf['path']['lib'] . 'app_controller.class.php';
                APP::$controller = new APP_Controller();
                return APP::$controller->displayPageNotFoundError();
            }
        }else{
            $this->log->debug("Path Arr is empty.. Something must be wrong, returning 503 error");
            //Something went wrong with processing url, we display 503 error.
            // http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
            $protocol = "HTTP/1.0";
            if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){
                $protocol = "HTTP/1.1";
            }
            header( "$protocol 503 Server error", true, 503 );
            return array("code"=>501, "message"=>"Server error");
        }
    }

    /**
     *
     * A quick way to make redirects permanent or temporary is
     * to make use of the $http_response_code parameter in header().
     *
     * <?php
     * // 301 Moved Permanently
     * header("Location: /foo.php",TRUE,301);
     *
     * // 302 Found
     * header("Location: /foo.php",TRUE,302);
     * header("Location: /foo.php");
     *
     * // 303 See Other
     * header("Location: /foo.php",TRUE,303);
     *
     * // 307 Temporary Redirect
     * header("Location: /foo.php",TRUE,307);
     * ?>
     *  The HTTP status code changes the way browsers
     * and robots handle redirects, so if you are using header(Location:)
     * it's a good idea to set the status code at the same time.
     * Browsers typically:
     * re-request a 307 page every time,
     * cache a 302 page for the session,
     * cache a 301 page for longer, or even indefinitely.
     *
     * Search engines typically:
     * transfer "page rank" to the new location for 301 redirects,
     * but not for 302, 303 or 307.
     *
     * If the status code is not specified, header('Location:') defaults to 302.
     * @param string $url
     * @param int $code default value is 302 - Found
     */
    function jump($url = false, $code = 302) {
        if(isset($_REQUEST['redir'])){
            $url = urldecode($_REQUEST['redir']);
            $this->log->debug("Redir parameter found. Redirecting to: " . $url);
            $url = APP::$conf['url']['site'] . $url;
            header("Location: $url", true, $code);
        }else if($url != false) {
            $url = APP::$conf['url']['site'] . $url;
            header("Location: $url", true, $code);
        }else {
            $this->log->debug("Jumping back to: " . $this->full_url);
            header("Location: $this->full_url", true, $code);
        }
        exit;

    }

    /**
     * Greacefuly close the application.
     */
    function quit($data) {
        $response = array("data"=>$data);
        //at the end we show time used to process the message
        if (APP::$conf['showTimer']) {
            $time = $GLOBALS['timer']->stop();
            $response["timer"] = array("message"=>"Request processed in: " . $time, "time"=>$time);
            $response["path"] = APP::$request->full_url;
        }else{
            APP::$log->debug("Request processed in: " . $GLOBALS['timer']->stop());
            // Apache logs: take a look at that: http://logging.apache.org/log4php/download.html
        }
        echo json_encode($response);
        exit;
    }


    /**
     * success - Green, info - Blue, warning - Yellow, danger/error - Red
     * @param $msg
     * @param $status_id
     */
    function addMessage($type, $msg) {
        $_SESSION['messages'][$type][] = $msg;
    }

    function addSuccess($msg) {
        $this->addMessage("success", $msg);
    }

    function addInfo($msg) {
        $this->addMessage("info", $msg);
    }

    function addWarning($msg) {
        $this->addMessage("warning", $msg);
    }

    function addError($msg) {
        $this->addMessage("error", $msg);
    }

    function getMessages(){
        if(isset($_SESSION['messages'])){
            return $_SESSION['messages'];
        }else return [];
    }

    function removeMessages() {
        $_SESSION['messages'] = null;
    }
}