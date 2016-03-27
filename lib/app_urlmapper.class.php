<?php

/**
 * Created by IntelliJ IDEA.
 * User: Aleksander Akerø
 * Date: 27.03.2016
 * Time: 05.43
 */
class App_UrlMapper {
    var $routes = array(
        array(
            'url' => '/^login/',
            'controller' => 'authorize',
            'action' => 'login'
        ),
        array(
            'url' => '/^logout/',
            'controller' => 'authorize',
            'action' => 'logout'
        )
    );

    public function getMappedRoute($url) {
        $mappedRoute = [];

        foreach($this->routes as $route) {
            if(preg_match($route['url'], $url)) {
                $mappedRoute = $route;
            }
        }

        return $mappedRoute;
    }
}