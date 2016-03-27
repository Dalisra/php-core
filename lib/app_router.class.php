<?php

/**
 * Created by IntelliJ IDEA.
 * User: Aleksander Akerø
 * Date: 27.03.2016
 * Time: 05.41
 */
class APP_Router {
    private $path;
    private $defaultController;
    private $defaultAction;

    private $controller;
    private $action;

    public function __construct($path) {
        $this->log = Logger::getLogger("com.dalisra.request");

        $this->path = $path;
        $this->defaultController = APP::$conf['routing']['defaultController'];
        $this->defaultAction = APP::$conf['routing']['defaultAction'];
    }

    public function parse() {
        if($this->path == null)
            $this->path = '';

        // TODO: Possible bug with this removing the GET parameters ...?
        $chunks = explode('/', $this->path);

        if(strlen($chunks[0]) < 1) {
            $this->controller = $this->defaultController;
            $this->action = $this->defaultAction;

        } else {
            $controllerName = array_shift($chunks);
            $controllerFile = APP::$conf['path']['controllers'] . $controllerName . '.class.php';
            $actionName = array_shift($chunks);

            /** Assuming error until proven wrong ...Moahahah! */
            $this->controller = 'App_Controller';
            $this->action = '404';

            if(file_exists($controllerFile)) {
                $this->controller = $controllerName;

                if(strlen($actionName) > 0 ) $this->action = $actionName;
                else $this->action = $this->defaultAction;

            } else {
                $urlmapper = new App_UrlMapper();
                $route = $urlmapper->getMappedRoute($this->path);

                if (count($route) > 0) {
                    $controllerFile = APP::$conf['path']['controllers'] . $route['controller'] . '.class.php';

                    if (file_exists($controllerFile)) {
                        $this->controller = $route['controller'];
                        $this->action = $route['action'];
                    }
                }
            }
        }
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }
}